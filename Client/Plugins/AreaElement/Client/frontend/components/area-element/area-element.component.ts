import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { UntypedFormControl, UntypedFormGroup } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { AreaElementDefinitionProperties, ProgressElement, ProgressState } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';
import { selectProgressState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { distinctUntilChanged, Subject, takeUntil } from 'rxjs';

@Component({
	selector: 'apto-area-element',
	templateUrl: './area-element.component.html',
	styleUrls: ['./area-element.component.scss'],
})
export class AreaElementComponent implements OnInit, OnDestroy {
	@Input()
	public element: ProgressElement<AreaElementDefinitionProperties> | undefined | null;

	@Input()
	public product: Product | null | undefined;

  @Input()
  public isDialog = false;

	public formElement = new UntypedFormGroup({});

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

  private readonly destroy$ = new Subject<void>();
  private readonly progressState$ = this.store.select(selectProgressState);
  private progressState: ProgressState = null;
  private formSavedFromSelectButton: boolean = false;

	public itemFieldList: SelectItem[][] = [];

  public sumOfFieldValues = 0;

	public constructor(private store: Store) {}

	public getSelectValues(min: number, max: number, step: number): SelectItem[] {
		const items: SelectItem[] = [];
		for (let i = min; i <= max; i += step) {
			items.push({
				surrogateId: '',
				id: `${i}`,
				name: { de_DE: `${i}` },
				isDefault: false,
				aptoPrices: [],
			});
		}

		return items;
	}

	public ngOnInit(): void {
		if (!this.element) {
			return;
		}

    this.createFormInputs();

    // todo add logic for not allowing go further when the sum is too big
    this.formElement.valueChanges.pipe(
      distinctUntilChanged()
    ).subscribe((x) => {
      this.sumOfFieldValues = <number>Object.values(x).reduce((a: any, b: any) => Number(a) + Number(b), 0);
    });

    this.progressState$.pipe(
      takeUntil(this.destroy$),
      distinctUntilChanged(),
    ).subscribe((next: ProgressState) => {
      this.progressState = next;
      this.element = this.getProgressElement(this.element?.element.id);

      if (!this.formSavedFromSelectButton) {
        this.setFormInputs();
      }

      this.formSavedFromSelectButton = false;
    });
  }

  private createFormInputs(): void {
    for (let i = 0; i < Object.entries(this.element?.element.definition.properties).filter(([property]) => property.includes('field_')).length; i += 1) {
      let itemsField: SelectItem[] = [];

      this.formElement.addControl(`field_${i}`, new UntypedFormControl(this.getElementValue(i)));

      if (this.element.element.definition.staticValues.fields?.[i]?.rendering === 'select') {
        for (let index = 0; index < Object.entries(this.element.element.definition.properties[`field_${i}`]).length; index += 1) {
          let itemField: SelectItem[] = [];
          if (this.element.element.definition.properties[`field_${i}`][index].maximum) {
            itemField = this.getSelectValues(
              this.element.element.definition.properties[`field_${i}`][index].minimum,
              this.element.element.definition.properties[`field_${i}`][index].maximum,
              this.element.element.definition.properties[`field_${i}`][index].step
            );
          }
          itemsField = itemsField.concat(itemField);
        }
      }

      this.itemFieldList.push(itemsField);
    }
  }

  /*  If we switch between sections by clicking on the section in the right menu or next/previous buttons, then we need to update
      the form input values with the values from the state, otherwise when switching between sections the values in the form
      will stay the same and will not update.
      But if we patch form value without this if check, then on saving the form with "Auswählen" button we will see flickering:
      new value -> old value -> new value.
      So we need to patchValue with value from state, but only if we switch between sections.  */
  private setFormInputs(): void {
    for (let i = 0; i < Object.entries(this.element?.element.definition.properties).filter(([property]) => property.includes('field_')).length; i += 1) {
      if (this.formElement.contains(`field_${i}`)) {
        this.formElement.get(`field_${i}`)?.patchValue(this.getElementValue(i));
      }
    }
  }

  private getElementValue(key: number): number {
    return this.element.state.values[`field_${key}`] || this.element.element.definition.staticValues.fields?.[key]?.default || 0;
  }

  public getProgressElement(elementId: string): ProgressElement | null {
    const element = this.progressState.currentStep.elements.filter((e) => e.element.id === elementId);
    if (element.length > 0) {
      return element[0];
    }
    return null;
  }

	public hasValues(): boolean {
		return this.element ? this.element.state.active : false;
	}

	public saveInput(): void {
		if (!this.element) {
			return;
		}
    this.formSavedFromSelectButton = true;

    this.store.dispatch(
			updateConfigurationState({
				updates: {
					set: Object.entries(this.formElement.value).map(([property, value]) => ({
						sectionId: this.element!.element.sectionId,
						elementId: this.element!.element.id,
            sectionRepetition: this.element!.state.sectionRepetition,
            property,
						value,
					})),
				},
			})
		);
	}

	public removeInput(): void {
    if (!this.element) {
			return;
		}
    this.formSavedFromSelectButton = true;

    this.store.dispatch(
			updateConfigurationState({
				updates: {
					remove: Object.entries(this.formElement.value).map(([property, value]) => ({
						sectionId: this.element!.element.sectionId,
						elementId: this.element!.element.id,
            sectionRepetition: this.element!.state.sectionRepetition,
            property,
						value,
					})),
				},
			})
		);
	}

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
