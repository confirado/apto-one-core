import {
  addOfferConfiguration, addOfferConfigurationSuccess,
  addToBasket, addToBasketSuccess,
  createLoadingFlagAction, fetchPartsListSuccess,
  getConfigurationStateSuccess,
  getCurrentRenderImageSuccess, getElementComputableValuesSuccess,
  getRenderImagesSuccess, getStatePriceSuccess, hideLoadingFlagAction,
  humanReadableStateLoadSuccess,
  initConfigurationSuccess,
  resetLoadingFlagAction,
  setHideOnePage,
  setNextPerspective,
  setNextStep, setPerspective,
  setPrevPerspective,
  setPrevStep,
  setQuantity, setSectionTouched,
  setStep, updateConfigurationState,
} from '@apto-catalog-frontend/store/configuration/configuration.actions';
import {
  ComputedValues,
  Configuration,
  CurrentSection,
  HumanReadableState,
  PartsListPart,
  RenderImage,
  StatePrice, TempStateItem,
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import { Action, createReducer, on } from '@ngrx/store';

export interface ConfigurationState {
	state: Configuration;
	computedValues: ComputedValues;
	renderImages: RenderImage[];
	perspectives: string[];
	currentPerspective: string | null;
	currentStep: CurrentSection | null;
	statePrice: StatePrice | null;
	loading: boolean;
	productId: string | null;
	humanReadableState: HumanReadableState[];
	quantity: number;
  hideOnePage: boolean;
  partsList: PartsListPart[];
  tempState: TempStateItem[]
}

export const configurationInitialState: ConfigurationState = {
	state: {
		compressedState: [],
		sections: [],
		elements: [],
    failedRules: [],
	},
	computedValues: {},
	renderImages: [],
	perspectives: [],
	currentPerspective: null,
	currentStep: null,
	statePrice: null,
	productId: null,
	loading: false,
	humanReadableState: [],
	quantity: 1,
  hideOnePage: false,
  partsList: [],
  tempState: [],
};

const _configurationReducer = createReducer(
	configurationInitialState,
	/*
    on(initConfiguration, (state) => ({
		...state,
		loading: true,
	})),
    */
  on(addToBasket, (state, action) => {
    return {
      ...state,
      loading: true,
    };
  }),
  on(addToBasketSuccess, (state, action) => {
    return {
      ...state,
      loading: false,
    };
  }),
  on(hideLoadingFlagAction, (state, action) => {
    return {
      ...state,
      loading: false,
    };
  }),
  on(createLoadingFlagAction, (state, action) => {
    return {
      ...state,
      loading: true,
    };
  }),
  on(resetLoadingFlagAction, (state, action) => {
    return {
      ...state,
      loading: false
    };
  }),
  on(updateConfigurationState, (state, action) => {
    return {
      ...state,
      loading: true
    }
  }),
  on(initConfigurationSuccess, (state, action) => {
    /*
      productId: string | null;
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
  on(addOfferConfiguration, (state, action) => {
    return {
      ...state,
      loading: true,
    }
  }),
  on(addOfferConfigurationSuccess, (state, action) => {
    return {
      ...state,
      loading: false,
    }
  }),
  on(getElementComputableValuesSuccess, (state, action) => {
    return {
      ...state,
      searchMapping: action.payload.searchMapping
    }
  }),
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
  on(setPerspective, (state, { perspective }) => {
    return {
      ...state,
      currentPerspective: perspective
    };
  }),
	on(setPrevStep, (state) => {
		const sections = state.state.sections.filter((section) => !section.disabled);
    const currentIndex = sections.findIndex((section) => section.id === state.currentStep.id && section.repetition === state.currentStep.repetition);

		// if no current index was found but at least one section is available set current step to first section
		if (currentIndex === -1 && sections.length > 0) {
			return {
				...state,
				currentStep: { id: sections[0].id, repetition: sections[0].repetition },
			};
		}

		// prev step is available
		if (currentIndex > 0 && sections.length > currentIndex - 1) {
			return {
				...state,
				currentStep: { id: sections[currentIndex - 1].id, repetition: sections[currentIndex - 1].repetition },
			};
		}

		return {
			...state,
		};
	}),
	on(setNextStep, (state) => {
		const sections = state.state.sections.filter((section) => !section.disabled);
		const currentIndex = sections.findIndex((section) => section.id === state.currentStep.id && section.repetition === state.currentStep.repetition);

		// if no current index was found but at least one section is available set current step to first section
		if (currentIndex === -1 && sections.length > 0) {
			return {
				...state,
				currentStep: { id: sections[0].id, repetition: sections[0].repetition },
			};
		}

		// next step is available
		if (currentIndex + 1 < sections.length) {
			return {
				...state,
				currentStep: { id: sections[currentIndex + 1].id, repetition: sections[currentIndex + 1].repetition },
			};
		}

		return {
			...state,
		};
	}),
	on(setStep, (state, action) => {
    return {
      ...state,
      currentStep: action.payload,
    }
	}),
  on(setSectionTouched, (state, action) => {
    const { sectionId, repetition, touched } = action.payload;

    let updatedTempState = state.tempState ? [...state.tempState] : [];
    let found = false;

    // if section found in temp state then update it, otherwise add it to tempState array
    updatedTempState = updatedTempState.map(item => {
      if (item.sectionId === sectionId && item.repetition === repetition) {
        found = true;
        return { ...item, touched };
      }
      return item;
    });

    if (!found) {
      updatedTempState.push({ sectionId, repetition, touched });
    }

    return {
      ...state,
      tempState: updatedTempState
    };
  }),
  on(setHideOnePage, (state, action) => ({
    ...state,
    hideOnePage: action.payload
  })),
  on(fetchPartsListSuccess, (state, action) => ({
    ...state,
    partsList: action.payload
  })),
  on(getStatePriceSuccess, (state, action) => {
    return {
      ...state,
      statePrice: action.payload,
    }
  })
);

export function configurationReducer(state: ConfigurationState | undefined, action: Action): ConfigurationState {
	return _configurationReducer(state, action);
}
