import { Injectable } from '@angular/core';
import { ContentSnippetRepository } from '@apto-base-frontend/store/content-snippets/content-snippet.repository';
import { initShop, initShopSuccess } from '@apto-base-frontend/store/shop/shop.actions';
import { shopInitialState } from '@apto-base-frontend/store/shop/shop.reducer';
import { ShopRepository } from '@apto-base-frontend/store/shop/shop.repository';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { forkJoin, iif, of } from 'rxjs';
import { map, switchMap } from 'rxjs/operators';

@Injectable()
export class ShopEffects {
	public constructor(
		private actions$: Actions,
		private shopRepository: ShopRepository,
		private contentSnippetRepository: ContentSnippetRepository
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
}
