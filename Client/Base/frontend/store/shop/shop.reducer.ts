import { initShop, initShopSuccess } from '@apto-base-frontend/store/shop/shop.actions';
import { Connector, Shop } from '@apto-base-frontend/store/shop/shop.model';
import { environment } from '@apto-frontend/src/environments/environment';
import { Action, createReducer, on } from '@ngrx/store';

export interface ShopState {
	shop: Shop | null;
	connector: Connector;
	loading: boolean;
}

export const shopInitialState: ShopState = {
	shop: null,
	connector: {
		sessionCookies: [],
		user: null,
		loggedIn: false,
		taxState: 'gross',
		customerGroup: environment.defaultCustomerGroup,
		displayCurrency: environment.defaultCurrency.displayCurrency,
		shopCurrency: environment.defaultCurrency.shopCurrency,
		basket: {
			amount: '0,00 €',
			quantity: 0,
			articles: [],
		},
		customerGroupExternalId: null,
	},
	loading: false,
};

const _shopReducer = createReducer(
	shopInitialState,
	on(initShop, (state) => {
		return {
			...state,
			loading: true,
		};
	}),
	on(initShopSuccess, (state, action) => {
		return {
			...state,
			shop: action.payload.shop,
			connector: action.payload.connector,
			loading: false,
		};
	})
);

export function shopReducer(state: ShopState | undefined, action: Action) {
	return _shopReducer(state, action);
}
