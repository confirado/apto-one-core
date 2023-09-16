import { CatalogFeatureState, featureSelector } from '@apto-catalog-frontend/store/feature';
import { createSelector } from '@ngrx/store';
import { RuleRepairSettings } from '@apto-catalog-frontend/store/product/product.model';

export const selectProduct = createSelector(featureSelector, (state: CatalogFeatureState) => state.product.product);

export const selectProductList = createSelector(featureSelector, (state: CatalogFeatureState) => state.product.productList);

export const selectRuleRepairSettings = createSelector(featureSelector, (state: CatalogFeatureState): RuleRepairSettings | null => {
  let repairSettings = null;
  if (!state.product.product) {
    return null;
  }

  state.product.product.customProperties.every((cp) => {
    if (cp.key === 'ruleRepairSettings' && false === cp.translatable) {
      repairSettings = JSON.parse(cp.value);
      return false;
    }
    return true;
  });

  return repairSettings;
});
