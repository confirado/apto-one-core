import { createAction, props } from "@ngrx/store";
import { Connector, Shop } from "@apto-base-frontend/store/shop/shop.model";
import { Language } from "@apto-base-frontend/store/language/language.model";
import { ContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippet.model";

export enum ShopActionTypes {
  InitShop        = '[Shops] Init Shop',
  InitShopSuccess = '[Shops] Init Shop success',
  DeleteBasketItem = '[Shops] Delete Basket Item'
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
