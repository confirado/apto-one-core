// Import Angular core and other necessary modules
import { Component, Input, OnInit } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { Store } from '@ngrx/store';
import { UntilDestroy } from '@ngneat/until-destroy';
import {combineLatest, Observable, of} from 'rxjs';
import { map, switchMap } from 'rxjs/operators';

// Import environment configuration and translation model
import { environment } from '@apto-frontend/src/environments/environment';
import { translate } from "@apto-base-core/store/translated-value/translated-value.model";

// Import selectors and actions related to content snippets, language, and material picker
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import {
  MaterialPickerFilterForm,
  MaterialPickerItem, Property,
  PropertyGroup
} from '@apto-catalog-frontend/models/material-picker';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { ItemsUpdatePayload } from "@apto-material-picker-element-frontend/store/material-picker/material-picker.model";
import {
  selectColors,
  selectItems, selectMultiPropertyGroups,
  selectPriceGroups, selectSinglePropertyGroups
} from "@apto-material-picker-element-frontend/store/material-picker/material-picker.selectors";
import {
  findPoolItems,
  initMaterialPicker
} from "@apto-material-picker-element-frontend/store/material-picker/material-picker.actions";

/**
 * Angular component for the Material Picker Element.
 * This component handles the selection of materials and related properties.
 */
@UntilDestroy()
@Component({
	selector: 'apto-material-picker-element',
	templateUrl: './material-picker-element.component.html',
	styleUrls: ['./material-picker-element.component.scss'],
})
export class MaterialPickerElementComponent implements OnInit {
  /**
   * Input property representing the element being configured.
   */
  @Input()
  public element: ProgressElement<any>;

  /**
   * Input property representing the section the element belongs to.
   */
  @Input()
  public section: Section | undefined;

  /**
   * Input property representing the product being configured.
   */
	@Input()
	public product: Product | undefined;

  /**
   * URL for media resources.
   */
  public mediaUrl = environment.api.media + '/';

  /**
   * Default locale for translations.
   */
  public locale: string = environment.defaultLocale;

  /**
   * Current step in the configuration process.
   */
  public step: number = 1;

  /**
   * Observable emitting the content snippet for the material picker.
   */
	public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.materialPickerElement'));

  /**
   * Observable emitting the content snippet for the default element definition.
   */
	public readonly contentSnippetButton$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

  /**
   * Observable emitting items available for selection.
   */
  public readonly items$ = this.store.select(selectItems);

  /**
   * Observable emitting available colors.
   */
  public readonly colors$ = this.store.select(selectColors);

  /**
   * Observable emitting available price groups.
   */
  public readonly priceGroups$ = this.store.select(selectPriceGroups);

  /**
   * Observable emitting single property groups.
   */
  public readonly singlePropertyGroups$ = this.store.select(selectSinglePropertyGroups);

  /**
   * Observable emitting multiple property groups.
   */
  public readonly multiplePropertyGroups$ = this.store.select(selectMultiPropertyGroups);

  /**
   * Holds the currently selected primary item.
   */
  public currentItem: { id: string; name: string; priceGroup: string } | undefined;

  /**
   * Holds an array of currently selected materials.
   */
	public currentMaterials: { id: string; name: string; priceGroup: string }[] = [];

  /**
   * Holds the currently selected secondary item.
   */
  public currentSecondaryItem: { id: string; name: string; priceGroup: string } | undefined;

  /**
   * Holds an array of currently selected secondary materials.
   */
  public currentSecondaryMaterials: { id: string; name: string; priceGroup: string }[] = [];

  /**
   * Form control for the primary selection.
   */
  public formElement = new FormControl<{ id: string; name: string; priceGroup: string }[]>([]);

  /**
   * Form control for the secondary selection.
   */
  public secondaryFormElement = new FormControl<{ id: string; name: string; priceGroup: string }[]>([]);

  /**
   * Form control for enabling multi-color selection.
   */
	public multiColor = new FormControl<boolean>(false);

  /**
   * Form control for specifying the color order.
   */
	public colorOrder = new FormControl<boolean>(false);

  /**
   * Form control for specifying the input count.
   */
	public inputCount = new FormControl<number>(0);

