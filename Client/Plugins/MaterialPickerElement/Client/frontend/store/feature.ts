import { ActionReducerMap, createFeatureSelector } from '@ngrx/store';
import { PayloadAction } from '@apto-catalog-frontend/store/feature';
import { materialPickerReducer, MaterialPickerState } from "@apto-material-picker-element-frontend/store/material-picker/material-picker.reducer";

export const featureKey = 'aptoMaterialPicker';

export interface MaterialPickerFeatureState {
  state: MaterialPickerState
}

export const featureSelector = createFeatureSelector<MaterialPickerFeatureState>(featureKey);

export const reducers: ActionReducerMap<MaterialPickerFeatureState, PayloadAction> = {
  state: materialPickerReducer
};
