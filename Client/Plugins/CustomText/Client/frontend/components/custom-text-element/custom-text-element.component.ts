import { Component, Input, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ConfigurationError, ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Store } from '@ngrx/store';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { selectConfigurationError } from '@apto-catalog-frontend/store/configuration/configuration.selectors';

@Component({
	selector: 'apto-custom-text-element',
	templateUrl: './custom-text-element.component.html',
	styleUrls: ['./custom-text-element.component.scss'],
})
export class CustomTextElementComponent implements OnInit {
	@Input()
	public element: ProgressElement | undefined | null;

  @Input()
  public section: Section | null | undefined;

  @Input()
  public product: Product | null | undefined;

  @Input()
  public isDialog = false;

	public formElement = new FormControl<string | undefined>(undefined);

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

  public configurationError: ConfigurationError | null = null;

	public constructor(private store: Store) {}

	public ngOnInit(): void {
		this.formElement.setValue(this.element?.state.values.text);

    this.store.select(selectConfigurationError).subscribe((next) => {
      this.configurationError = next;
    });
	}

  protected get hasAttachments(): boolean {
    return this.element.element.attachments?.length !== 0;
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
}
