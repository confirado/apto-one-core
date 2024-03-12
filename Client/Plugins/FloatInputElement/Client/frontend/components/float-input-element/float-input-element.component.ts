import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';

import { debounceTime, distinctUntilChanged, of, Subject, takeUntil, filter, tap, map } from 'rxjs';
import { Store } from '@ngrx/store';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { selectStateElements } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ElementState, ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product, FloatInputTypes, CompareValueTypes, Section } from '@apto-catalog-frontend/store/product/product.model';

import { number, Parser, parser } from 'mathjs';
import { BigNumber } from 'bignumber.js';
import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';

@Component({
	selector: 'apto-float-input-element',
	templateUrl: './float-input-element.component.html',
	styleUrls: ['./float-input-element.component.scss'],
})
export class FloatInputElementComponent implements OnInit, OnDestroy {
  @Input()
  public product: Product | null | undefined;

  @Input()
  public section: Section | null | undefined;

  @Input()
  public element: ProgressElement | undefined | null;

  @Input()
  public isDialog = false;

  @Input()
  public fullWidth = false;

	public formElementInput = new FormControl<string | undefined>(undefined);
  public formElementSlider = new FormControl<string | undefined>(undefined);
	public stepValue = 0.1;
  public readonly floatInputTypes = FloatInputTypes;
  public readonly compareValueTypes = CompareValueTypes;
  public inputType: string = FloatInputTypes.INPUT;
  public minValue: number | null;
  public maxValue: number | null;
  public increaseStep: number | undefined;
  public decreaseStep: number | undefined;

  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));
  private readonly destroy$ = new Subject<void>();
  private readonly stateElements$ = this.store.select(selectStateElements);
  private stateElements: ElementState[];
  private saveDelay = 500;
  private mathjsParser: Parser;

  public constructor(private store: Store) {}

	public ngOnInit(): void {
    if (!this.element) {
      return;
    }

    this.initIncreaseDecreaseStep();

    this.mathjsParser = parser();

    this.inputType = this.element?.element.definition.staticValues.renderingType;
    this.formElementInput.setValue(this.element?.state.values.value || this.element?.element.definition.staticValues.defaultValue || '1');
    this.formElementSlider.setValue(this.element?.state.values.value || this.element?.element.definition.staticValues.defaultValue || '1');

    if (this.element?.element.definition.properties.value && this.element.element.definition.properties.value[0]) {
      this.stepValue = this.element.element.definition.properties.value?.[0]?.step;
    }

    this.stateElements$
      .pipe(
        takeUntil(this.destroy$),
        distinctUntilChanged()
      ).subscribe((stateElements) => {
        this.stateElements = stateElements;
        this.minValue = this.calculateMinMaxValues().minValue;
        this.maxValue = this.calculateMinMaxValues().maxValue;
      });

    // we dont need form element subscriptions in that case, because save is triggered by save button
    if (this.inputType === FloatInputTypes.INPUT) {
      return;
    }

    this.formElementInput.valueChanges.pipe(
      takeUntil(this.destroy$),
      distinctUntilChanged(),
      /* sync value with input form element only if the value is not equal, otherwise it could cause an endless loop */
      filter((data) => data && this.formElementSlider.value !== data),
      tap((data) => {
        this.formElementSlider.setValue(data); // Update the slider immediately
      }),
      debounceTime(this.saveDelay),
      map((data) => {
        this.saveInput(data); // Save the input value with a delay
        return of(data);
      })
    ).subscribe();

    // In case of slider input we want that it saves it's value without clicking on save button
    this.formElementSlider.valueChanges.pipe(
      takeUntil(this.destroy$),
      distinctUntilChanged(),
      /* sync value with input form element only if the value is not equal, otherwise it could cause an endless loop */
      filter((data) => this.formElementInput.value !== data)
    ).subscribe((data) => {
      this.formElementInput.setValue(data as string);
    });
	}

  /**
   * Slider is considered as changed when user slides and lets the left mouse button
   * during sliding, slide change event is not fired.
   * So we save into store when user is finished with sliding and lets the mouse button
   *
   * @param event
   */
  public onSliderChanged(event: MouseEvent): void {
      this.saveInput(this.formElementSlider.value);
  }

  public get hint(): TranslatedValue {
    return this.element.element.definition.staticValues.suffix;
  }

  public get prefix(): TranslatedValue {
    return this.element.element.definition.staticValues.prefix;
  }

  public get suffix(): TranslatedValue {
    return this.element.element.definition.staticValues.suffix;
  }

  private initIncreaseDecreaseStep() {
    let increaseStep = this.element.element.customProperties.find((customProperty) => {
      return customProperty.key === 'increaseStep';
    });
    let decreaseStep = this.element.element.customProperties.find((customProperty) => {
      return customProperty.key === 'decreaseStep';
    });

    if (increaseStep && typeof increaseStep.value === 'string') {
      this.increaseStep = parseFloat(increaseStep.value);
    }
    if (decreaseStep && typeof decreaseStep.value === 'string') {
      this.decreaseStep = parseFloat(decreaseStep.value);
    }
  }

  /**
   * Calculates minimum and maximum values
   *
   * @private
   */
  private calculateMinMaxValues(): { minValue: number | undefined, maxValue: number | undefined } {

    let min = null,
      max = null;

    for (const elementValueRef of this.element.element.definition.staticValues.elementValueRefs) {
      const configurationValue = this.getElementPropertyValueFromState(elementValueRef.sectionId, elementValueRef.elementId, elementValueRef.selectableValue);

      // continue if value is not set in configuration
      if (configurationValue === null) {
        continue;
      }

      switch (elementValueRef.compareValueType) {
        case this.compareValueTypes.MINIMUM: {
          // continue if minimum is already set because the first found value counts
          if (min !== null) {
            continue;
          }

          min = this.getElementPropertyFromValueReference(elementValueRef, configurationValue);
          break;
        }
        case this.compareValueTypes.MAXIMUM: {
          // continue if maximum is already set because the first found value counts
          if (max !== null) {
            continue;
          }

          max = this.getElementPropertyFromValueReference(elementValueRef, configurationValue);
          break;
        }
      }

      if (min !== null && max !== null) {
        break;
      }
    }

    min = min ?? this.element.element.definition.properties.value?.[0]?.minimum;
    max = max ?? this.element.element.definition.properties.value?.[0]?.maximum;

    return { minValue: min, maxValue: max };
  }

  private getElementPropertyValueFromState(sectionId, elementId, property): number | string | null {
    if (!sectionId || !elementId || !property) {
      return null;
    }

    for (const stateElement of this.stateElements) {
      if (stateElement.active && stateElement.sectionId === sectionId && stateElement.id === elementId) {
        return stateElement.values[property] ?? null;
      }
    }

    return null;
  }

  /**
   * Calculates the result of the formula given in value reference (Abhängige Werte) by giving as parameter values from the state
   *
   * in state 'width: 1200'
   * formula: 'width - 500'
   *
   * @param elementValueRef
   * @param configurationValue
   * @private
   */
  private getElementPropertyFromValueReference(elementValueRef, configurationValue): number {
    let value;

    if (elementValueRef.compareValueFormula) {
      this.mathjsParser.set(elementValueRef.selectableValue, configurationValue);
      value = this.mathjsParser.evaluate(elementValueRef.compareValueFormula);
      this.mathjsParser.clear();
    } else {
      value = configurationValue;
    }

    return this.roundValueByStep(value, this.getLowestStep());
  }

  private roundValueByStep(valueInput, stepInput): number {
    const value = new BigNumber(valueInput.toString());
    const step = new BigNumber(stepInput.toString());

    return parseFloat(value.toFixed(step.decimalPlaces()));
  }

  private getLowestStep(): null | number {
    const values = this.element.element.definition.properties.value;

    if (!values || values.length === 0) {
      return null;
    }

    return Math.min(...values.map((validValue) => validValue.step));
  }

  /**
   * This is triggered when the user moves slider left right
   *
   * @param value
   */
  public onSliderInput(value): void {
    this.formElementSlider.setValue(value);
  }

  protected get hasAttachments(): boolean {
    return this.element.element.attachments?.length !== 0;
  }

	public hasValues(): boolean {
		return this.element ? this.element.state.active : false;
	}

	public saveInput(value: string): void {
		if (!this.element) {
			return;
		}

    // do not save the value into the store if it is not in the allowed range
    if (number(value) < this.minValue || number(value) > this.maxValue) {
      return;
    }

		this.store.dispatch(
			updateConfigurationState({
				updates: {
					set: [
						{
							sectionId: this.element!.element.sectionId,
							elementId: this.element!.element.id,
              sectionRepetition: this.element!.state.sectionRepetition,
							property: 'value',
							value: value,
						},
					],
				},
			})
		);
	}

	public removeInput(): void {
		if (!this.element) {
			return;
		}
		this.store.dispatch(
			updateConfigurationState({
				updates: {
					remove: [
						{
							sectionId: this.element!.element.sectionId,
							elementId: this.element!.element.id,
              sectionRepetition: this.element!.state.sectionRepetition,
              property: null,
							value: null,
						},
					],
				},
			})
		);
	}

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
