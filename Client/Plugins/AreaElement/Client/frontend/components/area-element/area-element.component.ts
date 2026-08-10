import { Component, Input, OnInit } from '@angular/core';
import { UntypedFormControl, UntypedFormGroup, Validators } from '@angular/forms';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { SelectItem } from '@apto-catalog-frontend/models/select-items';
import {
  getConfigurationStateSuccess,
  updateConfigurationState,
} from '@apto-catalog-frontend-configuration-actions';
import {
  AreaElementDefinitionProperties,
  ConfigurationError,
  ProgressElement,
} from '@apto-catalog-frontend-configuration-model';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';
import { MatDialogRef } from '@angular/material/dialog';
import { Actions, ofType } from '@ngrx/effects';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';
import { combineLatest, take } from 'rxjs';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { map } from 'rxjs/operators';
import { environment } from '@apto-frontend/src/environments/environment';
import {
  ConfirmationDialogComponent
} from '@apto-catalog-frontend-confirmation-dialog';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { DialogTypesEnum } from '@apto-frontend/src/configs-static/dialog-types-enum';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { selectConfigurationError } from '@apto-catalog-frontend-configuration-selectors';

/**
 * Diskriminiertes Range-Bound-Format vom Backend:
 * entweder eine feste Zahl (Altdaten / bereits aufgeloest) oder ein Objekt,
 * das entweder einen festen Wert oder eine (noch nicht zwingend aufgeloeste)
 * Computed-Value-Referenz beschreibt.
 */
type RangeBound = number | { type: string; value?: number; name?: string } | null | undefined;

@UntilDestroy()
@Component({
  selector: 'apto-area-element',
  templateUrl: './area-element.component.html',
  styleUrls: ['./area-element.component.scss'],
})
export class AreaElementComponent implements OnInit {
  @Input()
  public element: ProgressElement<AreaElementDefinitionProperties> | undefined | null;

  @Input()
  public section: Section | undefined;

  @Input()
  public product: Product | null | undefined;

  @Input()
  public isDialog = false;

  public formElement = new UntypedFormGroup({});

  public readonly contentSnippet$ = this.store.select(selectContentSnippet('aptoDefaultElementDefinition'));

  public configurationError: ConfigurationError | null = null;

  public itemFieldList: SelectItem[][] = [];

  public sumOfFieldValues = 0;
  public increaseStep: number | undefined;
  public decreaseStep: number | undefined;

  public constructor(
      private store: Store,
      private dialogRef: MatDialogRef<AreaElementComponent>,
      private dialogService: DialogService,
      private readonly actions$: Actions
  ) {}

  /**
   * Unwrapped das diskriminierte Bound-Format vom Backend.
   * Gibt null zurueck, wenn kein sinnvoller Zahlenwert vorhanden ist
   * (z.B. eine Computed-Value-Referenz, die aktuell noch nicht aufloesbar ist).
   */
  public resolveBound(bound: RangeBound): number | null {
    if (bound === null || bound === undefined) {
      return null;
    }

    if (typeof bound === 'object') {
      if (bound.type === 'fixed' && typeof bound.value === 'number') {
        return bound.value;
      }
      // 'computed' und (noch) unaufgeloest: kein sinnvoller Zahlenwert anzeigbar
      return null;
    }

    return bound;
  }

  /**
   * Anzeige-String fuer die Wertebereich-Hinweise im Template.
   * Zeigt lieber nichts an, statt "[object Object]" bei unaufgeloesten Referenzen.
   */
  public displayRange(fieldValues: { minimum: RangeBound; maximum: RangeBound }, suffix: string): string {
    const min = this.resolveBound(fieldValues.minimum);
    const max = this.resolveBound(fieldValues.maximum);

    if (min === null || max === null) {
      return '';
    }

    return `${min} - ${max} ${suffix}`.trim();
  }

  public getSelectValues(min: RangeBound, max: RangeBound, step: number): SelectItem[] {
    const resolvedMin = this.resolveBound(min);
    const resolvedMax = this.resolveBound(max);

    if (resolvedMin === null || resolvedMax === null) {
      return [];
    }

    const items: SelectItem[] = [];
    for (let i = resolvedMin; i <= resolvedMax; i += step) {
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

    this.store.select(selectConfigurationError).subscribe((next) => {
      this.configurationError = next;
    });

    this.initIncreaseDecreaseStep();

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
          const entry = this.element.element.definition.properties[`field_${i}`][index];
          const resolvedMax = this.resolveBound(entry.maximum);

          if (resolvedMax !== null) {
            itemField = this.getSelectValues(
                entry.minimum,
                entry.maximum,
                entry.step
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
      if (this.hasOnlySelectInputFields()) {
        this.saveInput();
      }
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

  public hasOnlySelectInputFields(): boolean {
    const fields = this.element.element.definition.staticValues.fields;
    for (let i = 0; i < fields.length; i++) {
      if (fields[i].rendering !== 'select') {
        return false;
      }
    }
    return true;
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
