import { Component, Input, OnInit } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { MaterialPickerFilterForm, MaterialPickerItem, PropertyGroup } from '@apto-catalog-frontend/models/material-picker';
import { CatalogMessageBusService } from '@apto-catalog-frontend/services/catalog-message-bus.service';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { environment } from '@apto-frontend/src/environments/environment';
import { UntilDestroy } from '@ngneat/until-destroy';
import { Store } from '@ngrx/store';
import { BehaviorSubject, combineLatest, filter, map, startWith, switchMap } from 'rxjs';

@UntilDestroy()
@Component({
	selector: 'apto-material-picker-element',
	templateUrl: './material-picker-element.component.html',
	styleUrls: ['./material-picker-element.component.scss'],
})
export class MaterialPickerElementComponent implements OnInit {
	public mediaUrl = environment.api.media + '/';

	@Input()
	public set element(element: ProgressElement<any>) {
		this.element$.next(element);
		if (element && !element.element.definition.staticValues.allowMultiple && element.state.active) {
			this.currentItem = {
				id: element.state.values.materialId,
				name: element.state.values.materialName,
				priceGroup: element.state.values.priceGroup,
			};
			this.formElement.setValue([this.currentItem]);

			const currentSecondItem = {
				id: element.state.values.materialIdSecondary,
				name: element.state.values.materialNameSecondary,
				priceGroup: element.state.values.priceGroupSecondary,
			};
			this.secondaryFormElement.setValue([currentSecondItem]);
		} else if (element && element.state.active) {
			if (element.state.values.materials) {
				for (const item of element.state.values.materials) {
					this.currentMaterials.push(item);
				}
			}

			const currentSecondMaterials: { id: string; name: string; priceGroup: string }[] = [];

			if (element && element.state.values.materialsSecondary) {
				for (const item of element.state.values.materialsSecondary) {
					currentSecondMaterials.push(item);
				}
			}
			this.secondaryFormElement.setValue(currentSecondMaterials);
			this.formElement.setValue(this.currentMaterials);
		}
	}

	@Input()
	public product: Product | undefined;

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.materialPickerElement'));

	public readonly contentSnippetButton$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

	public element$ = new BehaviorSubject<ProgressElement<any> | undefined>(undefined);

	public typedElement$ = this.element$.pipe(filter((element): element is ProgressElement<any> => element !== undefined));

	public formElement = new FormControl<{ id: string; name: string; priceGroup: string }[]>([]);

	public secondaryFormElement = new FormControl<{ id: string; name: string; priceGroup: string }[]>([]);

	public currentMaterials: { id: string; name: string; priceGroup: string }[] = [];

	public currentItem: { id: string; name: string; priceGroup: string } | undefined;

	public materialCount: number = 0;

	public multiColor = new FormControl<boolean>(false);

	public colorOrder = new FormControl<boolean>(false);

	public inputCount = new FormControl<number>(0);

	public filter = new FormGroup<MaterialPickerFilterForm>({
		colorRating: new FormControl<string | null>(null),
		priceGroup: new FormControl<string | null>(null),
		properties: new FormGroup<any>({}),
		searchString: new FormControl<string>(''),
	});

	public elementState(type: string): boolean {
		const element = this.element$.value;
		if (!element) {
			return false;
		}
		if (type === 'search-box') {
			return element.element.definition.staticValues.searchboxActive;
		}
		if (type === 'multiple') {
			return element.element.definition.staticValues.allowMultiple;
		}
		if (type === 'active') {
			return element.state.active;
		}
		if (type === 'second-material') {
			return element.element.definition.staticValues.secondaryMaterialActive;
		}
		return false;
	}

	public isSelected(id: string): boolean {
		if (this.elementState('multiple')) {
			return this.currentMaterials.some((i) => i.id === id);
		}
		if (!this.elementState('multiple')) {
			if (this.currentItem) {
				return this.currentItem.id === id;
			}
		}
		return false;
	}

