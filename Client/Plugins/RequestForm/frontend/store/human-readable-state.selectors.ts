import { createSelector } from '@ngrx/store';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { CatalogFeatureState, featureSelector } from '@apto-catalog-frontend/store/feature';
import { selectHumanReadableState as selectConfigurationHumanReadableState } from '@apto-catalog-frontend-configuration-selectors';
import { getHumanReadableFullState } from '@apto-catalog-frontend/services/store-utilities';

export const selectHumanReadableState = createSelector(featureSelector, selectLocale, selectConfigurationHumanReadableState, (state: CatalogFeatureState, locale: string | null, configurationHumanReadableState) => {
  return getHumanReadableFullState(state, locale, configurationHumanReadableState);
});
