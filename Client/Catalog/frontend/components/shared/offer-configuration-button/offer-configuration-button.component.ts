import { Component, Input } from '@angular/core';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { Store } from '@ngrx/store';
import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { OfferConfigurationDialogComponent } from '@apto-catalog-frontend/components/common/dialogs/offer-configuration-dialog/offer-configuration-dialog.component';

@Component({
	selector: 'apto-offer-configuration-button',
	templateUrl: './offer-configuration-button.component.html',
	styleUrls: ['./offer-configuration-button.component.scss'],
})

export class OfferConfigurationButtonComponent {

  @Input()
  public product: Product | null | undefined;

  protected readonly aptoSummary$ = this.store.select(selectContentSnippet('aptoSummary'));
  protected readonly sidebarSummary$ = this.store.select(selectContentSnippet('aptoStepByStep.sidebarSummary'));
  protected readonly aptoSliderAction$ = this.store.select(selectContentSnippet('aptoSliderAction'));

  public constructor(
    private store: Store,
    private dialogService: DialogService
  ) { }

  protected openModal(): void {
    this.dialogService.openCustomDialog(OfferConfigurationDialogComponent, DialogSizesEnum.md, { productId: this.product.id });
  }
}
