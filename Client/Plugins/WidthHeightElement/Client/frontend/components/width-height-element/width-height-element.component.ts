import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { FormControl, UntypedFormControl, UntypedFormGroup } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement, ProgressState } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { HeightWidthProperties, Product } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';
import { distinctUntilChanged, Subject, takeUntil } from 'rxjs';
import { selectProgressState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';

@Component({
	selector: 'apto-width-height-element',
	templateUrl: './width-height-element.component.html',
	styleUrls: ['./width-height-element.component.scss'],
})
export class WidthHeightElementComponent implements OnInit, OnDestroy {
	@Input()
	public element: ProgressElement<HeightWidthProperties> | undefined | null;

	@Input()
	public product: Product | null | undefined;

	@Input()
	public isDialog = false;

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

	public formElement = new UntypedFormGroup({
		height: new UntypedFormControl(0),
		width: new UntypedFormControl(0),
    quantityInput: new FormControl<number>(1),
	});

	public hasValues(): boolean {
		return this.element ? this.element.state.active : false;
	}

	public stepWidth: number = 1;

	public stepHeight: number = 1;

	public itemsHeight: SelectItem[] = [];

	public itemsWidth: SelectItem[] = [];

  private formSavedFromSelectButton: boolean = false;
  private readonly destroy$ = new Subject<void>();
  private readonly progressState$ = this.store.select(selectProgressState);
  private progressState: ProgressState = null;

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

	public constructor(
    private store: Store
  ) { }

	public ngOnInit(): void {
		if (!this.element) {
			return;
		}
    this.setFormInputs();

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

		if (this.element.element.definition.properties.height && this.element.element.definition.properties.height[0]) {
			this.stepHeight = this.element.element.definition.properties.height?.[0]?.step;
		}

		if (this.element.element.definition.properties.width && this.element.element.definition.properties.width[0]) {
			this.stepWidth = this.element.element.definition.properties.width?.[0]?.step;
		}

		if (this.element.element.definition.staticValues.renderingHeight === 'select') {
			if (this.element.element.definition.properties.height?.[0]?.maximum) {
				this.itemsHeight = this.getSelectValues(
					this.element.element.definition.properties.height[0]?.minimum,
					this.element.element.definition.properties.height[0]?.maximum,
					this.element.element.definition.properties.height[0]?.step
				);
			}
		}

		if (this.element.element.definition.staticValues.renderingWidth === 'select') {
			if (this.element.element.definition.properties.width?.[0]?.maximum) {
				this.itemsWidth = this.getSelectValues(
					this.element.element.definition.properties.width?.[0]?.minimum,
					this.element.element.definition.properties.width?.[0]?.maximum,
					this.element.element.definition.properties.width?.[0]?.step
				);
			}
		}
	}

  /*  If we switch between sections by clicking on the section in the right menu or next/previous buttons, then we need to update
      the form input values with the values from the state, otherwise when switching between sections the values in the form
      will stay the same and will not update.
      But if we patch form value without this if check, then on saving the form with "Auswählen" button we will see flickering:
      new value -> old value -> new value.
      So we need to patchValue with value from state, but only if we switch between sections.  */
  private setFormInputs(): void {
    // eslint-disable-next-line dot-notation
    this.formElement.controls['height'].setValue(
      this.element?.state.values.height || this.element.element.definition.staticValues.defaultHeight || 0
    );
    // eslint-disable-next-line dot-notation
    this.formElement.controls['width'].setValue(
      this.element?.state.values.width || this.element.element.definition.staticValues.defaultWidth || 0
    );
  }

  public getProgressElement(elementId: string): ProgressElement | null {
    const element = this.progressState.currentStep.elements.filter((e) => e.element.id === elementId);
    if (element.length > 0) {
      return element[0];
    }
    return null;
  }

	public saveInput(): void {
		if (!this.element) {
			return;
		}
    this.formSavedFromSelectButton = true;

    this.store.dispatch(
			updateConfigurationState({
				updates: {
					set: Object.entries(this.formElement.value)
						.filter(
							([property]) =>
								(this.element?.element.definition.staticValues.renderingWidth !== 'none' && property === 'width') ||
								(this.element?.element.definition.staticValues.renderingHeight !== 'none' && property === 'height')
						)
						.map(([property, value]) => ({
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
					remove: Object.entries(this.formElement.value)
						.filter(
							([property]) =>
								(this.element?.element.definition.staticValues.renderingWidth !== 'none' && property === 'width') ||
								(this.element?.element.definition.staticValues.renderingHeight !== 'none' && property === 'height')
						)
						.map(([property, value]) => ({
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
