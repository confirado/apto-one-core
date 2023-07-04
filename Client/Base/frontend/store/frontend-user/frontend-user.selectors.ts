import { createSelector } from '@ngrx/store';
import { BaseFeatureState, featureSelector } from '@apto-base-frontend/store/feature';

export const selectIsLoggedIn = createSelector(featureSelector, (state: BaseFeatureState) => {
  return state.frontendUser.currentUser ? state.frontendUser.currentUser.isLoggedIn : false;
});
