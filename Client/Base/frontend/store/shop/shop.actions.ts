import { createAction, props } from "@ngrx/store";
import { Connector, Shop } from "@apto-base-frontend/store/shop/shop.model";
import { Language } from "@apto-base-frontend/store/language/language.model";
import { ContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippet.model";

export enum ShopActionTypes {
  InitShop = '[Shops] Init Shop',
  InitShopSuccess = '[Shops] Init Shop success',
  DeleteBasketItem = '[Shops] Delete Basket Item',
  DeleteBasketAllItems = '[Shops] Delete Basket All Items',
  DeleteBasketItemSuccess = '[Shops] Delete Basket Item Success',
}

export const initShop = createAction(
  ShopActionTypes.InitShop
);

export const initShopSuccess = createAction(
  ShopActionTypes.InitShopSuccess,
  props<{ payload: { shop: Shop, languages: Language[], locale: string, contentSnippets: ContentSnippet[], connector: Connector }; }>()
);

export const deleteBasketItem = createAction(
  ShopActionTypes.DeleteBasketItem,
  props<{ payload: { basketItemId: string } }>()
);

export const deleteBasketAllItems = createAction(
  ShopActionTypes.DeleteBasketAllItems,
  props<{ payload: { basketItemIds: string[] } }>()
);

export const deleteBasketItemSuccess = createAction(
  ShopActionTypes.DeleteBasketItemSuccess
);
