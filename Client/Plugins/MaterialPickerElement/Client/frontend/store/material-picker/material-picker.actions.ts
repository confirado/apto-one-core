import {createAction, props} from "@ngrx/store";
import {
  MaterialPickerColor,
  MaterialPickerItem,
  MaterialPickerPriceGroup, PropertyGroup
} from "@apto-catalog-frontend/models/material-picker";
import {ItemsUpdatePayload} from "@apto-material-picker-element-frontend/store/material-picker/material-picker.model";

export enum MaterialPickerActionTypes {
  InitMaterialPicker = '[MaterialPicker] Init',
  InitMaterialPickerSuccess = '[MaterialPicker] Init Success',
  FindPoolItems = '[MaterialPicker] Find Pool Items',
  FindPoolItemsSuccess = '[MaterialPicker] Find Pool Items Success',
}

export const initMaterialPicker = createAction(
  MaterialPickerActionTypes.InitMaterialPicker,
  props<{
    payload: ItemsUpdatePayload;
  }>()
);

export const initMaterialPickerSuccess = createAction(
  MaterialPickerActionTypes.InitMaterialPickerSuccess,
  props<{
    payload: {
      items: MaterialPickerItem[]
      colors: MaterialPickerColor[]
      priceGroups: MaterialPickerPriceGroup[]
      propertyGroups: PropertyGroup[]
    };
  }>()
);

export const findPoolItems = createAction(
  MaterialPickerActionTypes.FindPoolItems,
  props<{
    payload: ItemsUpdatePayload;
  }>()
);

export const findPoolItemsSuccess = createAction(
  MaterialPickerActionTypes.FindPoolItemsSuccess,
  props<{
    payload: {
      items: MaterialPickerItem[]
      colors: MaterialPickerColor[]
    };
  }>()
);
