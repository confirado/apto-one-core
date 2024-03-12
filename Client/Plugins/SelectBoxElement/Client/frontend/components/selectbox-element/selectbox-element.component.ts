import { Component, Input, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';
import { CatalogMessageBusService } from '@apto-catalog-frontend/services/catalog-message-bus.service';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { Store } from '@ngrx/store';
import { startWith } from 'rxjs';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';

@UntilDestroy()
@Component({
	selector: 'apto-selectbox-element',
	templateUrl: './selectbox-element.component.html',
	styleUrls: ['./selectbox-element.component.scss'],
})
export class SelectboxElementComponent implements OnInit {
	@Input()
	public element: ProgressElement | undefined | null;

  @Input()
  public section: Section | null | undefined;

  @Input()
  public product: Product | null | undefined;

  @Input()
  public isDialog = false;

	public formElement = new FormControl<string[] | string>([]);

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

	public constructor(private store: Store, private catalogMessageBusService: CatalogMessageBusService) {}

	public items: SelectItem[] = [];

	public selectedItems: { id: string; name: TranslatedValue | undefined; multi: string }[] = [];

	public currentFormArray: { id: string; name: TranslatedValue | undefined; multi: FormControl<number> }[] = [];

	public committedBoxes: { id: string; name: TranslatedValue | undefined; multi: string }[] = [];

	public ngOnInit(): void {
		if (!this.element) {
			return;
		}

		if (this.element.element.definition.staticValues.enableMultiSelect) {
			if (this.element?.state.values.selectedItem || this.element.element.definition.staticValues.defaultItem?.id) {
				this.formElement.setValue(
					this.element?.state.values.selectedItem || [this.element.element.definition.staticValues.defaultItem?.id]
				);
			} else {
				this.formElement.setValue([]);
			}
		} else if (!this.element.element.definition.staticValues.enableMultiSelect) {
			if (this.element?.state.values.selectedItem) {
				this.formElement.setValue(this.element?.state.values.selectedItem);
			} else if (this.element.element.definition.staticValues.defaultItem?.id) {
				this.formElement.setValue([this.element.element.definition.staticValues.defaultItem?.id]);
			} else {
				this.formElement.setValue([]);
			}
		}

    let initialized = false;
		this.catalogMessageBusService
			.findSelectBoxItems(this.element.element.id)
			.pipe(untilDestroyed(this))
			.subscribe((result) => {
				this.items = result.data;
				// Set default value
				this.formElement.valueChanges.pipe(startWith(this.formElement.value)).subscribe((value) => {
					if (this.element?.state.active) {
						this.committedBoxes = this.element.state.values.boxes;
					}
					if (this.element?.element.definition.staticValues.enableMultiSelect && this.formElement.value) {
						for (const entry of this.formElement.value) {
							if (!this.currentFormArray.some((item) => item.id === entry) || this.currentFormArray.length === 0) {
								this.currentFormArray.push({
									id: entry,
									name: this.items.find((i) => i.id === entry)?.name,
									multi: new FormControl<number>(parseInt(this.committedBoxes.find((i) => i.id === entry)?.multi || '1', 10), {
										nonNullable: true,
									}),
								});
							}
						}
						this.currentFormArray = this.currentFormArray.filter((item) => this.formElement.value?.includes(item.id));

						if (this.formElement.value.length === 0) {
							this.currentFormArray = [];
						}
					} else if (!this.element?.element.definition.staticValues.enableMultiSelect && this.formElement.value) {
						const currentId: string = this.formElement.value[0];
						if (!this.currentFormArray.some((item) => item.id === currentId)) {
							this.currentFormArray = [
								{
									id: currentId,
									name: this.items.find((item) => item.id === currentId)?.name,
									multi: new FormControl<number>(parseInt(this.committedBoxes.find((i) => i.id === currentId)?.multi || '1', 10), {
										nonNullable: true,
									}),
								},
							];
						}
					}
					if (this.formElement.value === null) {
						this.currentFormArray = [];
					}

          if (initialized === false) {
            initialized = true;
            this.updateSelectedItems();
          } else {
            this.saveInput();
          }
				});
			});
	}

	public setBoxName(id: string | undefined): TranslatedValue | undefined {
		if (this.items.some((item) => item.id === id)) {
			return this.items.find((item) => item.id === id)?.name;
		}
		return { de_DE: '' };
	}

  public updateSelectedItems() {
    this.selectedItems = [];
    for (const item of this.currentFormArray) {
      this.selectedItems.push({
        id: item.id,
        name: item.name,
        multi: item.multi.value.toString(),
      });
    }
  }

  protected get hasAttachments(): boolean {
    return this.element.element.attachments?.length !== 0;
  }

	public saveInput(): void {
		if (!this.element) {
			return;
		}

    this.updateSelectedItems();

		if (this.formElement && this.formElement.value && this.formElement.value.length > 0) {
			this.store.dispatch(
				updateConfigurationState({
					updates: {
						set: [
							{
								sectionId: this.element.element.sectionId,
								elementId: this.element.element.id,
                sectionRepetition: this.element.state.sectionRepetition,
                property: 'aptoElementDefinitionId',
								value: 'apto-element-select-box',
							},
							{
								sectionId: this.element.element.sectionId,
								elementId: this.element.element.id,
                sectionRepetition: this.element.state.sectionRepetition,
                property: 'boxes',
								value: this.selectedItems,
							},
							{
								sectionId: this.element.element.sectionId,
								elementId: this.element.element.id,
                sectionRepetition: this.element.state.sectionRepetition,
                property: 'selectedItem',
								value: this.formElement.value,
							},
						],
					},
				})
			);
		} else {
			this.store.dispatch(
				updateConfigurationState({
					updates: {
						remove: [
							{
								sectionId: this.element.element.sectionId,
								elementId: this.element.element.id,
                sectionRepetition: this.element.state.sectionRepetition,
                property: 'aptoElementDefinitionId',
								value: 'apto-element-select-box',
							},
						],
					},
				})
			);
		}
	}
}
