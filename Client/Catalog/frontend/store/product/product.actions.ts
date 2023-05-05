import { Product } from '@apto-catalog-frontend/store/product/product.model';
import { createAction, props } from '@ngrx/store';

// eslint-disable-next-line no-shadow
export enum ProductActionTypes {
	LoadProductList = '[Product List] Load Product List',
	LoadProductListSuccess = '[Product List] Load Product List success',
}

export const loadProductList = createAction(ProductActionTypes.LoadProductList);

export const loadProductListSuccess = createAction(ProductActionTypes.LoadProductListSuccess, props<{ payload: Product[] }>());
