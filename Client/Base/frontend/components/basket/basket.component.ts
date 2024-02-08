/**
 * Component for managing the basket functionality.
 * This Angular component is responsible for handling various aspects of the shopping basket, including
 * displaying items, handling user interactions, and managing the state of the basket.
 */

import { Component } from '@angular/core';

// This service provides methods to interact with the basket.
import { BasketService } from '@apto-base-frontend/services/basket.service';

// These selectors allow the component to retrieve specific pieces of data from the Redux store.
import { selectFullConnector, selectShopLoading } from '@apto-base-frontend/store/shop/shop.selectors';

// This enables the component to dispatch actions and access the store's state.
import { Store } from '@ngrx/store';

// This selector is used to retrieve content snippets related to the basket for display.
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';

// This action is dispatched when the user initiates the removal of an item from the basket.
import { deleteBasketItem } from '@apto-base-frontend/store/shop/shop.actions';

// The DialogService is used to display warning dialogs to confirm certain actions.
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';

// This enum provides predefined sizes for the warning dialogs displayed by the DialogService.
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';

// The default locale is used to set the initial locale for the component.
import { environment } from '@apto-frontend/src/environments/environment';

// The translate function is used to translate content snippets and messages based on the selected locale.
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';

// This selector is used to determine whether the configuration is still loading, indicating that the component should wait before displaying certain information.
import { selectConfigurationLoading } from '@apto-catalog-frontend/store/configuration/configuration.selectors';

// The LoadingIndicatorComponent is used to visually indicate when certain operations are in progress.
import { LoadingIndicatorComponent, LoadingIndicatorTypes } from '@apto-base-core/components/common/loading-indicator/loading-indicator.component';

@Component({
	selector: 'apto-basket',
	templateUrl: './basket.component.html',
	styleUrls: ['./basket.component.scss'],
})
export class BasketComponent {
  /**
   * Observable emitting the content snippet for the aptoBasket.
   */
  public readonly csAptoBasket$ = this.store.select(selectContentSnippet('aptoBasket'));

  /**
   * Observable emitting the content snippet for the confirm delete dialog.
   */
  public readonly csConfirmDeleteDialog$ = this.store.select(selectContentSnippet('aptoBasket.confirmDeleteDialog'));

  /**
   * Observable emitting the content snippet for the clear cart confirmation dialog.
   */
  public readonly csClearCartConfirmation$ = this.store.select(selectContentSnippet('aptoBasket.clearCartConfirmation'));

  /**
   * Types of loading indicators.
   */
  protected readonly loadingIndicatorTypes = LoadingIndicatorTypes;

  /**
   * Observable emitting the loading state of the configuration.
   */
  public readonly configurationLoading$ = this.store.select(selectConfigurationLoading);

  /**
   * Observable emitting the full connector.
   */
  public readonly connector$ = this.store.select(selectFullConnector);

  /**
   * Observable emitting the loading state of the shop.
   */
  public readonly selectShopLoading$ = this.store.select(selectShopLoading);

  /**
   * Default locale.
   */
  public locale: string;

  /**
   * Toggles the sidebar.
   */
  public toggleSideBar(): void {
    this.basketService.sideBar?.toggle();
  }

  /**
   * Constructor for BasketComponent.
   * @param store Store service for managing state.
   * @param basketService BasketService for managing basket functionality.
   * @param dialogService DialogService for opening dialogs.
   */
  public constructor(
    private store: Store,
    private basketService: BasketService,
    private dialogService: DialogService
  ) {
    this.locale = environment.defaultLocale; // Initializing locale with the default value from environment.
  }

