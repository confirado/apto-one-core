import { Component, Input, OnInit } from '@angular/core';
import { FormControl, UntypedFormControl, UntypedFormGroup, Validators } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';
import {
  getConfigurationStateSuccess,
  updateConfigurationState,
} from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ConfigurationError, ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { HeightWidthProperties, Product, RangeField, Section } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';
import { MatDialogRef } from '@angular/material/dialog';
import { Actions, ofType } from '@ngrx/effects';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { DialogTypesEnum } from '@apto-frontend/src/configs-static/dialog-types-enum';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { combineLatest, take } from 'rxjs';
import { map } from 'rxjs/operators';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { environment } from '@apto-frontend/src/environments/environment';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectConfigurationError } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { ConfirmationDialogComponent } from '@apto-catalog-frontend-confirmation-dialog';

@UntilDestroy()
@Component({
	selector: 'apto-width-height-element',
	templateUrl: './width-height-element.component.html',
	styleUrls: ['./width-height-element.component.scss'],
})
export class WidthHeightElementComponent implements OnInit {
	@Input()
	public element: ProgressElement<HeightWidthProperties> | undefined | null;

  @Input()
  public section: Section | null | undefined;

	@Input()
	public product: Product | null | undefined;

	@Input()
	public isDialog = false;

	public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

  public configurationError: ConfigurationError | null = null;

	public formElement = new UntypedFormGroup({
		height: new UntypedFormControl(0),
		width: new UntypedFormControl(0),
    quantityInput: new FormControl<number>(1),
	});

  protected get hasAttachments(): boolean {
    return this.element.element.attachments?.length !== 0;
  }

	public hasValues(): boolean {
		return this.element ? this.element.state.active : false;
	}

	public stepWidth: number = 1;
	public stepHeight: number = 1;
	public itemsHeight: SelectItem[] = [];
	public itemsWidth: SelectItem[] = [];
  public increaseStep: number | undefined;
  public decreaseStep: number | undefined;

  public getSelectValues(properties: RangeField[]): SelectItem[] {
    const items: SelectItem[] = [];

    properties.map((property: RangeField) => {
      for (let i = property.minimum; i <= property.maximum; i += property.step) {
        items.push({
          surrogateId: '',
          id: `${i}`,
          name: { de_DE: `${i}` },
          isDefault: false,
          aptoPrices: [],
        });
      }
    });

		return items;
	}

	public constructor(
    private store: Store,
    private dialogRef: MatDialogRef<WidthHeightElementComponent>,
    private dialogService: DialogService,
    private readonly actions$: Actions
  ) { }

	public ngOnInit(): void {
		if (!this.element) {
			return;
		}

    this.store.select(selectConfigurationError).subscribe((next) => {
      this.configurationError = next;
    });

    this.initIncreaseDecreaseStep();

		// eslint-disable-next-line dot-notation
		this.formElement.controls['height'].setValue(
			this.element?.state.values.height || this.element.element.definition.staticValues.defaultHeight || 0
		);
		// eslint-disable-next-line dot-notation
		this.formElement.controls['width'].setValue(
			this.element?.state.values.width || this.element.element.definition.staticValues.defaultWidth || 0
		);
    // Always take the first step value to initialize the buttons properly
		if (this.element.element.definition.properties.height && this.element.element.definition.properties.height[0]) {
			this.stepHeight = this.element.element.definition.properties.height?.[0]?.step;
		}
    // Always take the first step value to initialize the buttons properly
		if (this.element.element.definition.properties.width && this.element.element.definition.properties.width[0]) {
			this.stepWidth = this.element.element.definition.properties.width?.[0]?.step;
		}

    this.addRequirementsForInput();

		if (this.element.element.definition.staticValues.renderingHeight === 'select') {
			if (this.element.element.definition.properties.height?.[0]?.maximum) {
        this.itemsHeight = this.getSelectValues(this.element.element.definition.properties.height);
      }
		}

		if (this.element.element.definition.staticValues.renderingWidth === 'select') {
			if (this.element.element.definition.properties.width?.[0]?.maximum) {
        this.itemsWidth = this.getSelectValues(this.element.element.definition.properties.width);
      }
		}
	}

	public saveInput(): void {
		if (!this.element) {
			return;
		}

    this.markAllControlsAsDirty();

    if (!this.formElement.valid) {
      combineLatest(
        this.store.select(selectLocale).pipe(map((l) => l || environment.defaultLocale)),
        this.store.select(selectContentSnippet('aptoStepByStep.elementsContainer.incorrectValuesInRange')),
      ).pipe(take(1)).subscribe((result) => {
        this.dialogService.openCustomDialog(ConfirmationDialogComponent, DialogSizesEnum.md, {
          type: DialogTypesEnum.ERROR,
          hideIcon: true,
          descriptionText: translate(result[1].content, result[0]),
        });
      });

      return;
    }

    this.closeModalOnSuccess();
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

  private initIncreaseDecreaseStep() {
    let increaseStep = this.element.state.customProperties.find((customProperty) => {
      return customProperty.key === 'increaseStep';
    });
    let decreaseStep = this.element.state.customProperties.find((customProperty) => {
      return customProperty.key === 'decreaseStep';
    });

    if (increaseStep && typeof increaseStep.value === 'string') {
      this.increaseStep = parseFloat(increaseStep.value);
    }
    if (decreaseStep && typeof decreaseStep.value === 'string') {
      this.decreaseStep = parseFloat(decreaseStep.value);
    }
  }

  private addRequirementsForInput(): void {
    if (this.element.element.definition.staticValues.renderingHeight === 'input') {
      this.formElement.controls['height'].setValidators([
        Validators.required,
      ]);
    }

    if (this.element.element.definition.staticValues.renderingWidth === 'input') {
      this.formElement.controls['width'].setValidators([
        Validators.required,
      ]);
    }
  }

  private markAllControlsAsDirty(): void {
    Object.keys(this.formElement.controls).forEach((key) => {
      this.formElement.get(key).markAsDirty();
    });
  }

  private closeModalOnSuccess(): void {
    if (this.dialogRef?.id) {
      this.actions$.pipe(
        ofType(getConfigurationStateSuccess),
        untilDestroyed(this)
      ).subscribe((result) => {
        this.dialogRef.close();
      });
    }
  }
}
