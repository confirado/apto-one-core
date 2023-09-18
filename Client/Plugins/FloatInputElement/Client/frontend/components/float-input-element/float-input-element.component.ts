import { Component, HostListener, Input, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Store } from '@ngrx/store';
import { Product, FloatInputTypes } from '@apto-catalog-frontend/store/product/product.model';
import { debounceTime, delay, map } from 'rxjs';
import { findAllMatchingNodes } from '@angular/compiler-cli/src/ngtsc/typecheck/src/comments';

@Component({
	selector: 'apto-float-input-element',
	templateUrl: './float-input-element.component.html',
	styleUrls: ['./float-input-element.component.scss'],
})
export class FloatInputElementComponent implements OnInit {
	@Input()
	public element: ProgressElement | undefined | null;

  @Input()
  public product: Product | null | undefined;

  @Input()
  public isDialog = false;

  @Input()
  public fullWidth = false;

  @HostListener('document:mousedown', ['$event'])
  onMouseDown(event: MouseEvent): void {
    if (event.button === 0) {
      this.isLeftMouseDown = true;
    }
  }

  @HostListener('document:mouseup', ['$event'])
  onMouseUp(event: MouseEvent): void {
    if (event.button === 0) {
      this.isLeftMouseDown = false;
    }
  }

	public formElement = new FormControl<string | undefined>(undefined);
	public stepValue: number = 0.1;
	public isLeftMouseDown: boolean = false;

  public readonly floatInputTypes = FloatInputTypes;
  public inputType: string = FloatInputTypes.INPUT;
  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

	public constructor(private store: Store) {}

	public ngOnInit(): void {
		this.inputType = this.element?.element.definition.staticValues.renderingType;
		this.formElement.setValue(this.element?.state.values.value || this.element?.element.definition.staticValues.defaultValue || 0);

		if (this.element?.element.definition.properties.value && this.element.element.definition.properties.value[0]) {
			this.stepValue = this.element.element.definition.properties.value?.[0]?.step;
		}

    // In case of slider input we want that it saves it's value without clicking on save button
    this.formElement.valueChanges.pipe(
      debounceTime(100)
    ).subscribe((data) => {
      // we don't want to save the value if the user is holding the left mouse button pressed
      if (this.isLeftMouseDown === false) {
        this.saveInput();
      }
    });
	}

  /**
   * This is triggered when the user moves slider left right
   *
   * @param value
   */
  public onSliderInput(value): void {
    this.formElement.setValue(value);
  }

	public hasValues(): boolean {
		return this.element ? this.element.state.active : false;
	}

	public saveInput(): void {
		if (!this.element) {
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
		this.store.dispatch(
			updateConfigurationState({
				updates: {
					remove: [
						{
							sectionId: this.element!.element.sectionId,
							elementId: this.element!.element.id,
							property: 'value',
							value: this.formElement.value,
						},
					],
				},
			})
		);
	}
}
