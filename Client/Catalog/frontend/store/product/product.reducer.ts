import { initConfiguration, initConfigurationSuccess } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { loadProductList, loadProductListSuccess } from '@apto-catalog-frontend/store/product/product.actions';
import { Element, Group, Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { Action, createReducer, on } from '@ngrx/store';

export interface ProductState {
	product: Product | null;
	groups: Group[];
	sections: Section[];
	elements: Element[];
	productList: Product[];
	loading: boolean;
}

export const productInitialState: ProductState = {
	product: null,
	groups: [],
	sections: [],
	elements: [],
	productList: [],
	loading: false,
};

const _productReducer = createReducer(
	productInitialState,
	on(initConfiguration, (state) => ({
		...state,
		loading: true,
	})),
	on(initConfigurationSuccess, (state, action) => ({
		...state,
		product: action.payload.product,
		groups: action.payload.groups,
		sections: action.payload.sections,
		elements: action.payload.elements,
		loading: false,
	})),
	on(loadProductList, (state) => ({
		...state,
		loading: true,
	})),
	on(loadProductListSuccess, (state, action) => ({
		...state,
		productList: action.payload,
		loading: false,
	}))
);

export function productReducer(state: ProductState | undefined, action: Action): ProductState {
	return _productReducer(state, action);
}
