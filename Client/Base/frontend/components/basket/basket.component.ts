import { Component } from '@angular/core';
import { BasketService } from '@apto-base-frontend/services/basket.service';
import { selectFullConnector, selectShopLoading } from '@apto-base-frontend/store/shop/shop.selectors';
import { Store } from '@ngrx/store';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { deleteBasketAllItems, deleteBasketItem } from '@apto-base-frontend/store/shop/shop.actions';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { environment } from '@apto-frontend/src/environments/environment';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectConfigurationLoading } from '@apto-catalog-frontend-configuration-selectors';
import { LoadingIndicatorComponent, LoadingIndicatorTypes } from '@apto-base-core/components/common/loading-indicator/loading-indicator.component';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { map, take } from 'rxjs/operators';
import { combineLatest } from 'rxjs';

@Component({
	selector: 'apto-basket',
	templateUrl: './basket.component.html',
	styleUrls: ['./basket.component.scss'],
})
export class BasketComponent {

  public readonly csAptoBasket$ = this.store.select(selectContentSnippet('aptoBasket'));
  public readonly csConfirmDeleteDialog$ = this.store.select(selectContentSnippet('aptoBasket.confirmDeleteDialog'));
  public readonly csClearCartConfirmation$ = this.store.select(selectContentSnippet('aptoBasket.clearCartConfirmation'));
  protected readonly loadingIndicatorTypes = LoadingIndicatorTypes;
  public readonly configurationLoading$ = this.store.select(selectConfigurationLoading);
  public readonly connector$ = this.store.select(selectFullConnector);
  public readonly selectShopLoading$ = this.store.select(selectShopLoading);

  public locale: string;

  public toggleSideBar(): void {
    this.basketService.sideBar?.toggle();
  }

  public constructor(
    private store: Store,
    private basketService: BasketService,
    private dialogService: DialogService
  ) {
    this.locale = environment.defaultLocale;
  }

  protected removeBasketItem(basketItemId: string, basketItemName: string): void {
    let dialogMessage = '';
    let dialogTitle = '';
    let dialogButtonCancel = '';
    let dialogButtonAccept = '';

    const confirmDialogSubscription = this.csConfirmDeleteDialog$.subscribe((next) => {
      if (next === null) {
        this.store.dispatch(deleteBasketItem({ payload: { basketItemId: basketItemId } }));
        confirmDialogSubscription.unsubscribe();
        return;
      }
      next.children.forEach((value) => {
        if (value.name === 'title') {
          dialogTitle = translate(value.content, this.locale);
        }
        if (value.name === 'text') {
          dialogMessage = translate(
            value.content,
            this.locale
          ).replace(
            '{_productName_}',
            basketItemName
          );
        }
        if (value.name === 'cancel') {
          dialogButtonCancel = translate(value.content, this.locale);
        }
        if (value.name === 'confirm') {
          dialogButtonAccept = translate(value.content, this.locale);
        }
      });
      this.dialogService
        .openWarningDialog(
          DialogSizesEnum.md,
          dialogTitle,
          dialogMessage,
          dialogButtonCancel,
          dialogButtonAccept
        )
        .afterClosed()
        .subscribe((next) => {
          if (next === true) {
            this.store.dispatch(deleteBasketItem({ payload: { basketItemId: basketItemId } }));
          }
          confirmDialogSubscription.unsubscribe();
        });
    });
  }

  protected removeAllBasketItems(basketItems: any[]): void {
    combineLatest(
      this.store.select(selectLocale).pipe(map((l) => l || environment.defaultLocale)),
      this.store.select(selectContentSnippet('aptoBasket.clearCartConfirmation.confirm')),
      this.store.select(selectContentSnippet('aptoBasket.clearCartConfirmation.cancel')),
      this.store.select(selectContentSnippet('aptoBasket.clearCartConfirmation.title')),
      this.store.select(selectContentSnippet('aptoBasket.clearCartConfirmation.text'))
    ).pipe(take(1)).subscribe((result) => {
      const lang = result[0];
      const dialogButtonAccept = translate(result[1].content, lang);
      const dialogButtonCancel = translate(result[2].content, lang);
      const dialogTitle = translate(result[3].content, lang);
      const dialogMessage = translate(result[4].content, this.locale).replace('{_productName_}', basketItems.map(item => item.name).join(', '));

      this.dialogService
        .openWarningDialog(
          DialogSizesEnum.md,
          dialogTitle,
          dialogMessage,
          dialogButtonCancel,
          dialogButtonAccept
        )
        .afterClosed()
        .subscribe((next) => {
          if (next) {
            this.store.dispatch(deleteBasketAllItems({ payload: { basketItemIds: basketItems.map((item) => item.id) } }));
          }
        });
    });
  }

  // @todo is this needed, seems not to be used?
  public totalPrice(
    products: {
      name: string;
      price: number;
      currency: string;
      quantity: number;
      image: string;
    }[]
  ): string {
    let totalPrice = 0;
    let totalCurrency;
    for (const product of products) {
      const currentPrice = product.quantity * product.price;
      totalPrice += currentPrice;
      totalCurrency = product.currency;
    }
    if (!totalCurrency) {
      return 'no currency found!';
    }
    return `${totalPrice.toString()} ${totalCurrency}`;
  }
}