	public currentValues(): boolean {
		if (this.formElement.value && this.secondaryFormElement.value) {
			if (this.multiColor.value) {
				return this.formElement.value.length !== 0 && this.secondaryFormElement.value.length !== 0;
			}
			return this.formElement.value.length !== 0;
		}
		return false;
	}

	public usedValues$ = this.filter.valueChanges.pipe(
		startWith(this.filter.value),
		map((value) => {
			let finalProperties: string[] = [];

			if (value.properties.length !== 0) {
				Object.entries<string[]>(value.properties).forEach(([key, values]) => {
					finalProperties = finalProperties.concat(values);
				});
			}
			value.properties = finalProperties;
			return value as any;
		})
	);

	public colors$ = combineLatest([this.typedElement$, this.usedValues$]).pipe(
		switchMap(([element, value]) =>
			this.catalogMessageBusService.findMaterialPickerPoolColors(element.element.definition.staticValues.poolId, value)
		),
		map((items) => Object.values(items))
	);

	public items$ = combineLatest([this.typedElement$, this.usedValues$]).pipe(
		switchMap(([element, value]) =>
			this.catalogMessageBusService.findMaterialPickerPoolItemsFiltered(element.element.definition.staticValues.poolId, value as any)
		),
		map((items) => {
			this.materialCount = items.data.length;
			return items.data;
		})
	);

	public localeItems$ = combineLatest([this.items$, this.store.select(selectLocale)]).pipe(
		map(([items, locale]) => {
			if (!locale) {
				return [];
			}

			const localeItems: { id: string; name: string; priceGroup: string }[] = [];
			items.forEach((item) =>
				localeItems.push({ id: item.material.id, name: item.material.name[locale], priceGroup: item.priceGroup.name[locale] })
			);
			return localeItems;
		})
	);

	public priceGroups$ = this.typedElement$.pipe(
		switchMap((element) => this.catalogMessageBusService.findMaterialPickerPoolPriceGroups(element.element.definition.staticValues.poolId))
	);

	public propertyGroups$ = this.typedElement$.pipe(
		switchMap((element) =>
			this.catalogMessageBusService.findMaterialPickerPoolPropertyGroups(element.element.definition.staticValues.poolId)
		)
	);

	public propertyGroupList: PropertyGroup[] = [];

	public constructor(
    private catalogMessageBusService: CatalogMessageBusService,
    private store: Store
  ) {}

	public selectColor(hex: string | null): void {
		this.filter.controls.colorRating.setValue(this.filter.controls.colorRating.value === hex ? null : hex);
	}

	public ngOnInit(): void {
		this.propertyGroups$.subscribe((propertyGroups) => {
			propertyGroups?.forEach((propertyGroup) =>
				this.filter.controls.properties.addControl(propertyGroup.id, new FormControl<string[]>([]))
			);
			this.propertyGroupList = propertyGroups;
		});

		const element = this.element$.value;
		if (!element) {
			return;
		}
		if (element.state.values.materialColorMixing === 'multicolored') {
			this.multiColor.setValue(true);
		}
		if (element.state.values.materialColorArrangement === 'input') {
			this.colorOrder.setValue(true);
		}
		if (element.state.values.materialColorQuantity) {
			this.inputCount.setValue(parseInt(element.state.values.materialColorQuantity, 10));
		}
	}

	public selectInput(item: MaterialPickerItem, localeItems: { id: string; name: string; priceGroup: string }[]): void {
		const element = this.element$.value;
		if (!element) {
			return;
		}
		const localeItem = localeItems.find((i) => i.id === item.material.id);
		if (!element.element.definition.staticValues.allowMultiple && localeItem) {
			if (this.currentItem && this.currentItem.id === localeItem.id) {
				this.currentItem = undefined;
				this.formElement.setValue([]);
			} else {
				this.currentItem = localeItem;
				if (this.currentItem) {
					this.formElement.setValue([this.currentItem]);
				}
			}
		} else {
			const cutItem = this.currentMaterials.find((i) => i.id === item.material.id);
			if (cutItem) {
				this.currentMaterials.splice(this.currentMaterials.indexOf(cutItem), 1);
			} else if (localeItem) {
				this.currentMaterials.push({
					id: item.material.id,
					name: localeItem.name,
					priceGroup: localeItem.priceGroup,
				});
			}
			this.formElement.setValue(this.currentMaterials);
		}
	}