  /**
   * Form group for filtering materials.
   */
  public filter = new FormGroup<MaterialPickerFilterForm>({
		colorRating: new FormControl<string | null>(null),
		priceGroup: new FormControl<string | null>(null),
		properties: new FormGroup<any>({}),
		searchString: new FormControl<string>(''),
	});

  /**
   * Constructor to inject the store dependency.
   */
	public constructor(private store: Store) {}

  /**
   * Initializes the component on initialization.
   */
  public ngOnInit(): void {
    // Dispatch the initialization action for the material picker
    this.store.dispatch(initMaterialPicker({
      payload: this.getPayload()
    }));

    // Subscribe to changes in the selected locale
    this.store.select(selectLocale).subscribe((locale) => {
      if (locale !== null) {
        this.locale = locale;
      }
    });

    // Initialize property group form controls
    combineLatest([this.singlePropertyGroups$, this.multiplePropertyGroups$]).subscribe(([singlePropertyGroups, multiplePropertyGroups]) => {
      const addPropertyFormControl = (propertyGroup: PropertyGroup) => {
        // Add a form control for each property group to the properties form group
        this.filter.controls.properties.addControl(
          propertyGroup.id,
          new FormControl<string[]>([]),
          { emitEvent: false }
        );
      };

      // Add form controls for single property groups
      singlePropertyGroups?.forEach(addPropertyFormControl);
      // Add form controls for multiple property groups
      multiplePropertyGroups?.forEach(addPropertyFormControl);
    });

    // Set values for material color mixing, color arrangement, and quantity based on configuration state
    if (this.element.state.values.materialColorMixing === 'multicolored') {
      this.multiColor.setValue(true);
    }
    if (this.element.state.values.materialColorArrangement === 'input') {
      this.colorOrder.setValue(true);
    }
    if (this.element.state.values.materialColorQuantity) {
      // Parse and set the input count value
      this.inputCount.setValue(parseInt(this.element.state.values.materialColorQuantity, 10));
    }

    // Initialize single selection if allowMultiple flag is false
    if (!this.element.element.definition.staticValues.allowMultiple) {
      this.initSingleSelection();
    }

    // Initialize multiple selection if allowMultiple flag is true
    if (this.element.element.definition.staticValues.allowMultiple) {
      this.initMultiSelection();
    }

    // Apply filter when filter values change
    this.filter.valueChanges.subscribe((next) => {
      this.applyFilter();
    });
  }

  /**
   * Method to navigate to the next step in the material picker.
   */
  public nextStep() {
    this.step = 2;
  }

  /**
   * Initializes single selection by setting the current item and secondary item based on configuration state.
   */
  private initSingleSelection() {
    if (this.element.state.values.materialId) {
      // Set the current item based on configuration state
      this.currentItem = {
        id: this.element.state.values.materialId,
        name: this.element.state.values.materialName,
        priceGroup: this.element.state.values.priceGroup,
      };
      // Set the value of the form element to the current item
      this.formElement.setValue([this.currentItem]);
    }

    if (this.element.state.values.materialIdSecondary) {
      // Set the secondary item based on configuration state
      this.currentSecondaryItem = {
        id: this.element.state.values.materialIdSecondary,
        name: this.element.state.values.materialNameSecondary,
        priceGroup: this.element.state.values.priceGroupSecondary,
      };
      // Set the value of the secondary form element to the secondary item
      this.secondaryFormElement.setValue([this.currentSecondaryItem]);
    }
  }

  /**
   * Initializes the multi-selection of materials.
   * It populates the currentMaterials and currentSecondaryMaterials arrays
   * based on the materials stored in the element state.
   * It then sets the values of the form controls accordingly.
   */
  private initMultiSelection() {
    // Check if materials are available in the element state
    if (this.element.state.values.materials) {
      // Iterate through each material and push it into the currentMaterials array
      for (const item of this.element.state.values.materials) {
        this.currentMaterials.push(item);
      }
    }

    // Initialize currentSecondaryMaterials as an empty array
    this.currentSecondaryMaterials = [];

    // Check if secondary materials are available in the element state
    if (this.element.state.values.materialsSecondary) {
      // Iterate through each secondary material and push it into the currentSecondaryMaterials array
      for (const item of this.element.state.values.materialsSecondary) {
        this.currentSecondaryMaterials.push(item);
      }
    }

    // Set the value of the secondary form control with the currentSecondaryMaterials array
    this.secondaryFormElement.setValue(this.currentSecondaryMaterials);

    // Set the value of the main form control with the currentMaterials array
    this.formElement.setValue(this.currentMaterials);
  }

