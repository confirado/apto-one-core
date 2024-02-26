import { CatalogFeatureState, featureSelector } from '@apto-catalog-frontend/store/feature';
import { createSelector } from '@ngrx/store';
import { RuleRepairSettings } from '@apto-catalog-frontend/store/product/product.model';
import { Element } from '@apto-catalog-frontend/store/product/product.model';

export const selectProduct = createSelector(featureSelector, (state: CatalogFeatureState) => state.product.product);
export const selectElement = (id: string) => createSelector(featureSelector,
  (state: CatalogFeatureState) => state.product.elements.find((e: Element) => e.id === id)
);
export const selectElements = (ids: string[]) => createSelector(featureSelector,
  (state: CatalogFeatureState) => state.product.elements.filter((e: Element) => ids.includes(e.id))
);

export const selectProductList = createSelector(featureSelector, (state: CatalogFeatureState) => state.product.productList);

export const selectProductListLoading = createSelector(featureSelector, (state: CatalogFeatureState) => state.product.loading);

export const selectRuleRepairSettings = createSelector(featureSelector, (state: CatalogFeatureState): RuleRepairSettings | null => {
  let repairSettings = null;
  if (!state.product.product) {
    return null;
  }

  state.product.product.customProperties.every((cp) => {
    if (cp.key === 'ruleRepairSettings' && false === cp.translatable && typeof cp.value === 'string') {
      repairSettings = JSON.parse(cp.value);
      return false;
    }
    return true;
  });

  return repairSettings;
});
