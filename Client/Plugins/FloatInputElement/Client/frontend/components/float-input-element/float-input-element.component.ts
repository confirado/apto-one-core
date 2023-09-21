import { Component, Input, OnDestroy, OnInit} from '@angular/core';
import { FormControl } from '@angular/forms';
import { debounceTime, distinctUntilChanged, of, Subject, takeUntil, filter, tap, map } from 'rxjs';
import { Store } from '@ngrx/store';

import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product, FloatInputTypes } from '@apto-catalog-frontend/store/product/product.model';


@Component({
	selector: 'apto-float-input-element',
	templateUrl: './float-input-element.component.html',
	styleUrls: ['./float-input-element.component.scss'],
})
export class FloatInputElementComponent implements OnInit, OnDestroy {
	@Input()
	public element: ProgressElement | undefined | null;

  @Input()
  public product: Product | null | undefined;

  @Input()
  public isDialog = false;

  @Input()
  public fullWidth = false;

	public formElementInput = new FormControl<string | undefined>(undefined);
  public formElementSlider = new FormControl<string | undefined>(undefined);
	public stepValue = 0.1;

  public readonly floatInputTypes = FloatInputTypes;
  public inputType: string = FloatInputTypes.INPUT;
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));
  private saveDelayTimeoutId: any = null;
  private saveDelay = 250;
  private destroy$ = new Subject<void>();

	public constructor(private store: Store) {}

	public ngOnInit(): void {
		this.inputType = this.element?.element.definition.staticValues.renderingType;
		this.formElementInput.setValue(this.element?.state.values.value || this.element?.element.definition.staticValues.defaultValue || 0);
    this.formElementSlider.setValue(this.element?.state.values.value || this.element?.element.definition.staticValues.defaultValue || 0);

		if (this.element?.element.definition.properties.value && this.element.element.definition.properties.value[0]) {
			this.stepValue = this.element.element.definition.properties.value?.[0]?.step;
		}

    // we dont need form element subscriptions in that case, because save is triggered by save button
    if (this.inputType === FloatInputTypes.INPUT) {
      return;
    }

    // @todo compare this version then keep or remove
    // // In case of slider input we want that it saves it's value without clicking on save button
    // this.formElementInput.valueChanges.pipe(
    //   takeUntil(this.destroy$),
    //   distinctUntilChanged(),
    //   /* sync value with input form element only if the value is not equal, otherwise it could cause an endless loop */
    //   filter((data) => this.formElementSlider.value !== data),
    // )
    //   .subscribe((data) => {
    //     console.error('subscribe input');
    //     this.formElementSlider.setValue(data);
    //
    //     if (null !== this.saveDelayTimeoutId) {
    //       clearTimeout(this.saveDelayTimeoutId);
    //       this.saveDelayTimeoutId = null;
    //     }
    //
    //     // save input value if value is not changed within 'saveDelay' time
    //     this.saveDelayTimeoutId = setTimeout(() => {
    //       console.error('save input');
    //       this.saveInput(this.formElementSlider.value);
    //     }, this.saveDelay);
    //   });


    this.formElementInput.valueChanges.pipe(
      takeUntil(this.destroy$),
      distinctUntilChanged(),
      /* sync value with input form element only if the value is not equal, otherwise it could cause an endless loop */
      filter((data) => this.formElementSlider.value !== data),
      tap((data) => {
        console.error('subscribe input');
        this.formElementSlider.setValue(data); // Update the slider immediately
      }),
      debounceTime(this.saveDelay),
      map((data) => {
        console.error('save input');
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
      console.error('subscribe slider');
      this.formElementInput.setValue(data as string);
    });
	}

  public onSliderMouseUp(event: MouseEvent): void {
    // we only want to save the value on mouse up event
    console.error('save slider');
    this.saveInput(this.formElementSlider.value);
  }

  /**
   * This is triggered when the user moves slider left right
   *
   * @param value
   */
  public onSliderInput(value): void {
    this.formElementSlider.setValue(value);
  }

  // @todo do we need this?
	public hasValues(): boolean {
		return this.element ? this.element.state.active : false;
	}

	public saveInput(value: string): void {
		if (!this.element || !value) {
			return;
		}
		this.store.dispatch(
			updateConfigurationState({
				updates: {
					set: [
						{
							sectionId: this.element!.element.sectionId,
							elementId: this.element!.element.id,
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