  /**
   * Handles color selection.
   * @param hex The hexadecimal color value selected.
   */
  public onSelectColor(hex: string | null): void {
    // Toggles the selected color value in the colorRating form control
    this.filter.controls.colorRating.setValue(this.filter.controls.colorRating.value === hex ? null : hex);
  }

  /**
   * Handles the selection of a material element in the Material Picker.
   * @param item The selected MaterialPickerItem.
   */
  public onSelectElement(item: MaterialPickerItem): void {
    // Check if the element is defined
    if (!this.element) {
      return;
    }

    // Create a localized representation of the selected item
    const localeItem: any = {
      id: item.material.id,
      name: translate(item.material.name, this.locale),
      priceGroup: translate(item.priceGroup.name, this.locale)
    };

    // Determine the current step and corresponding variables
    let currentItem = this.step === 2 ? this.currentSecondaryItem : this.currentItem;
    let currentMaterials = this.step === 2 ? this.currentSecondaryMaterials : this.currentMaterials;
    let formElement = this.step === 2 ? this.secondaryFormElement : this.formElement;

    // Handle selection logic for a single material (not allowing multiple)
    if (!this.element.element.definition.staticValues.allowMultiple) {
      if (currentItem && currentItem.id === localeItem.id) {
        // If the current item is the same as the selected one, clear it
        currentItem = undefined;
        formElement.setValue([]);
      } else {
        // Set the selected item as the current item
        currentItem = localeItem;
        formElement.setValue([currentItem]);
      }
    } else {
      // Handle selection logic for multiple materials
      const cutItem = currentMaterials.find((i: any) => i.id === localeItem.id);
      if (cutItem) {
        // If the selected item already exists, remove it from the list
        currentMaterials.splice(currentMaterials.indexOf(cutItem), 1);
      } else if (localeItem) {
        // Add the selected item to the list
        currentMaterials.push({
          id: localeItem.id,
          name: localeItem.name,
          priceGroup: localeItem.priceGroup,
        });
      }

      formElement.setValue(currentMaterials);
    }

    // Update current item and materials based on the step
    if (this.step === 1) {
      this.currentItem = currentItem;
      this.currentMaterials = currentMaterials;
    } else if (this.step === 2) {
      this.currentSecondaryItem = currentItem;
      this.currentSecondaryMaterials = currentMaterials;
    }

    // Handle removal or saving based on configuration state
    if (!this.element.element.definition.staticValues.secondaryMaterialActive) {
      if (this.currentMaterials.length === 0 && this.element.element.definition.staticValues.allowMultiple ||
        this.currentItem === undefined && !this.element.element.definition.staticValues.allowMultiple) {
        this.removeInput();
      } else {
        this.saveInput();
      }
    }
  }

  /**
   * Handles selection of multiple properties.
   * @param group The property group to which the selected property belongs.
   * @param property The property being selected.
   */
  public onMultiplePropertySelected(group: PropertyGroup, property: Property) {
    // Check if the property group exists in the form controls
    if (!this.filter.controls.properties.controls.hasOwnProperty(group.id)) {
      return; // Exit the function if the property group does not exist
    }

    // Retrieve the index of the selected property in the property group's value array
    const currentIndex = this.filter.controls.properties.controls[group.id].value.indexOf(property.id);

    // Retrieve the current value of the property group
    const value = this.filter.controls.properties.controls[group.id].value;

    // Toggle the selection of the property based on its current state
    if (currentIndex === -1) {
      // If the property is not already selected, add it to the array
      value.push(property.id);
    } else {
      // If the property is already selected, remove it from the array
      value.splice(currentIndex, 1);
    }

    // Update the value of the property group with the modified array
    this.filter.controls.properties.controls[group.id].setValue([...value]);
  }

