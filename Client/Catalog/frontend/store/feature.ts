import { configurationReducer, ConfigurationState } from '@apto-catalog-frontend-configuration-reducer';
import { productReducer, ProductState } from '@apto-catalog-frontend/store/product/product.reducer';
import { Action, ActionReducerMap, createFeatureSelector } from '@ngrx/store';

export interface CatalogFeatureState {
	product: ProductState;
	configuration: ConfigurationState;
}

export const featureKey = 'aptoCatalog';
export const featureSelector = createFeatureSelector<CatalogFeatureState>(featureKey);

export interface PayloadAction extends Action {
	payload: any;
}

export const reducers: ActionReducerMap<CatalogFeatureState, PayloadAction> = {
	product: productReducer,
	configuration: configurationReducer
};

// @todo: we have a loading flag for product and content snippets, is it possible with rxjs, to react to all actions that set a loading flag?
// @todo: e.g. for a global loading spinner, a stream that gives us always the last loading flag of all actions
