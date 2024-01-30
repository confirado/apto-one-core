import { MessageBusResponseMessage } from '@apto-base-core/models/message-bus-response';
import {
  ComputedValues,
  Configuration, CurrentSection,
  GetConfigurationStateArguments, HumanReadableState, PartsListPart,
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Element, Group, Product, Section } from '@apto-catalog-frontend/store/product/product.model';
import { createAction, props } from '@ngrx/store';

// eslint-disable-next-line no-shadow
export enum ConfigurationActionTypes {
	InitConfiguration = '[Configuration] Init',
	InitConfigurationSuccess = '[Configuration] Init success',
	UpdateConfigurationState = '[Configuration] Update State',
	GetConfigurationState = '[Configuration] Get State',
	GetConfigurationStateSuccess = '[Configuration] Get State success',
	SetPrevStep = '[Configuration] Set prev step',
	SetPrevStepSuccess = '[Configuration] Set prev step success',
	SetNextStep = '[Configuration] Set next step',
	SetStep = '[Configuration] Set step',
	SetStepSuccess = '[Configuration] Set step success',
	GetRenderImagesSuccess = '[RenderImage] Get RenderImages success',
	GetCurrentRenderImageSuccess = '[RenderImage] Get current RenderImage success',
  SetRenderImages = '[RenderImage] Set Render Images',
	SetPrevPerspective = '[Perspective] Set prev Perspective',
	SetNextPerspective = '[Perspective] Set next Perspective',
	SetQuantity = '[Quantity] Set Quantity',
	AddToBasket = '[Basket] Add to basket',
  AddToBasketSuccess = '[Basket] Add to basket success',
	SetElementProperties = '[Update] Set Element Properties',
	HumanReadableStateLoadSuccess = '[HumanReadableState] Load success',
	AddGuestConfiguration = '[Configuration] Add guest configuration',
	AddGuestConfigurationSuccess = '[Configuration] Add guest configuration success',
  SetHideOnePage = '[OnePage] Hide One Page',
	OnError = '[Configuration] Error',
  createLoadingFlagAction = '[Configuration] Create Loading Flag',
  resetLoadingFlagAction = '[Configuration] Reset Loading Flag',
  FetchPartsList = '[Configuration] Fetch Parts List',
  FetchPartsListSuccess = '[Configuration] Fetch Parts List Success',
  HideLoadingFlag = '[Configuration] hide loading flag',
}

export const hideLoadingFlagAction = createAction(ConfigurationActionTypes.HideLoadingFlag);

export const createLoadingFlagAction = createAction(
  ConfigurationActionTypes.createLoadingFlagAction
);

export const resetLoadingFlagAction = createAction(
  ConfigurationActionTypes.resetLoadingFlagAction
);

export const initConfiguration = createAction(
	ConfigurationActionTypes.InitConfiguration,
	props<{
		payload: {
			id: string;
      type: string | null;
		};
	}>()
);

export const initConfigurationSuccess = createAction(
	ConfigurationActionTypes.InitConfigurationSuccess,
	props<{
		payload: {
			product: Product;
			groups: Group[];
			sections: Section[];
			elements: Element[];
			currentStep: CurrentSection | null;
			productId: string;
			configuration: Configuration;
			computedValues: ComputedValues;
			perspectives: string[];
			currentPerspective: string | null;
			statePrice: any;
			connector: any;
      renderImages: [];
		};
	}>()
);

export const updateConfigurationState = createAction(
	ConfigurationActionTypes.UpdateConfigurationState,
	props<{ updates: GetConfigurationStateArguments['updates'] }>()
);

export const getConfigurationState = createAction(ConfigurationActionTypes.GetConfigurationState, props<{ payload: any }>());

export const getConfigurationStateSuccess = createAction(
	ConfigurationActionTypes.GetConfigurationStateSuccess,
	props<{
		payload: {
			productId: string;
			configuration: Configuration;
			computedValues: ComputedValues;
			perspectives: string[];
			currentPerspective: string | null;
			statePrice: any;
      renderImages: [];
		};
	}>()
);

export const getCurrentRenderImageSuccess = createAction(ConfigurationActionTypes.GetCurrentRenderImageSuccess, props<{ payload: any }>());

export const getRenderImagesSuccess = createAction(ConfigurationActionTypes.GetRenderImagesSuccess, props<{ payload: any }>());

export const setPrevStep = createAction(ConfigurationActionTypes.SetPrevStep, props<{ payload: CurrentSection | null }>());

export const setPrevStepSuccess = createAction(ConfigurationActionTypes.SetPrevStepSuccess);

export const humanReadableStateLoadSuccess = createAction(
	ConfigurationActionTypes.HumanReadableStateLoadSuccess,
	props<{ payload: any }>()
);

export const setNextStep = createAction(ConfigurationActionTypes.SetNextStep, props<{ payload: CurrentSection | null }>());

export const setStep = createAction(ConfigurationActionTypes.SetStep, props<{ payload: CurrentSection | null }>());

export const setStepSuccess = createAction(ConfigurationActionTypes.SetStepSuccess);

export const setPrevPerspective = createAction(ConfigurationActionTypes.SetPrevPerspective);

export const setNextPerspective = createAction(ConfigurationActionTypes.SetNextPerspective);

export const setQuantity = createAction(ConfigurationActionTypes.SetQuantity, props<{ quantity: number }>());

export const addToBasket = createAction(
	ConfigurationActionTypes.AddToBasket,
	props<{
		payload: {
			type: 'REQUEST_FORM' | 'ADD_TO_BASKET';
			formData?: any;
      humanReadableState?: HumanReadableState;
      productImage?: string;
		};
	}>()
);

export const addToBasketSuccess = createAction(
  ConfigurationActionTypes.AddToBasketSuccess
);

export const setElementProperties = createAction(
	ConfigurationActionTypes.SetElementProperties,
	props<{
		payload: {
			sectionId: string;
			elementId: string;
			updates: any;
		};
	}>()
);

export const addGuestConfiguration = createAction(
	ConfigurationActionTypes.AddGuestConfiguration,
	props<{
		payload: {
			email: string;
			name: string;
		};
	}>()
);

export const addGuestConfigurationSuccess = createAction(ConfigurationActionTypes.AddGuestConfigurationSuccess);

export const setHideOnePage = createAction(ConfigurationActionTypes.SetHideOnePage, props<{ payload: boolean }>());

export const onError = createAction(ConfigurationActionTypes.OnError, props<{ message: MessageBusResponseMessage }>());

export const fetchPartsList = createAction(ConfigurationActionTypes.FetchPartsList);
export const fetchPartsListSuccess = createAction(ConfigurationActionTypes.FetchPartsListSuccess, props<{ payload: PartsListPart[] }>());