  /**
   * Checks if an element with the given ID is selected.
   * @param id The ID of the element to check.
   * @returns A boolean indicating whether the element is selected.
   */
  public isElementSelected(id: string): boolean {
    // Define variables to hold the current item and materials based on the step
    let currentItem = this.currentItem;
    let currentMaterials = this.currentMaterials;

    // Update variables if step is 2
    if (this.step === 2) {
      currentItem = this.currentSecondaryItem;
      currentMaterials = this.currentSecondaryMaterials;
    }

    // Check if the element supports multiple selection
    if (this.elementState('multiple')) {
      // If it does, check if any of the current materials has the given ID
      return currentMaterials.some((i) => i.id === id);
    }

    // If the element does not support multiple selection
    if (!this.elementState('multiple')) {
      // Check if there is a current item and if its ID matches the given ID
      if (currentItem) {
        return currentItem.id === id;
      }
    }

    // If none of the conditions above are met, return false
    return false;
  }

  /**
   * Checks if the form elements have valid values.
   * @returns A boolean indicating whether the form elements have valid values.
   */
  public asserValidValues(): boolean {
    // Check if both formElement and secondaryFormElement have values
    if (this.formElement.value && this.secondaryFormElement.value) {
      // If multiColor is true, check if both formElement and secondaryFormElement have non-empty values
      if (this.multiColor.value) {
        return this.formElement.value.length !== 0 && this.secondaryFormElement.value.length !== 0;
      }
      // If multiColor is false, only check if formElement has a non-empty value
      return this.formElement.value.length !== 0;
    }
    // Return false if either formElement or secondaryFormElement is null or undefined
    return false;
  }

  /**
   * Applies the current filter settings by dispatching an action to find pool items.
   */
  private applyFilter() {
    // Dispatches an action to find pool items with the payload obtained from getPayload() method
    this.store.dispatch(findPoolItems({
      payload: this.getPayload()
    }));
  }

  /**
   * Constructs the payload for updating items.
   * @returns An object representing the payload for updating items.
   */
  private getPayload(): ItemsUpdatePayload {
    let payload = {
      // Extract the pool ID from the element definition's static values
      poolId: this.element.element.definition.staticValues.poolId,
      // Construct the filter object
      filter: {
        // Get the search string from the filter form control
        searchString: this.filter.controls.searchString.value,
        // Get the color rating from the filter form control
        colorRating: this.filter.controls.colorRating.value,
        // Get the price group from the filter form control
        priceGroup: this.filter.controls.priceGroup.value,
        // Initialize an empty array for properties
        properties: [],
        // Set the order by to ascending
        orderBy: 'asc',
      }
    };

    // Iterate over each entry in the properties form control value
    Object.entries<string[]>(this.filter.controls.properties.value).forEach(([key, values]) => {
      // Concatenate the values with the existing properties array
      payload.filter.properties = payload.filter.properties.concat(values);
    });

    return payload;
  }

