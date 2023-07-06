import { createSelector } from '@ngrx/store';
import { BaseFeatureState, featureSelector } from '@apto-base-frontend/store/feature';

export const selectIsLoggedIn = createSelector(featureSelector, (state: BaseFeatureState) => {
  return state.frontendUser.currentUser ? state.frontendUser.currentUser.isLoggedIn : false;
});

export const selectCurrentUser = createSelector(featureSelector, (state: BaseFeatureState) => {
  return state.frontendUser.currentUser ? state.frontendUser.currentUser : null;
});

export const selectLoginError = createSelector(featureSelector, (state: BaseFeatureState) => {
  return state.frontendUser.loginError;
});
