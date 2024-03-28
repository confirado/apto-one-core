import { Component, Inject, OnInit } from '@angular/core';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { Store } from '@ngrx/store';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { translate, TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { HumanReadableFullState } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { DialogDataInterface } from '@apto-catalog-frontend/components/common/dialogs/dialog-data-interface';
import { selectConfigurationLoading, selectHumanReadableFullState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { addOfferConfiguration, addOfferConfigurationSuccess } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { Actions, ofType } from '@ngrx/effects';
import { map } from 'rxjs';
import { LoadingIndicatorTypes } from '@apto-base-core/components/common/loading-indicator/loading-indicator.component';
import { FormValidationsService } from '@apto-base-core/services/form-validations.service';

// eslint-disable-next-line no-shadow
export enum FormFiledAttributeNames {
  LABEL = 'label',
  POSITION = 'position',
  REQUIRED = 'required',
  TYPE = 'type',
}

export interface FormFiledAttributeItem {
  name: FormFiledAttributeNames,
  content: TranslatedValue,
}

export interface FormFiledAttribute {
  children: FormFiledAttributeItem[],
  content: any[]
  name: string,
}

@Component({
	selector: 'apto-configuration-dialog',
	templateUrl: './offer-configuration-dialog.component.html',
	styleUrls: ['./offer-configuration-dialog.component.scss'],
})
export class OfferConfigurationDialogComponent implements OnInit {

  protected readonly AptoOfferConfigurationDialog$ = this.store.select(selectContentSnippet('AptoOfferConfigurationDialog'));
  protected readonly selectLoading$ = this.store.select(selectConfigurationLoading);
  private readonly locale$ = this.store.select(selectLocale);
  private readonly humanReadableFullState$ = this.store.select(selectHumanReadableFullState);
  private humanReadableFullState: HumanReadableFullState[]; // todo create correct type for it

  protected formGroup: FormGroup;
  protected extraFormFields = <any>[];
  protected locale = 'de_DE';
  protected offerConfigurationSent = false;
  protected loadingIndicatorTypes = LoadingIndicatorTypes;

	public constructor(
    private actions$: Actions,
    private store: Store,
    private dialogRef: MatDialogRef<OfferConfigurationDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: DialogDataInterface,
    private formValidations: FormValidationsService,
  ) {}

  public ngOnInit(): void {
    this.locale$.subscribe((next) => {
      this.locale = next;
    });

    this.humanReadableFullState$.subscribe((next) => {
      this.humanReadableFullState = next;
    });

    this.formGroup = new FormGroup({
      email: new FormControl<string>('', { validators: [Validators.required, this.formValidations.emailValidator] }),
      name: new FormControl<string>('', { nonNullable: true }),
    });

    /*  In our offer configuration dialog some form fields are defined in content snippets. We want to read that data
        from content snippets and dynamically create corresponding form inputs   */
    this.AptoOfferConfigurationDialog$.subscribe((data) => {
      data.children.forEach((dialogItem) => {
        if (dialogItem.name === 'form') {
          dialogItem.children.forEach((form) => {
            if (form.name === 'fields') {
              this.sortExtraFormFields(form, FormFiledAttributeNames.POSITION);

              // dynamically add items to the 'formGroup' form
              form.children.forEach((fields) => {
                this.formGroup.addControl(fields.name, new FormControl<string>('', { nonNullable: true }));
              });
            }
          });
        }
      });
    });
  }

  protected nameAttr(field: FormFiledAttribute): string {
    return field.name;
  }

  protected labelAttr(field: FormFiledAttribute): string {
    const { label, required } = this.constructAttributes(field.children);
    return translate(label.content, this.locale) + (translate(required.content, this.locale) === 'required' ? '*' : '');
  }

  protected requiredAttr(field: FormFiledAttribute): boolean {
    const { required } = this.constructAttributes(field.children);
    return translate(required.content, this.locale) === 'required';
  }

  protected typeAttr(field: FormFiledAttribute): string {
    const { type } = this.constructAttributes(field.children);
    return type ? translate(type.content, this.locale) : '';
  }

  protected get isFormValid(): boolean {
    return this.formGroup.status === 'VALID';
  }

  /**
   * return value looks like this:
   * {
   *   "label": {
   *     "name": "label",
   *     "content": {
   *         "de_DE": "Nachricht"
   *     }
   *   },
   *   "position": { ... },
   *   "required": { ... },
   *   "type": { ... },
   * }
   *
   * @param formAttributes
   * @private
   */
  private constructAttributes(formAttributes: FormFiledAttributeItem[]): { [key in FormFiledAttributeNames]: FormFiledAttributeItem } {
    const attributes: any = {};

    formAttributes.forEach((attr) => {
      attributes[attr.name] = attr;
    });

    return attributes;
  }

  /**
   * In our template/dialog we want to display form inputs sorted by 'position' field
   * so for showing dynamically created form inputs we use this data
   *
   * @param form
   * @param field
   * @protected
   */
  private sortExtraFormFields(form, field: string): void {
    // let's first make "position" field integer so that we can compare
    const sortableData: any = form.children.map((parent: any) => ({
      ...parent,
      position: parseInt(parent.children.find((child) => child.name === field)?.content.de_DE || 0, 10),
    }));

    // Sort the data based on the "position" field
    sortableData.sort((a, b) => a.position - b.position);

    this.extraFormFields = {
      ...form,
      children: sortableData,
    };
  }

  private generateFormData(): object {
    const formData = {};

    this.extraFormFields.children.forEach((field) => {
      formData[field.name] = this.formGroup.get(field.name).value;
    });

    return formData;
  }

  protected onSubmit(): void {
    if (this.isFormValid) {
      this.store.dispatch(addOfferConfiguration(
        {
          payload: {
            name: this.formGroup.get('name').value,
            email: this.formGroup.get('email').value,
            payload: {
              formData: this.generateFormData(),
              humanReadableState: this.humanReadableFullState,
            },
          },
        },
      ));

      if (this.dialogRef?.id) {
        this.actions$.pipe(
          ofType(addOfferConfigurationSuccess),
          map(() => this.dialogRef.close())
        ).subscribe();
      }
    }
  }
}
