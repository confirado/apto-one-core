import { Injectable } from '@angular/core';
import { loadProductList, loadProductListSuccess } from '@apto-catalog-frontend/store/product/product.actions';
import { ProductRepository } from '@apto-catalog-frontend/store/product/product.repository';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { map, switchMap } from 'rxjs/operators';

@Injectable()
export class ProductEffects {
	public constructor(private actions$: Actions, private productRepository: ProductRepository) {}

	public loadProductList$ = createEffect(() =>
		this.actions$.pipe(
			ofType(loadProductList),
			switchMap(() => this.productRepository.findProductsByFilter()),
			map((productList) => loadProductListSuccess({ payload: productList }))
		)
	);
}
