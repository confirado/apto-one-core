import { Action, createReducer, on } from '@ngrx/store';
import { findPoolItemsSuccess, initMaterialPickerSuccess } from "@apto-material-picker-element-frontend/store/material-picker/material-picker.actions";
import {
  MaterialPickerColor,
  MaterialPickerItem,
  MaterialPickerPriceGroup, PropertyGroup
} from "@apto-catalog-frontend/models/material-picker";

export interface MaterialPickerState {
  items: MaterialPickerItem[]
  colors: MaterialPickerColor[]
  priceGroups: MaterialPickerPriceGroup[],
  propertyGroups: PropertyGroup[],
}

export const initialState: MaterialPickerState = {
  items: [],
  colors: [],
  priceGroups: [],
  propertyGroups: []
};

const _reducer = createReducer(
  initialState,
  on(initMaterialPickerSuccess, (state, action) => {
    return {
      ...state,
      items: action.payload.items,
      colors: action.payload.colors,
      priceGroups: action.payload.priceGroups,
      propertyGroups: action.payload.propertyGroups
    }
  }),
  on(findPoolItemsSuccess, (state, action) => {
    return {
      ...state,
      items: action.payload.items,
      colors: action.payload.colors
    }
  })
);

export function materialPickerReducer(state: MaterialPickerState | undefined, action: Action): MaterialPickerState {
  return _reducer(state, action);
}
