import { BaseFeatureState, featureSelector } from '@apto-base-frontend/store/feature';
import { SelectConnector } from '@apto-base-frontend/store/shop/shop.model';
import { environment } from '@apto-frontend/src/environments/environment';
import { createSelector } from '@ngrx/store';

export const selectShop = createSelector(featureSelector, (state: BaseFeatureState) => state.shop.shop);
export const selectFullConnector = createSelector(featureSelector, (state: BaseFeatureState) => state.shop.connector);
export const selectShopLoading = createSelector(featureSelector, (state: BaseFeatureState) => state.shop.loading);

export const selectConnector = createSelector(
	featureSelector,
	(state: BaseFeatureState): SelectConnector => ({
		taxState: state.shop.connector.taxState,
		shopCurrency: state.shop.connector.shopCurrency,
		displayCurrency: state.shop.connector.displayCurrency,
		customerGroupExternalId: state.shop.connector.customerGroupExternalId,
		customerGroup: state.shop.connector.customerGroup,
		sessionCookies: state.shop.connector.sessionCookies,
		locale: state.language.locale ? state.language.locale : environment.defaultLocale,
    configured: state.shop.connector.configured,
    user: state.shop.connector.user,
    loggedIn: state.shop.connector.loggedIn
	})
);

export const selectArticleQuantity = createSelector(featureSelector, (state: BaseFeatureState ) => {
  let totalQuantity = 0;
  let articles = state.shop.connector.basket.articles;

  for (let i = 0; i < articles.length; i++) {
    totalQuantity += articles[i].quantity;
  }

  return totalQuantity;
});
