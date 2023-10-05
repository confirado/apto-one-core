import { Injectable } from '@angular/core';
import { ContentSnippetRepository } from '@apto-base-frontend/store/content-snippets/content-snippet.repository';
import { deleteBasketItem, deleteBasketItemSuccess, initShop, initShopSuccess } from '@apto-base-frontend/store/shop/shop.actions';
import { shopInitialState } from '@apto-base-frontend/store/shop/shop.reducer';
import { ShopRepository } from '@apto-base-frontend/store/shop/shop.repository';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { forkJoin, iif, of } from 'rxjs';
import {map, switchMap, withLatestFrom} from 'rxjs/operators';
import {Store} from "@ngrx/store";
import {selectShop} from "@apto-base-frontend/store/shop/shop.selectors";
import {translate} from "@apto-base-core/store/translated-value/translated-value.model";
import {selectLocale} from "@apto-base-frontend/store/language/language.selectors";

@Injectable()
export class ShopEffects {
	public constructor(
		private actions$: Actions,
		private shopRepository: ShopRepository,
		private contentSnippetRepository: ContentSnippetRepository,
    private store$: Store
	) {}

	public loadShops$ = createEffect(() =>
		this.actions$.pipe(
			ofType(initShop),
			switchMap(() => forkJoin([this.shopRepository.findShopContext(), this.contentSnippetRepository.findContentSnippetTree()])),
			switchMap((result) =>
				iif(
					() => !!result[0].shop.connectorUrl[result[0].locale],
					this.shopRepository.getConnectorState(result[0].shop.connectorUrl[result[0].locale]).pipe(
						map((connector) => ({
							...result[0],
							contentSnippets: result[1],
							connector,
						}))
					),
					of({
						...result[0],
						contentSnippets: result[1],
						connector: shopInitialState.connector,
					})
				)
			),
			map((result) =>
				initShopSuccess({
					payload: {
						shop: result.shop,
						languages: result.languages,
						locale: result.locale,
						contentSnippets: result.contentSnippets,
						connector: result.connector,
					},
				})
			)
		)
	);

  public deleteBasketItem$ = createEffect(() =>
    this.actions$.pipe(
      ofType(deleteBasketItem),
      withLatestFrom(this.store$.select(selectShop), this.store$.select(selectLocale)),
      switchMap(([action, shop, language]) => {
        return this.shopRepository.deleteBasketItem(
          translate(shop.connectorUrl, language),
          action.payload.basketItemId
        );
      }),
      switchMap(result => [
        //@todo: initshop has too many queries that are not needed. We need to create a new action "updateShop",
        //@todo: in which only the "getConnectorState" is queried.
        deleteBasketItemSuccess(),
        initShop(),
      ]),
    )
  );
}