	public saveInput(): void {
		const element = this.element$.value;
		if (!element) {
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

		if (element.element.definition.staticValues.allowMultiple) {
			this.store.dispatch(
				updateConfigurationState({
					updates: {
						set: [
              this.getStateUpdateObject(element, 'aptoElementDefinitionId', 'apto-element-material-picker'),
              this.getStateUpdateObject(element, 'poolId', element.element.definition.staticValues.poolId),
              this.getStateUpdateObject(element, 'productId', this.product?.id),
              this.getStateUpdateObject(element, 'materialId', ''),
              this.getStateUpdateObject(element, 'materialName', ''),
              this.getStateUpdateObject(element, 'priceGroup', ''),
              this.getStateUpdateObject(element, 'materials', this.currentMaterials),
              this.getStateUpdateObject(element, 'materialIdSecondary', ''),
              this.getStateUpdateObject(element, 'materialNameSecondary', ''),
              this.getStateUpdateObject(element, 'priceGroupSecondary', ''),
              this.getStateUpdateObject(element, 'materialsSecondary', this.secondaryFormElement.value),
              this.getStateUpdateObject(element, 'materialColorMixing', materialColorMixing),
              this.getStateUpdateObject(element, 'materialColorArrangement', materialColorOrder),
              this.getStateUpdateObject(element, 'materialColorQuantity', inputCountString)
						],
					},
				})
			);
		} else if (this.currentItem) {
			let secondItem = { id: '', name: '', priceGroup: '' };
			if (this.secondaryFormElement.value && this.secondaryFormElement.value.length !== 0) {
				[secondItem] = this.secondaryFormElement.value;
			}
			this.store.dispatch(
				updateConfigurationState({
					updates: {
						set: [
              this.getStateUpdateObject(element, 'aptoElementDefinitionId', 'apto-element-material-picker'),
              this.getStateUpdateObject(element, 'poolId', element.element.definition.staticValues.poolId),
              this.getStateUpdateObject(element, 'productId', this.product?.id),
              this.getStateUpdateObject(element, 'materialId', this.currentItem.id),
              this.getStateUpdateObject(element, 'materialName', this.currentItem.name),
              this.getStateUpdateObject(element, 'priceGroup', this.currentItem.priceGroup),
              this.getStateUpdateObject(element, 'materialIdSecondary', secondItem.id),
              this.getStateUpdateObject(element, 'materialNameSecondary', secondItem.name),
              this.getStateUpdateObject(element, 'priceGroupSecondary', secondItem.priceGroup),
              this.getStateUpdateObject(element, 'materialsSecondary', []),
              this.getStateUpdateObject(element, 'materialColorMixing', materialColorMixing),
              this.getStateUpdateObject(element, 'materialColorArrangement', materialColorOrder),
              this.getStateUpdateObject(element, 'materialColorQuantity', inputCountString)
						],
					},
				})
			);
		}
	}

	public removeInput(): void {
		const element = this.element$.value;
		if (!element) {
			return;
		}
		this.store.dispatch(
			updateConfigurationState({
				updates: {
					remove: [
            this.getStateUpdateObject(element)
					],
				},
			})
		);
	}

  private getStateUpdateObject(element: ProgressElement, property: string = null, value: any = null) {
    return {
      sectionId: element.element.sectionId,
      elementId: element.element.id,
      property: property,
      value: value,
    }
  }
}
