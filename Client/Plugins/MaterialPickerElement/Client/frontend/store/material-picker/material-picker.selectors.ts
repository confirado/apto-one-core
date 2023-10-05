import { createSelector } from "@ngrx/store";
import { featureSelector, MaterialPickerFeatureState } from "@apto-material-picker-element-frontend/store/feature";
import {
  MaterialPickerColor, MaterialPickerItem,
  MaterialPickerPriceGroup,
  PropertyGroup
} from "@apto-catalog-frontend/models/material-picker";

export const selectItems = createSelector(featureSelector, (state: MaterialPickerFeatureState): MaterialPickerItem[] => {
    return state.state.items;
  }
);

export const selectColors = createSelector(featureSelector, (state: MaterialPickerFeatureState): MaterialPickerColor[] => {
    return state.state.colors;
  }
);

export const selectPriceGroups = createSelector(featureSelector, (state: MaterialPickerFeatureState): MaterialPickerPriceGroup[] => {
    return state.state.priceGroups;
  }
);

export const selectPropertyGroups = createSelector(featureSelector, (state: MaterialPickerFeatureState): PropertyGroup[] => {
    return state.state.propertyGroups;
  }
);

export const selectSinglePropertyGroups = createSelector(featureSelector, (state: MaterialPickerFeatureState): PropertyGroup[] => {
    return state.state.propertyGroups.filter((pg) => {
      return pg.allowMultiple === false;
    });
  }
);

export const selectMultiPropertyGroups = createSelector(featureSelector, (state: MaterialPickerFeatureState): PropertyGroup[] => {
    return state.state.propertyGroups.filter((pg) => {
      return pg.allowMultiple === true;
    });
  }
);