  /**
   * Saves the input configuration state.
   */
  public saveInput(): void {
    // Check if the element is defined
    if (!this.element) {
      return;
    }

    // Determine material color mixing
    let materialColorMixing = 'monochrome';
    if (this.multiColor.value) {
      materialColorMixing = 'multicolored';
    }

    // Determine material color arrangement
    let materialColorOrder = 'alternately';
    if (this.colorOrder.value) {
      materialColorOrder = 'input';
    }

    // Parse input count to string
    let inputCountString = '';
    if (this.inputCount.value && this.inputCount.value > 0) {
      inputCountString = this.inputCount.value.toString();
    }

    // Dispatch update configuration state action based on whether multiple materials are allowed
    if (this.element.element.definition.staticValues.allowMultiple) {
      this.store.dispatch(
        updateConfigurationState({
          updates: {
            set: [
              // Update configuration state for multiple materials
              this.getStateUpdateObject('aptoElementDefinitionId', 'apto-element-material-picker'),
              this.getStateUpdateObject('poolId', this.element.element.definition.staticValues.poolId),
              this.getStateUpdateObject('productId', this.product?.id),
              this.getStateUpdateObject('materialId', ''),
              this.getStateUpdateObject('materialName', ''),
              this.getStateUpdateObject('priceGroup', ''),
              this.getStateUpdateObject('materials', [...this.currentMaterials]),
              this.getStateUpdateObject('materialIdSecondary', ''),
              this.getStateUpdateObject('materialNameSecondary', ''),
              this.getStateUpdateObject('priceGroupSecondary', ''),
              this.getStateUpdateObject('materialsSecondary', [...this.currentSecondaryMaterials]),
              this.getStateUpdateObject('materialColorMixing', materialColorMixing),
              this.getStateUpdateObject('materialColorArrangement', materialColorOrder),
              this.getStateUpdateObject('materialColorQuantity', inputCountString)
            ],
          },
        })
      );
    } else if (this.currentItem) {
      // Define secondary item if not already defined
      let secondaryItem = this.currentSecondaryItem;
      if (!secondaryItem) {
        secondaryItem = {
          id: '',
          name: '',
          priceGroup: ''
        };
      }

      // Dispatch update configuration state action for single material selection
      this.store.dispatch(
        updateConfigurationState({
          updates: {
            set: [
              this.getStateUpdateObject('aptoElementDefinitionId', 'apto-element-material-picker'),
              this.getStateUpdateObject('poolId', this.element.element.definition.staticValues.poolId),
              this.getStateUpdateObject('productId', this.product?.id),
              this.getStateUpdateObject('materialId', this.currentItem.id),
              this.getStateUpdateObject('materialName', this.currentItem.name),
              this.getStateUpdateObject('priceGroup', this.currentItem.priceGroup),
              this.getStateUpdateObject('materialIdSecondary', secondaryItem.id),
              this.getStateUpdateObject('materialNameSecondary', secondaryItem.name),
              this.getStateUpdateObject('priceGroupSecondary', secondaryItem.priceGroup),
              this.getStateUpdateObject('materialsSecondary', []),
              this.getStateUpdateObject('materialColorMixing', materialColorMixing),
              this.getStateUpdateObject('materialColorArrangement', materialColorOrder),
              this.getStateUpdateObject('materialColorQuantity', inputCountString)
            ],
          },
        })
      );
    }
  }

  /**
   * Removes the input configuration.
   */
  public removeInput(): void {
    if (!this.element) {
      return;
    }

    // Reset step to 1
    this.step = 1;

    // Dispatch action to remove input configuration state
    this.store.dispatch(
      updateConfigurationState({
        updates: {
          remove: [
            // Get the state update object to remove the input configuration
            this.getStateUpdateObject()
          ],
        },
      })
    );
  }

  /**
   * Creates and returns an object containing state update properties.
   * @param property The property to update.
   * @param value The new value for the property.
   * @returns An object containing the state update properties.
   */
  private getStateUpdateObject(property: string = null, value: any = null) {
    return {
      sectionId: this.element.element.sectionId,
      elementId: this.element.element.id,
      sectionRepetition: this.element.state.sectionRepetition,
      property: property,
      value: value,
    };
  }

  /**
   * Determines the state of the specified element type.
   * @param type The type of element state to check.
   * @returns A boolean value indicating the state of the specified element type.
   */
  public elementState(type: string): boolean {
    if (!this.element) {
      return false;
    }

    if (type === 'search-box') {
      return this.element.element.definition.staticValues.searchboxActive;
    }
    if (type === 'multiple') {
      return this.element.element.definition.staticValues.allowMultiple;
    }
    if (type === 'active') {
      return this.element.state.active;
    }
    if (type === 'second-material') {
      return this.element.element.definition.staticValues.secondaryMaterialActive;
    }
    return false;
  }

  /**
   * Resets the search field by clearing its value.
   */
  public resetSearchField(): void {
    this.filter.controls.searchString.setValue('');
  }

  /**
   * Retrieves the default price group.
   * @returns An Observable emitting a string representing the default price group.
   */
  public getDefaultPriceGroup(): Observable<string> {
    return this.priceGroups$.pipe(
      switchMap(priceGroups => {
        // Check if priceGroups array exists and contains exactly one element
        if (priceGroups && priceGroups.length === 1) {
          // If only one price group is available, return its name in German
          return of(priceGroups[0].name['de_DE'].toString());
        } else {
          // If multiple price groups exist or none is available, retrieve the default content snippet
          return this.store.select(selectContentSnippet('plugins.materialPickerElement.allProperty')).pipe(
            map(contentSnippet => {
              // Extract and return the content snippet's value in German
              return contentSnippet.content['de_DE'].toString();
            })
          );
        }
      })
    );
  }
}
