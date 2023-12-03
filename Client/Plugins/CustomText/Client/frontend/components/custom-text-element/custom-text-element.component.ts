import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement, ProgressState } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Store } from '@ngrx/store';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { distinctUntilChanged, Subject, takeUntil } from 'rxjs';
import { selectProgressState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';

@Component({
	selector: 'apto-custom-text-element',
	templateUrl: './custom-text-element.component.html',
	styleUrls: ['./custom-text-element.component.scss'],
})
export class CustomTextElementComponent implements OnInit, OnDestroy {
	@Input()
	public element: ProgressElement | undefined | null;

  @Input()
  public product: Product | null | undefined;

  @Input()
  public isDialog = false;

	public formElement = new FormControl<string | undefined>(undefined);
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

  private formSavedFromSelectButton: boolean = false;
  private readonly destroy$ = new Subject<void>();
  private readonly progressState$ = this.store.select(selectProgressState);
  private progressState: ProgressState = null;

	public constructor(private store: Store) {}

	public ngOnInit(): void {

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

	public hasValues(): boolean {
		return this.element ? this.element.state.active : false;
	}

  /*  If we switch between sections by clicking on the section in the right menu or next/previous buttons, then we need to update
      the form input values with the values from the state, otherwise when switching between sections the values in the form
      will stay the same and will not update.
      But if we patch form value without this if check, then on saving the form with "Auswählen" button we will see flickering:
      new value -> old value -> new value.
      So we need to patchValue with value from state, but only if we switch between sections. */
  private setFormInputs(): void {
    this.formElement.setValue(this.element?.state.values.text);
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
					set: [
						{
							sectionId: this.element!.element.sectionId,
							elementId: this.element!.element.id,
              sectionRepetition: this.element!.state.sectionRepetition,
              property: 'aptoElementDefinitionId',
							value: 'apto-element-custom-text',
						},
						{
							sectionId: this.element!.element.sectionId,
							elementId: this.element!.element.id,
              sectionRepetition: this.element!.state.sectionRepetition,
              property: 'text',
							value: this.formElement.value,
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
    this.formSavedFromSelectButton = true;

    this.store.dispatch(
			updateConfigurationState({
				updates: {
					remove: [
						{
							sectionId: this.element!.element.sectionId,
							elementId: this.element!.element.id,
              sectionRepetition: this.element!.state.sectionRepetition,
              property: 'aptoElementDefinitionId',
							value: 'apto-element-custom-text',
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
