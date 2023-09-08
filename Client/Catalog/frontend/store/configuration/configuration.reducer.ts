import { SelectConnector } from '@apto-base-frontend/store/shop/shop.model';
import {
  getConfigurationStateSuccess,
  getCurrentRenderImageSuccess,
  getRenderImagesSuccess,
  humanReadableStateLoadSuccess,
  initConfigurationSuccess, setHideOnePage,
  setNextPerspective,
  setNextStep,
  setPrevPerspective,
  setPrevStep,
  setQuantity,
  setStep, updateConfigurationState,
} from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ComputedValues, Configuration, RenderImage, StatePrice } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Action, createReducer, on } from '@ngrx/store';

export interface ConfigurationState {
	state: Configuration;
	computedValues: ComputedValues;
	renderImages: RenderImage[];
	perspectives: string[];
	currentPerspective: string | null;
	currentStep: string | null;
	statePrice: StatePrice | null;
	loading: boolean;
	productId: string | null;
	connector: SelectConnector | null;
	humanReadableState: any | null;
	quantity: number;
  hideOnePage: boolean;
}

export const configurationInitialState: ConfigurationState = {
	state: {
		compressedState: [],
		sections: [],
		elements: [],
	},
	computedValues: {},
	renderImages: [],
	perspectives: [],
	currentPerspective: null,
	currentStep: null,
	statePrice: null,
	productId: null,
	loading: false,
	connector: null,
	humanReadableState: null,
	quantity: 1,
  hideOnePage: false
};

const _configurationReducer = createReducer(
	configurationInitialState,
	/*
    on(initConfiguration, (state) => ({
		...state,
		loading: true,
	})),
    */

  on(updateConfigurationState, (state, action) => {
    return {
      ...state,
      loading: true
    }
  }),
  on(initConfigurationSuccess, (state, action) => {
    /*
      productId: string | null;
      connector: SelectConnector | null;
      humanReadableState: any | null;
      quantity: number;
     */
    return {
      ...state,
      quantity: action.payload.product.minPurchase === 0 ? 1 : action.payload.product.minPurchase,
      state: action.payload.configuration,
      renderImages: action.payload.renderImages,
      computedValues: action.payload.computedValues,
      perspectives: action.payload.perspectives,
      currentPerspective: action.payload.currentPerspective,
      currentStep: action.payload.currentStep,
      statePrice: action.payload.statePrice,
      loading: false,
      productId: action.payload.productId,
      connector: action.payload.connector
    }
  }),
  /*
    on(getConfigurationState, (state) => ({
    ...state,
    loading: true,
    })),
  */
  on(getConfigurationStateSuccess, (state, action) => {
    return {
      ...state,
      state: action.payload.configuration,
      renderImages: action.payload.renderImages,
      computedValues: action.payload.computedValues,
      perspectives: action.payload.perspectives,
      currentPerspective: action.payload.currentPerspective,
      statePrice: action.payload.statePrice,
      loading: false,
    }
  }),

	on(getCurrentRenderImageSuccess, (state, action) => ({
		...state,
		renderImages: action.payload.renderImages,
	})),
	on(getRenderImagesSuccess, (state, action) => ({
		...state,
		renderImages: action.payload.renderImages,
	})),
	on(humanReadableStateLoadSuccess, (state, action) => ({
		...state,
		humanReadableState: action.payload,
	})),
	on(setQuantity, (state, action) => ({
		...state,
		quantity: action.quantity,
	})),
	on(setPrevPerspective, (state) => {
		if (state.perspectives.length < 2) {
			return {
				...state,
			};
		}

		if (state.currentPerspective === null) {
			return {
				...state,
				currentPerspective: state.perspectives[0],
			};
		}

		const currentIndex = state.perspectives.indexOf(state.currentPerspective);
		if (currentIndex === 0) {
			return {
				...state,
				currentPerspective: state.perspectives[state.perspectives.length - 1],
			};
		}

		return {
			...state,
			currentPerspective: state.perspectives[currentIndex - 1],
		};
	}),
	on(setNextPerspective, (state) => {
		if (state.perspectives.length < 2) {
			return {
				...state,
			};
		}

		if (state.currentPerspective === null) {
			return {
				...state,
				currentPerspective: state.perspectives[0],
			};
		}

		const currentIndex = state.perspectives.indexOf(state.currentPerspective);
		if (currentIndex + 1 === state.perspectives.length) {
			return {
				...state,
				currentPerspective: state.perspectives[0],
			};
		}

		return {
			...state,
			currentPerspective: state.perspectives[currentIndex + 1],
		};
	}),
	on(setPrevStep, (state) => {
		const sections = state.state.sections.filter((section) => !section.disabled);
		const currentIndex = sections.findIndex((section) => section.id === state.currentStep);

		// if no current index was found but at least one section is available set current step to first section
		if (currentIndex === -1 && sections.length > 0) {
			return {
				...state,
				currentStep: sections[0].id,
			};
		}

		// prev step is available
		if (currentIndex > 0 && sections.length > currentIndex - 1) {
			return {
				...state,
				currentStep: sections[currentIndex - 1].id,
			};
		}

		return {
			...state,
		};
	}),
	on(setNextStep, (state) => {
		const sections = state.state.sections.filter((section) => !section.disabled);
		const currentIndex = sections.findIndex((section) => section.id === state.currentStep);

		// if no current index was found but at least one section is available set current step to first section
		if (currentIndex === -1 && sections.length > 0) {
			return {
				...state,
				currentStep: sections[0].id,
			};
		}

		// next step is available
		if (currentIndex + 1 < sections.length) {
			return {
				...state,
				currentStep: sections[currentIndex + 1].id,
			};
		}

		return {
			...state,
		};
	}),
	on(setStep, (state, action) => ({
		...state,
		currentStep: action.payload.id,
	})),
  on(setHideOnePage, (state, action) => ({
    ...state,
    hideOnePage: action.payload
  })),
);

export function configurationReducer(state: ConfigurationState | undefined, action: Action): ConfigurationState {
	return _configurationReducer(state, action);
}
