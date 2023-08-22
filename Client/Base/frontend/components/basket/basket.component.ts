import {Component} from '@angular/core';
import {BasketService} from '@apto-base-frontend/services/basket.service';
import {selectFullConnector} from '@apto-base-frontend/store/shop/shop.selectors';
import {Store} from '@ngrx/store';
import {selectContentSnippet} from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import {deleteBasketItem} from "@apto-base-frontend/store/shop/shop.actions";
import {DialogService} from "@apto-catalog-frontend/components/common/dialogs/dialog-service";
import {DialogSizesEnum} from "@apto-frontend/src/configs-static/dialog-sizes-enum";
import {environment} from "@apto-frontend/src/environments/environment";
import {translate} from "@apto-base-core/store/translated-value/translated-value.model";

@Component({
	selector: 'apto-basket',
	templateUrl: './basket.component.html',
	styleUrls: ['./basket.component.scss'],
})
export class BasketComponent {
  public readonly csAptoBasket$ = this.store.select(selectContentSnippet('aptoBasket'));
  public readonly csConfirmDeleteDialog$ = this.store.select(selectContentSnippet('aptoBasket.confirmDeleteDialog'));
  public locale: string;

	public toggleSideBar(): void {
		this.basketService.sideBar?.toggle();
	}

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

	public connector$ = this.store.select(selectFullConnector);

	public constructor(private store: Store, private basketService: BasketService, private dialogService: DialogService) {
    this.locale = environment.defaultLocale;
  }

  removeBasketItem(basketItemId: string): void {
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
          dialogMessage = translate(value.content, this.locale);
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
}
