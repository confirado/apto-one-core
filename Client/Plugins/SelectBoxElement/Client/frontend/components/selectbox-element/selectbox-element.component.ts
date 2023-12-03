import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';
import { CatalogMessageBusService } from '@apto-catalog-frontend/services/catalog-message-bus.service';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement, ProgressState } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { Store } from '@ngrx/store';
import { distinctUntilChanged, startWith, Subject, takeUntil } from 'rxjs';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { selectProgressState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';

@UntilDestroy()
@Component({
	selector: 'apto-selectbox-element',
	templateUrl: './selectbox-element.component.html',
	styleUrls: ['./selectbox-element.component.scss'],
})
export class SelectboxElementComponent implements OnInit, OnDestroy {
	@Input()
	public element: ProgressElement | undefined | null;

  @Input()
  public product: Product | null | undefined;

  @Input()
  public isDialog = false;

	public formElement = new FormControl<string[] | string>([]);

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

  private readonly destroy$ = new Subject<void>();
  private readonly progressState$ = this.store.select(selectProgressState);
  private progressState: ProgressState = null;
  private formSavedFromSelectButton: boolean = false;

	public constructor(private store: Store, private catalogMessageBusService: CatalogMessageBusService) {}

	public items: SelectItem[] = [];

	public selectedItems: { id: string; name: TranslatedValue | undefined; multi: string }[] = [];

	public currentFormArray: { id: string; name: TranslatedValue | undefined; multi: FormControl<number> }[] = [];

	public committedBoxes: { id: string; name: TranslatedValue | undefined; multi: string }[] = [];

	public ngOnInit(): void {
		if (!this.element) {
			return;
		}
    this.setFormInputs();

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
				});
			});

    this.progressState$.pipe(
      takeUntil(this.destroy$),
      distinctUntilChanged()
    ).subscribe((next: ProgressState) => {
      this.progressState = next;
      this.element = this.getProgressElement(this.element?.element.id);

      if (!this.formSavedFromSelectButton) {
        this.setFormInputs();
      }

      this.formSavedFromSelectButton = false;
    });
	}

  /*  If we switch between sections by clicking on the section in the right menu or next/previous buttons, then we need to update
      the form input values with the values from the state, otherwise when switching between sections the values in the form
      will stay the same and will not update.
      But if we patch form value without this if check, then on saving the form with "Auswählen" button we will see flickering:
      new value -> old value -> new value.
      So we need to patchValue with value from state, but only if we switch between sections.  */
  private setFormInputs(): void {
    if (this.element?.element.definition.staticValues.enableMultiSelect) {
      if (this.element?.state.values.selectedItem || this.element?.element.definition.staticValues.defaultItem?.id) {
        this.formElement.setValue(
          this.element?.state.values.selectedItem || [this.element?.element.definition.staticValues.defaultItem?.id]
        );
      } else {
        this.formElement.setValue([]);
      }
    } else if (!this.element?.element.definition.staticValues.enableMultiSelect) {
      if (this.element?.state.values.selectedItem) {
        this.formElement.setValue(this.element?.state.values.selectedItem);
      } else if (this.element?.element.definition.staticValues.defaultItem?.id) {
        this.formElement.setValue([this.element?.element.definition.staticValues.defaultItem?.id]);
      } else {
        this.formElement.setValue([]);
      }
    }
  }

  public getProgressElement(elementId: string): ProgressElement | null {
    const element = this.progressState.currentStep.elements.filter((e) => e.element.id === elementId);
    if (element.length > 0) {
      return element[0];
    }
    return null;
  }

	public setBoxName(id: string | undefined): TranslatedValue | undefined {
		if (this.items.some((item) => item.id === id)) {
			return this.items.find((item) => item.id === id)?.name;
		}
		return { de_DE: '' };
	}

	public saveInput(): void {
		if (!this.element) {
			return;
		}
		this.selectedItems = [];
		for (const item of this.currentFormArray) {
			this.selectedItems.push({
				id: item.id,
				name: item.name,
				multi: item.multi.value.toString(),
			});
		}

    this.formSavedFromSelectButton = true;

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

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
