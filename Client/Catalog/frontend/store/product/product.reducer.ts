import {
  ConfigurationActionTypes,
  initConfiguration,
  initConfigurationSuccess,
  setNextStep, setPrevStep, setStep
} from '@apto-catalog-frontend/store/configuration/configuration.actions';
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
	})),
  /*  This is a workaround because, in apto-sbs-elements component, we iterate over product sections to prevent to much state changes and rerenderings
      a better solution would be to make a separate state for the elements we have to iterate in apto-sbs-elements component
      We need to know when we move between sections and react on it. Each time we change the section, we need
      to trigger a state change in product elements.  */
  on(setNextStep, setPrevStep, setStep, (state, action) => {
    const elements: Element[] = [];

    const configurationStepTypes = [
      ConfigurationActionTypes.SetStep,
      ConfigurationActionTypes.SetPrevStep,
      ConfigurationActionTypes.SetNextStep,
    ];

    state.elements.forEach((e) => {
      elements.push({
        ...e,
        sectionRepetition: configurationStepTypes.includes(action.type) ? action.payload.repetition : 0,
      });
    });
    return {
      ...state,
      elements,
    };
  })
);

export function productReducer(state: ProductState | undefined, action: Action): ProductState {
	return _productReducer(state, action);
}
