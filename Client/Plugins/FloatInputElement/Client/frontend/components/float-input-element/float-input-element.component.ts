import { Component, Input, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Store } from '@ngrx/store';
import { Product } from '@apto-catalog-frontend/store/product/product.model';

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

	public formElement = new FormControl<string | undefined>(undefined);

	public stepValue: number = 0.1;

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

	public constructor(private store: Store) {}

	public ngOnInit(): void {
		this.formElement.setValue(this.element?.state.values.value || this.element?.element.definition.staticValues.defaultValue || 0);

		if (this.element?.element.definition.properties.value && this.element.element.definition.properties.value[0]) {
			this.stepValue = this.element.element.definition.properties.value?.[0]?.step;
		}
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