  /**
   * Removes a basket item.
   * @param basketItemId The ID of the basket item to remove.
   * @param basketItemName The name of the basket item to remove.
   */
  protected removeBasketItem(basketItemId: string, basketItemName: string): void {
    let dialogMessage = ''; // Message to display in the dialog.
    let dialogTitle = ''; // Title of the dialog.
    let dialogButtonCancel = ''; // Label for the cancel button.
    let dialogButtonAccept = ''; // Label for the accept button.

    const confirmDialogSubscription = this.csConfirmDeleteDialog$.subscribe((next) => {
      if (next === null) {
        this.store.dispatch(deleteBasketItem({ payload: { basketItemId: basketItemId } })); // Dispatching action to delete basket item.
        confirmDialogSubscription.unsubscribe(); // Unsubscribing from the confirmation dialog subscription.
        return;
      }
      next.children.forEach((value) => {
        if (value.name === 'title') {
          dialogTitle = translate(value.content, this.locale); // Translating dialog title.
        }
        if (value.name === 'text') {
          dialogMessage = translate(
            value.content,
            this.locale
          ).replace(
            '{_productName_}',
            basketItemName
          ); // Translating dialog message and replacing placeholder with basket item name.
        }
        if (value.name === 'cancel') {
          dialogButtonCancel = translate(value.content, this.locale); // Translating cancel button label.
        }
        if (value.name === 'confirm') {
          dialogButtonAccept = translate(value.content, this.locale); // Translating accept button label.
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
            this.store.dispatch(deleteBasketItem({ payload: { basketItemId: basketItemId } })); // Dispatching action to delete basket item.
          }
          confirmDialogSubscription.unsubscribe(); // Unsubscribing from the confirmation dialog subscription.
        });
    });
  }

  /**
   * Removes all basket items.
   * @param basketItems An array of basket items to remove.
   */
  protected removeAllBasketItems(basketItems: any[]): void {
    let dialogMessage = ''; // Message to display in the dialog.
    let dialogTitle = ''; // Title of the dialog.
    let dialogButtonCancel = ''; // Label for the cancel button.
    let dialogButtonAccept = ''; // Label for the accept button.

    const confirmDialogSubscription = this.csClearCartConfirmation$.subscribe((next) => {
      if (next === null) {
        basketItems.forEach(item => {
          this.store.dispatch(deleteBasketItem({ payload: { basketItemId: item.id } })); // Dispatching action to delete basket item.
        });
        confirmDialogSubscription.unsubscribe(); // Unsubscribing from the confirmation dialog subscription.
        return;
      }
      next.children.forEach((value) => {
        if (value.name === 'title') {
          dialogTitle = translate(value.content, this.locale); // Translating dialog title.
        }
        if (value.name === 'text') {
          dialogMessage = translate(
            value.content,
            this.locale
          ).replace(
            '{_productName_}',
            basketItems.map(item => item.name).join(', ')
          ); // Translating dialog message and replacing placeholder with names of basket items.
        }
        if (value.name === 'cancel') {
          dialogButtonCancel = translate(value.content, this.locale); // Translating cancel button label.
        }
        if (value.name === 'confirm') {
          dialogButtonAccept = translate(value.content, this.locale); // Translating accept button label.
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
            basketItems.forEach(item => {
              this.store.dispatch(deleteBasketItem({ payload: { basketItemId: item.id } })); // Dispatching action to delete basket item.
            });
          }
          confirmDialogSubscription.unsubscribe(); // Unsubscribing from the confirmation dialog subscription.
        });
    });
  }

  // @todo is this needed, seems not to be used?
  /**
   * Calculates the total price of products.
   * @param products An array of products with name, price, currency, quantity, and image.
   * @returns A string representing the total price with currency.
   */
  public totalPrice(
    products: {
      name: string;
      price: number;
      currency: string;
      quantity: number;
      image: string;
    }[]
  ): string {
    let totalPrice = 0; // Total price of all products.
    let totalCurrency; // Currency of the products.
    for (const product of products) {
      const currentPrice = product.quantity * product.price; // Calculating price for the current product.
      totalPrice += currentPrice; // Adding current product's price to total price.
      totalCurrency = product.currency; // Setting currency from current product.
    }
    if (!totalCurrency) {
      return 'no currency found!'; // Returning error message if currency is not found.
    }
    return `${totalPrice.toString()} ${totalCurrency}`; // Returning total price with currency.
  }
}
