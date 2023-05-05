import { CatalogFeatureState, featureSelector } from '@apto-catalog-frontend/store/feature';
import { createSelector } from '@ngrx/store';

export const selectProduct = createSelector(featureSelector, (state: CatalogFeatureState) => state.product.product);

export const selectProductList = createSelector(featureSelector, (state: CatalogFeatureState) => state.product.productList);
