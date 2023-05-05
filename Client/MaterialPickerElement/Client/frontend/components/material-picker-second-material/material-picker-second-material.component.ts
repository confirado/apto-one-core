import { Component, forwardRef, Input, OnInit } from '@angular/core';
import { ControlValueAccessor, FormControl, FormGroup, NG_VALUE_ACCESSOR } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { MaterialPickerFilterForm, MaterialPickerItem, PropertyGroup } from '@apto-catalog-frontend/models/material-picker';
import { CatalogMessageBusService } from '@apto-catalog-frontend/services/catalog-message-bus.service';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { environment } from '@apto-frontend/src/environments/environment';
import { Store } from '@ngrx/store';
import { BehaviorSubject, combineLatest, debounceTime, delay, filter, map, startWith, switchMap } from 'rxjs';

@Component({
	selector: 'apto-material-picker-second-material',
	templateUrl: './material-picker-second-material.component.html',
	styleUrls: ['./material-picker-second-material.component.scss'],
	providers: [
		{
			provide: NG_VALUE_ACCESSOR,
			// eslint-disable-next-line
			useExisting: forwardRef(() => MaterialPickerSecondMaterialComponent),
			multi: true,
		},
	],
})
export class MaterialPickerSecondMaterialComponent implements OnInit, ControlValueAccessor {
	@Input()
	public set element(element: ProgressElement<any> | undefined) {
		this.element$.next(element);
	}

	@Input()
	public product: Product | undefined;

	@Input()
	public searchBox: boolean = false;

	public element$ = new BehaviorSubject<ProgressElement<any> | undefined>(undefined);

	public mediaUrl = environment.api.media + '/';

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('plugins.materialPickerElement'));

	public readonly contentSnippetButton$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

	public filter = new FormGroup<MaterialPickerFilterForm>({
		colorRating: new FormControl<string | null>(null),
		priceGroup: new FormControl<string | null>(null),
		properties: new FormGroup<any>({}),
		searchString: new FormControl<string>(''),
	});

	public secondaryFormElement = new FormControl<{ id: string; name: string; priceGroup: string }[]>([]);

	public currentItemMaterials: { id: string; name: string; priceGroup: string }[] = [];

	public currentItem: { id: string; name: string; priceGroup: string } | undefined;

	public materialCount: number = 0;

	public typedElement$ = this.element$.pipe(filter((element): element is ProgressElement<any> => element !== undefined));

	public isSelected(id: string): boolean {
		const element = this.element$.value;
		if (!element) {
			return false;
		}
		if (element.element.definition.staticValues.allowMultiple) {
			return this.currentItemMaterials.some((i) => i.id === id);
		}
		if (!element.element.definition.staticValues.allowMultiple) {
			if (this.currentItem) {
				return this.currentItem.id === id;
			}
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

	public constructor(private catalogMessageBusService: CatalogMessageBusService, private store: Store) {}

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
	}

	public saveInput(item: MaterialPickerItem, localeItems: { id: string; name: string; priceGroup: string }[]): void {
		const element = this.element$.value;
		if (!element) {
			return;
		}
		const localeItem = localeItems.find((i) => i.id === item.material.id);

		if (element.element.definition.staticValues.allowMultiple) {
			const cutItem = this.currentItemMaterials.find((i) => i.id === item.material.id);
			if (cutItem) {
				this.currentItemMaterials.splice(this.currentItemMaterials.indexOf(cutItem), 1);
			} else if (localeItem) {
				this.currentItemMaterials.push({
					id: item.material.id,
					name: localeItem.name,
					priceGroup: localeItem.priceGroup,
				});
			}
			this.secondaryFormElement.setValue(this.currentItemMaterials);
		} else if (localeItem) {
			if (this.currentItem && localeItem.id === this.currentItem.id) {
				this.currentItem = undefined;
				this.secondaryFormElement.setValue([]);
			} else {
				this.currentItem = localeItem;
				this.secondaryFormElement.setValue([localeItem]);
			}
		}
	}

	public registerOnChange(fn: any): void {
		this.secondaryFormElement.valueChanges.pipe(delay(0), debounceTime(100)).subscribe(fn);
	}

	public registerOnTouched(fn: any): void {}

	public writeValue(obj: { id: string; name: string; priceGroup: string }[]): void {
		const element = this.element$.value;
		if (!element) {
			return;
		}
		if (!element.element.definition.staticValues.allowMultiple) {
			[this.currentItem] = obj;
		} else {
			this.currentItemMaterials = obj;
		}
		this.secondaryFormElement.setValue(obj);
	}
}
