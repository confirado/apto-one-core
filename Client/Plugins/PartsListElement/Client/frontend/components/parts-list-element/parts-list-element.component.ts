import { Component, Input, OnInit } from '@angular/core';
import { Actions } from '@ngrx/effects';
import { take } from 'rxjs/operators';
import { selectConfiguration } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectConnector } from '@apto-base-frontend/store/shop/shop.selectors';
import { Store } from '@ngrx/store';
import { ConfigurationRepository } from '@apto-catalog-frontend/store/configuration/configuration.repository';
import { combineLatest } from 'rxjs';
import { PartsListPart, ProgressElement } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { updateConfigurationState } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { UntilDestroy, untilDestroyed } from '@ngneat/until-destroy';

@UntilDestroy()
@Component({
  selector: 'apto-parts-list-element',
  templateUrl: './parts-list-element.component.html',
  styleUrls: ['./parts-list-element.component.scss']
})
export class PartsListElementComponent implements OnInit {

  public partsList: PartsListPart[] = [];
  private selectedElements: Partial<PartsListPart>[] = [];

  @Input()
  public element: ProgressElement | undefined | null;

  @Input()
  public product: Product | null | undefined;

  constructor(
    private actions$: Actions,
    private store$: Store,
    private configurationRepository: ConfigurationRepository
  ) { }

  public ngOnInit(): void {
    this.store$.select(selectConfiguration).pipe(untilDestroyed(this)).subscribe(() => {
      this.selectedElements = this.element.state.values.selectedItems || [];
    });
    combineLatest([
      this.store$.select(selectConfiguration),
      this.store$.select(selectConnector)
    ]).pipe(take(1)).subscribe((data) => {
      this.configurationRepository.fetchPartsList({
        productId: data[0].productId as string,
        compressedState: data[0].state.compressedState,
        currency: data[1]?.displayCurrency.currency,
        customerGroupExternalId: data[1]?.customerGroup.id,
        categoryId: this.element.element.definition.staticValues.category
      }).subscribe((response) => {
        this.partsList = response;
      });
    });
  }

  public toggleSelect(part: PartsListPart): void {
    if (this.isSelected(part)) {
      this.selectedElements = this.selectedElements.filter((e) => e.id !== part.id);
      this.saveInformation();
      return;
    }

    const element = {
      id: part.id,
      partNumber: part.partNumber,
      partName: part.partName,
      unit: part.unit
    };

    if (this.isMultiSelect()) {
      this.selectedElements = [...this.selectedElements, element];
    } else {
      this.selectedElements = [element];
    }

    this.saveInformation();
  }

  public isSelected(part: PartsListPart): boolean {
    return !!this.selectedElements.find((e) => e.id === part.id);
  }

  private saveInformation(): void {
    this.store$.dispatch(
      updateConfigurationState({
        updates: {
          set: [
            {
              sectionId: this.element.element.sectionId,
              elementId: this.element.element.id,
              sectionRepetition: this.element.state.sectionRepetition,
              property: 'aptoElementDefinitionId',
              value: 'apto-parts-list-element',
            },
            {
              sectionId: this.element.element.sectionId,
              elementId: this.element.element.id,
              sectionRepetition: this.element.state.sectionRepetition,
              property: 'selectedItems',
              value: this.selectedElements,
            },
          ],
        },
      })
    );
  }

  private isMultiSelect(): boolean {
    return this.element.element.definition.staticValues.allowMultiple
  }
}
