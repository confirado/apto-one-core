import { Component, Input, OnInit } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { Store } from '@ngrx/store';
import { UntilDestroy } from '@ngneat/until-destroy';
import { combineLatest } from 'rxjs';

import { environment } from '@apto-frontend/src/environments/environment';
import { translate } from "@apto-base-core/store/translated-value/translated-value.model";
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import {
  MaterialPickerFilterForm,
  MaterialPickerItem, Property,
  PropertyGroup
} from '@apto-catalog-frontend/models/material-picker';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
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

@UntilDestroy()
@Component({
	selector: 'apto-material-picker-element',
	templateUrl: './material-picker-element.component.html',
	styleUrls: ['./material-picker-element.component.scss'],
})
export class MaterialPickerElementComponent implements OnInit {
  @Input()
  public element: ProgressElement<any>;

	@Input()
	public product: Product | undefined;

  public mediaUrl = environment.api.media + '/';
  public locale: string = environment.defaultLocale;
  public step: number = 1;

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.materialPickerElement'));
	public readonly contentSnippetButton$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));
  public readonly items$ = this.store.select(selectItems);
  public readonly colors$ = this.store.select(selectColors);
  public readonly priceGroups$ = this.store.select(selectPriceGroups);
  public readonly singlePropertyGroups$ = this.store.select(selectSinglePropertyGroups);
  public readonly multiplePropertyGroups$ = this.store.select(selectMultiPropertyGroups);

  public currentItem: { id: string; name: string; priceGroup: string } | undefined;
	public currentMaterials: { id: string; name: string; priceGroup: string }[] = [];
  public currentSecondaryItem: { id: string; name: string; priceGroup: string } | undefined;
  public currentSecondaryMaterials: { id: string; name: string; priceGroup: string }[] = [];

  public formElement = new FormControl<{ id: string; name: string; priceGroup: string }[]>([]);
  public secondaryFormElement = new FormControl<{ id: string; name: string; priceGroup: string }[]>([]);
	public multiColor = new FormControl<boolean>(false);
	public colorOrder = new FormControl<boolean>(false);
	public inputCount = new FormControl<number>(0);

	public filter = new FormGroup<MaterialPickerFilterForm>({
		colorRating: new FormControl<string | null>(null),
		priceGroup: new FormControl<string | null>(null),
		properties: new FormGroup<any>({}),
		searchString: new FormControl<string>(''),
	});

	public constructor(private store: Store) {}

  public ngOnInit(): void {
    this.store.dispatch(initMaterialPicker({
      payload: this.getPayload()
    }));

    this.store.select(selectLocale).subscribe((locale) => {
      if (locale !== null) {
        this.locale = locale;
      }
    });

    // init property group form controls
    combineLatest([this.singlePropertyGroups$, this.multiplePropertyGroups$]).subscribe(([singlePropertyGroups, multiplePropertyGroups]) => {
      const addPropertyFormControl = (propertyGroup: PropertyGroup) => {
        this.filter.controls.properties.addControl(
          propertyGroup.id,
          new FormControl<string[]>([]),
          { emitEvent: false }
        )
      };

      singlePropertyGroups?.forEach(addPropertyFormControl);
      multiplePropertyGroups?.forEach(addPropertyFormControl);
    });

    if (this.element.state.values.materialColorMixing === 'multicolored') {
      this.multiColor.setValue(true);
    }
    if (this.element.state.values.materialColorArrangement === 'input') {
      this.colorOrder.setValue(true);
    }
    if (this.element.state.values.materialColorQuantity) {
      this.inputCount.setValue(parseInt(this.element.state.values.materialColorQuantity, 10));
    }

    // init single
    if (!this.element.element.definition.staticValues.allowMultiple) {
      this.initSingleSelection();
    }

    // init multiple
    if (this.element.element.definition.staticValues.allowMultiple) {
      this.initMultiSelection();
    }

    // apply filter on filter values change
    this.filter.valueChanges.subscribe((next) => {
      this.applyFilter();
    });
  }

  public nextStep() {
    this.step = 2;
  }

  private initSingleSelection() {
    if (this.element.state.values.materialId) {
      this.currentItem = {
        id: this.element.state.values.materialId,
        name: this.element.state.values.materialName,
        priceGroup: this.element.state.values.priceGroup,
      };
      this.formElement.setValue([this.currentItem]);
    }

    if (this.element.state.values.materialIdSecondary) {
      this.currentSecondaryItem = {
        id: this.element.state.values.materialIdSecondary,
        name: this.element.state.values.materialNameSecondary,
        priceGroup: this.element.state.values.priceGroupSecondary,
      };
      this.secondaryFormElement.setValue([this.currentSecondaryItem]);
    }
  }

  private initMultiSelection() {
    if (this.element.state.values.materials) {
      for (const item of this.element.state.values.materials) {
        this.currentMaterials.push(item);
      }
    }

    this.currentSecondaryMaterials = [];
    if (this.element.state.values.materialsSecondary) {
      for (const item of this.element.state.values.materialsSecondary) {
        this.currentSecondaryMaterials.push(item);
      }
    }
    this.secondaryFormElement.setValue(this.currentSecondaryMaterials);
    this.formElement.setValue(this.currentMaterials);
  }

	public onSelectColor(hex: string | null): void {
		this.filter.controls.colorRating.setValue(this.filter.controls.colorRating.value === hex ? null : hex);
	}

	public onSelectElement(item: MaterialPickerItem): void {
		if (!this.element) {
			return;
		}

    const localeItem = {
      id: item.material.id,
      name: translate(item.material.name, this.locale),
      priceGroup: translate(item.priceGroup.name, this.locale)
    };

    let currentItem = this.currentItem;
    let currentMaterials = this.currentMaterials;
    let formElement = this.formElement;
    if (this.step === 2) {
      currentItem = this.currentSecondaryItem;
      currentMaterials = this.currentSecondaryMaterials;
      formElement = this.secondaryFormElement;
    }

		if (!this.element.element.definition.staticValues.allowMultiple) {
			if (currentItem && currentItem.id === localeItem.id) {
        // remove current item if selected item is clicked
				currentItem = undefined;
        formElement.setValue([]);
			} else {
        // set new current item
				currentItem = localeItem;
        formElement.setValue([currentItem]);
			}
		} else {
			const cutItem = currentMaterials.find((i) => i.id === localeItem.id);
			if (cutItem) {
        // remove current item if selected item is clicked
				currentMaterials.splice(currentMaterials.indexOf(cutItem), 1);
			} else if (localeItem) {
				currentMaterials.push({
					id: localeItem.id,
					name: localeItem.name,
					priceGroup: localeItem.priceGroup,
				});
			}
      formElement.setValue(currentMaterials);
		}

    if (this.step === 1) {
      this.currentItem = currentItem;
      this.currentMaterials = currentMaterials;
    }

    if (this.step === 2) {
      this.currentSecondaryItem = currentItem;
      this.currentSecondaryMaterials = currentMaterials;
    }
	}

  public onMultiplePropertySelected(group: PropertyGroup, property: Property) {
    if (!this.filter.controls.properties.controls.hasOwnProperty(group.id)) {
      return;
    }

    const currentIndex = this.filter.controls.properties.controls[group.id].value.indexOf(property.id);
    const value = this.filter.controls.properties.controls[group.id].value;
    if (currentIndex === -1) {
      value.push(property.id);
    } else {
      value.splice(currentIndex, 1);
    }

    this.filter.controls.properties.controls[group.id].setValue([...value]);
  }

  public isElementSelected(id: string): boolean {
    let currentItem = this.currentItem;
    let currentMaterials = this.currentMaterials;
    if (this.step === 2) {
      currentItem = this.currentSecondaryItem;
      currentMaterials = this.currentSecondaryMaterials;
    }

    if (this.elementState('multiple')) {
      return currentMaterials.some((i) => i.id === id);
    }
    if (!this.elementState('multiple')) {
      if (currentItem) {
        return currentItem.id === id;
      }
    }
    return false;
  }

  public asserValidValues(): boolean {
    if (this.formElement.value && this.secondaryFormElement.value) {
      if (this.multiColor.value) {
        return this.formElement.value.length !== 0 && this.secondaryFormElement.value.length !== 0;
      }
      return this.formElement.value.length !== 0;
    }
    return false;
  }

  private applyFilter() {
    this.store.dispatch(findPoolItems({
      payload: this.getPayload()
    }));
  }

  private getPayload(): ItemsUpdatePayload {
    let payload = {
      poolId: this.element.element.definition.staticValues.poolId,
      filter: {
        searchString: this.filter.controls.searchString.value,
        colorRating: this.filter.controls.colorRating.value,
        priceGroup: this.filter.controls.priceGroup.value,
        properties: [],
        orderBy: 'asc',
      }
    }

    Object.entries<string[]>(this.filter.controls.properties.value).forEach(([key, values]) => {
      payload.filter.properties = payload.filter.properties.concat(values);
    });

    return payload;
  }

	public saveInput(): void {
		if (!this.element) {
			return;
		}

		let materialColorMixing = 'monochrome';
		if (this.multiColor.value) {
			materialColorMixing = 'multicolored';
		}
		let materialColorOrder = 'alternately';
		if (this.colorOrder.value) {
			materialColorOrder = 'input';
		}
		let inputCountString = '';
		if (this.inputCount.value && this.inputCount.value > 0) {
			inputCountString = this.inputCount.value.toString();
		}

		if (this.element.element.definition.staticValues.allowMultiple) {
			this.store.dispatch(
				updateConfigurationState({
					updates: {
						set: [
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
      let secondaryItem = this.currentSecondaryItem;
      if (!secondaryItem) {
        secondaryItem = {
          id: '',
          name: '',
          priceGroup: ''
        };
      }

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

	public removeInput(): void {
		if (!this.element) {
			return;
		}

    this.step = 1;
		this.store.dispatch(
			updateConfigurationState({
				updates: {
					remove: [
            this.getStateUpdateObject()
					],
				},
			})
		);
	}

  private getStateUpdateObject(property: string = null, value: any = null) {
    return {
      sectionId: this.element.element.sectionId,
      elementId: this.element.element.id,
      sectionRepetition: this.element.state.sectionRepetition,
      property: property,
      value: value,
    }
  }

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
}
