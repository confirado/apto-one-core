import { BaseFeatureState, featureSelector } from "@apto-base-frontend/store/feature";
import { createSelector } from "@ngrx/store";

export const selectLanguages = createSelector(featureSelector,
  (state: BaseFeatureState) => {
    return state.language.languages;
  }
);

export const selectLocale = createSelector(featureSelector,
  (state: BaseFeatureState) => {
    return state.language.locale;
  }
);