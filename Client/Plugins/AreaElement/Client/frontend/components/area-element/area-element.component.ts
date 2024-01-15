import { Component, Input, OnInit } from '@angular/core';
import { UntypedFormControl, UntypedFormGroup, Validators } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { AreaElementDefinitionProperties, ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';
import { MatDialogRef } from '@angular/material/dialog';

@Component({
	selector: 'apto-area-element',
	templateUrl: './area-element.component.html',
	styleUrls: ['./area-element.component.scss'],
})
export class AreaElementComponent implements OnInit {
	@Input()
	public element: ProgressElement<AreaElementDefinitionProperties> | undefined | null;

	@Input()
	public product: Product | null | undefined;

  @Input()
  public isDialog = false;

	public formElement = new UntypedFormGroup({});

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

	public itemFieldList: SelectItem[][] = [];

  public sumOfFieldValues = 0;

	public constructor(
    private store: Store,
    private dialogRef: MatDialogRef<AreaElementComponent>
  ) {}

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

		for (
			let i = 0;
			i < Object.entries(this.element.element.definition.properties).filter(([property]) => property.includes('field_')).length;
			i += 1
		) {
			let itemsField: SelectItem[] = [];
      let validators = [];

      if (this.element.element.definition.staticValues.fields?.[i]?.rendering === 'input') {
          validators = [
            Validators.required,
            Validators.min(this.element.element.definition.properties[`field_${i}`][0].minimum),
            Validators.max(this.element.element.definition.properties[`field_${i}`][0].maximum),
          ];
      }

			this.formElement.addControl(
				`field_${i}`,
				new UntypedFormControl(
					this.element.state.values[`field_${i}`] || this.element.element.definition.staticValues.fields?.[i]?.default || 0,
          validators
				)
			);

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

    // todo add logic for not allowing go further when the sum is too big
    this.formElement.valueChanges.subscribe(x => {
      this.sumOfFieldValues = <number>Object.values(x).reduce((a: any, b: any) => Number(a) + Number(b), 0);
    });
  }

	public hasValues(): boolean {
		return this.element ? this.element.state.active : false;
	}

	public saveInput(): void {
		if (!this.element) {
			return;
		}

    this.markAllControlsAsDirty();

    if (!this.formElement.valid) {
      return;
    }

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

    this.dialogRef.close();
	}

	public removeInput(): void {
		if (!this.element) {
			return;
		}
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

  public closeModal(): void {
    this.dialogRef.close();
  }

  private markAllControlsAsDirty(): void {
    Object.keys(this.formElement.controls).forEach((key) => {
      this.formElement.get(key).markAsDirty();
    });
  }
}
