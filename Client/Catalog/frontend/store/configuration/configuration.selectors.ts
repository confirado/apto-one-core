import { translate, TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import {
  PartsListPart,
  ProgressState,
  ProgressStep,
  RenderImage,
  RenderImageData, SectionPriceTableItem, ParameterStateTypes, ProgressStatuses, ElementState, SectionTypes, HumanReadableFullState,
} from '@apto-catalog-frontend/store/configuration/configuration.model';
import { CatalogFeatureState, featureSelector } from '@apto-catalog-frontend/store/feature';
import { createSelector } from '@ngrx/store';
import { Element, Section } from '../product/product.model';
import { TempStateItem } from './configuration.model';
import { ProductState } from '../product/product.reducer';

import { getHumanReadableFullState } from '@apto-catalog-frontend/services/store-utilities';

export const selectConfiguration = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration);

export const selectConfigurationError = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.configurationError);

export const selectHideOnePage = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.hideOnePage);

export const selectConfigurationLoading = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.loading);

export const selectProduct = createSelector(featureSelector, (state: CatalogFeatureState) => state.product);

export const selectComputedValueList = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.computedValues);

function getDescription(section: Section, state: CatalogFeatureState, locale: string | null): string {
	const elements = state.configuration.state.elements.filter((e) => e.active && e.sectionId === section?.id).map((e) => e.id);

	const description = state.product.elements
		.filter((e) => elements.includes(e.id))
		.map((e) => e.name[locale || ''])
		.join(', ');

	return description;
}

export const selectTempState = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.tempState);

export const selectProgressState = createSelector(featureSelector, selectLocale, (state: CatalogFeatureState, locale: string | null) => {
  const cSections = state.configuration.state.sections.filter((section) => !section.hidden && (!section.disabled || !state.product.product.keepSectionOrder));

	let currentStep: ProgressStep | undefined;
	const afterSteps: ProgressStep[] = [];
	const beforeSteps: ProgressStep[] = [];

    for (const cSection of cSections) {
      const pSection = state.product.sections.find((ppSection) => ppSection.id === cSection.id);
      if (!pSection) {
          continue;
      }

      const elements = state.configuration.state.elements
        .filter((e) => (!e.disabled|| !state.product.product.keepSectionOrder) && e.sectionId === cSection.id && e.sectionRepetition === cSection.repetition)
        .map((e) => e.id);

      // let in product elements only those are available in state configuration
	  const pElements = state.product.elements.filter((e) => elements.includes(e.id));

    const progressElements = pElements.map((pElement) => ({
        state: state.configuration.state.elements.find((e) => e.id === pElement?.id && e.sectionRepetition === cSection.repetition)!,
        element: pElement,
    }));

	const description = getDescription(pSection, state, locale);

	const section: Section = {
      ...pSection,
      repetition: cSection.repetition,
    };

    // if one of the elements is active, then the whole section is active
		const active: boolean = progressElements.some((e) => e.state.active);

		const fulfilledElements: boolean[] = [];

		let fulfilled: boolean = false;

		if (!cSection.mandatory) {
			fulfilled = true;
		}

    // now check if the section is fulfilled
		for (const entry of progressElements) {
			if (!entry.element.isMandatory || (entry.element.isMandatory && entry.state.active)) {
				fulfilledElements.push(true);
			}
			if (entry.element.isMandatory && !entry.state.active) {
				fulfilledElements.push(false);
			}
		}

    // if section allows multiple elements then all mandatory elements must be selected, if not multiple then only one of them can be selected
    // to consider the section as fulfilled, so we check section for active in that case (see above)
    if (section.allowMultiple) {
      if (cSection.mandatory && fulfilledElements.every((e) => e) && active) {
        fulfilled = true;
      }
    } else {
      if (cSection.mandatory && active) {
        fulfilled = true;
      }
    }

    let currentStepId = null;
    let currentRepetition = 0;
    if (state.configuration.currentStep) {
      currentStepId = state.configuration.currentStep.id;
      currentRepetition = state.configuration.currentStep.repetition;
    }

		if (section.id === currentStepId && section.repetition === currentRepetition) {
			currentStep = {
				status: ProgressStatuses.CURRENT,
				fulfilled,
				description,
				active,
				section,
				elements: progressElements,
			};
		} else if (currentStep) {
			afterSteps.push({
				status: ProgressStatuses.REMAINING,
				fulfilled,
				description,
				active,
				section,
				elements: progressElements,
			});
		} else {
			beforeSteps.push({
				status: ProgressStatuses.COMPLETED,
				fulfilled,
				description,
				active,
				section,
				elements: progressElements,
			});
		}
	}

  // percentage value of current progress: Example: 33%
	const progress: number = Math.round(
		((afterSteps.filter((s) => s.fulfilled).length + beforeSteps.filter((s) => s.fulfilled).length + (currentStep?.fulfilled ? 1 : 0)) /
			(beforeSteps.length + afterSteps.length + (currentStep?.fulfilled ? 1 : 0))) *
			100
	);

	const progressState: ProgressState = {
		productId: state.product.product?.id,
		currentStep,
		afterSteps,
		beforeSteps,
		steps: currentStep ? [...beforeSteps, currentStep, ...afterSteps] : [...beforeSteps, ...afterSteps],
		progress,
	};

	return progressState;
});

export const selectProgress = createSelector(
  featureSelector, selectProgressState, selectTempState, selectProduct, (state: CatalogFeatureState, progressState: ProgressState, tempState: TempStateItem[], product: ProductState) => {
    // if current step is undefined we have no step let to configure so we can return 100
    if (!progressState.currentStep) {
      return 100;
    }

    let completedSteps = 0;
    if (product.product.keepSectionOrder === true) {
      // case section order is fixed
      completedSteps = progressState.beforeSteps.length;
      let currentActiveElements = progressState.currentStep.elements.filter(e => e.state.active).length;
      let currentMandatoryElements = progressState.currentStep.elements.filter(e => e.state.mandatory).length;
      let currentMandatoryActiveElements = progressState.currentStep.elements.filter(e => e.state.mandatory && e.state.active).length;

      // if current step seems complete we add 1 step to completed steps
      // current step is complete if minimum 1 element is selected and all mandatory elements are selected
      if (currentActiveElements > 0 && currentMandatoryElements === currentMandatoryActiveElements) {
        completedSteps++;
      }
    } else {
      // case section order is not fixed
      progressState.steps.forEach((step: ProgressStep) => {
        const section = state.configuration.state.sections.find((section) => section.id === step.section.id && section.repetition === step.section.repetition);
        const elements = state.configuration.state.elements.filter((element) => element.sectionId === section.id);
        const tempStateItem = tempState.find((tempStateItem) => tempStateItem.sectionId === section.id && tempStateItem.touched === true && tempStateItem.repetition === section.repetition);
        if (sectionIsValid(section, elements) && tempStateItem) {
          completedSteps++;
        }
      });
    }

    return Math.round((completedSteps / progressState.steps.length) * 100);
  }
);

export const selectCompressedState = createSelector(
	featureSelector,
	(state: CatalogFeatureState) => state.configuration.state.compressedState
);

export const selectRenderImage = createSelector(featureSelector, (state: CatalogFeatureState): RenderImage | null => {
	let currentRenderImage: RenderImage | null = null;

	// search current render image
	// state.configuration.renderImages.every((renderImage) => {
	// 	if (renderImage.perspective === state.configuration.currentPerspective) {
	// 		currentRenderImage = renderImage;
	// 		return false;
	// 	}
	// 	return true;
	// });

	return currentRenderImage;
});

export const selectCurrentRenderImages = createSelector(featureSelector, (state: CatalogFeatureState): RenderImageData[] => {
  let currentRenderImages: RenderImageData[] = [];

  // search current render image
  Object.keys(state.configuration.renderImages).forEach((key,index) => {
    if (key === state.configuration.currentPerspective) {
      currentRenderImages = state.configuration.renderImages[key];
    }
  });

  return currentRenderImages;
});

export const selectRenderImagesForPerspective = (perspective: string) => createSelector(featureSelector, (state: CatalogFeatureState): RenderImageData[] => {
  return state.configuration.renderImages[perspective] ?? [];
});

export const selectSumPrice = createSelector(featureSelector, (state: CatalogFeatureState) => {
	if (state.configuration.statePrice === null) {
		return null;
	}
	return state.configuration.statePrice.sum.price.formatted;
});

export const selectCurrentPerspective = createSelector(
	featureSelector,
	(state: CatalogFeatureState) => state.configuration.currentPerspective
);

export const selectPerspectives = createSelector(featureSelector, (state: CatalogFeatureState) => {
	const perspectives: string[] = [];

  Object.keys(state.configuration.renderImages).forEach((key,index) => {
    if (state.configuration.renderImages[key].length > 0) {
      perspectives.push(key);
    }
  });

	return perspectives;
});

export const selectBasicPrice = createSelector(featureSelector, (state: CatalogFeatureState) => {
	if (state.configuration.statePrice === null) {
		return null;
	}
	return state.configuration.statePrice.own.price.formatted;
});

export const selectBasicPseudoPrice = createSelector(featureSelector, (state: CatalogFeatureState) => {
	if (state.configuration.statePrice === null) {
		return null;
	}
	return state.configuration.statePrice.own.pseudoPrice.formatted;
});

export const selectSumPseudoPrice = createSelector(featureSelector, (state: CatalogFeatureState) => {
	if (state.configuration.statePrice === null) {
		return null;
	}
	return state.configuration.statePrice.sum.pseudoPrice.formatted;
});

export const selectSectionPrice = (section: Section): any =>
	createSelector(featureSelector, (state: CatalogFeatureState) => {
		if (state.configuration.statePrice === null) {
			return null;
		}
		return Object.entries(state.configuration.statePrice.sections).find(([key]) => key === section.id)?.[1][section.repetition].sum.price.formatted;
	});

export const selectSectionPseudoPrice = (section: Section): any =>
  createSelector(featureSelector, (state: CatalogFeatureState) => {
    if (state.configuration.statePrice === null) {
      return null;
    }
    return Object.entries(state.configuration.statePrice.sections).find(([key]) => key === section.id)?.[1][section.repetition].sum.pseudoPrice.formatted;
  });

export const selectSectionPriceTable = (section: Section): any => createSelector(featureSelector, (state: CatalogFeatureState): SectionPriceTableItem[] => {
  let priceTable: SectionPriceTableItem[] = [];

  // if that section has no surcharges we can return an empty array here
  if (
	  !state.configuration.statePrice.sections.hasOwnProperty(section.id) ||
	  state.configuration.statePrice.sections[section.id].length < section.repetition
  ) {
    return priceTable;
  }

  // add all element surcharges of that section to the priceTable array
  const sectionPrices = state.configuration.statePrice.sections[section.id][section.repetition];
  Object.keys(sectionPrices.elements).forEach((elementId) => {
    const elementPrice = sectionPrices.elements[elementId];
    const pElement = state.product.elements.find(e => e.id === elementId);

    // we do not want to add elements without a surcharge
    if (elementPrice.own.price.amount === 0) {
      return;
    }

    // add element own price
    priceTable.push({
      elementId: pElement.id,
      name: pElement.name,
      value: elementPrice.own.pseudoPrice.formatted,
      position: pElement.position,
      isDiscount: false
    });

    // add element discount
    if (elementPrice.own.pseudoDiff.amount !== 0) {
      priceTable.push({
        elementId: pElement.id,
        name: elementPrice.discount.name,
        value: elementPrice.own.pseudoDiff.formatted,
        // element position is always an integer, we add 0.5 here to make sure after sorting, the element price and its discount is displayed one below the other
        position: pElement.position + 0.5,
        isDiscount: true
      });
    }
  });

  // resort elements by its position
  priceTable.sort((a,b) => a.position - b.position);

  // add section discount
  if (sectionPrices.own.pseudoDiff.amount !== 0) {
    priceTable.unshift({
      elementId: null,
      name: sectionPrices.discount.name,
      value: sectionPrices.own.pseudoDiff.formatted,
      position: -1,
      isDiscount: true
    });
  }

  // add section own price
  if (sectionPrices.own.pseudoPrice.amount !== 0) {
    priceTable.unshift({
      elementId: null,
      name: section.name,
      value: sectionPrices.own.pseudoPrice.formatted,
      position: -2,
      isDiscount: false
    });
  }

  return priceTable;
});

export const selectQuantity = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.quantity);

export const selectParameterQuantity = createSelector(featureSelector, (state: CatalogFeatureState) => {
  const result = state.configuration.state.compressedState.find(elem => elem['name'] && elem['name'] === ParameterStateTypes.QUANTITY)
  return result['value'] ? Number(result['value']) : 1;
});

export const selectParameterRepetitions = createSelector(featureSelector, (state: CatalogFeatureState) => {
  const result = state.configuration.state.compressedState.find(elem => elem['name'] && elem['name'] === ParameterStateTypes.REPETITIONS)
  return result['value'] ? Number(result['value']) : 1;
});


export const selectElementValues = (element: Element): any =>
	createSelector(featureSelector, (state: CatalogFeatureState): TranslatedValue[] => {
    if (!state.configuration.humanReadableState) {
      return [];
    }

    const x = state.configuration.humanReadableState.find((e) => e.elementId === element.id && e.repetition === element.sectionRepetition);

    return x ? [].concat(...Object.values(x.values)) : [];
	});

export const selectHumanReadableState = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.humanReadableState);

export const selectHumanReadableFullState = createSelector(featureSelector, selectLocale, selectHumanReadableState, (state: CatalogFeatureState, locale: string | null, configurationHumanReadableState): HumanReadableFullState[] => {
  return getHumanReadableFullState(state, locale, configurationHumanReadableState);
});

export const selectCurrentProductElements = createSelector(featureSelector, (state: CatalogFeatureState) => {
  let currentStepId = null;
  if (state.configuration.currentStep) {
    currentStepId = state.configuration.currentStep.id;
  }

  return state.product.elements.filter((element) => element.sectionId === currentStepId);
});

export const selectCurrentStateElements = createSelector(featureSelector, (state: CatalogFeatureState) => {
  let currentStepId = null;
  if (state.configuration.currentStep) {
    currentStepId = state.configuration.currentStep.id;
  }

  return state.configuration.state.elements.filter((element) => element.sectionId === currentStepId);
});

export const selectSectionProductElements = (sectionId: string) => createSelector(featureSelector, (state: CatalogFeatureState) => {
  return state.product.elements.filter((element) => element.sectionId === sectionId);
});

export const selectSectionStateElements = (sectionId: string) => createSelector(featureSelector, (state: CatalogFeatureState) => {
  return state.configuration.state.elements.filter((element) => element.sectionId === sectionId);
});

export const selectElementState = (elementId: string) => createSelector(featureSelector, (state: CatalogFeatureState) => {
  const filtered = state.configuration.state.elements.filter((element) => element.id === elementId);
  if (filtered.length > 0) {
    return filtered[0];
  }
  return null;
});

export const selectStateElements = createSelector(featureSelector, (state: CatalogFeatureState) => {
  return state.configuration.state.elements;
});

export const selectStateActiveElements = createSelector(featureSelector, (state: CatalogFeatureState) => {
  return state.configuration.state.elements.filter((e: ElementState) => e.active);
});

export const selectElementComputableValues = createSelector(featureSelector, (state: CatalogFeatureState): any => {
  return state.configuration['searchMapping'];
});

export const configurationIsValid = createSelector(featureSelector, (state: CatalogFeatureState) => {
  if (state.configuration.state.failedRules.length > 0 ) {
    let onlySoftRules = true;
    for (let i = 0; i < state.configuration.state.failedRules.length; i++) {
      if (!state.configuration.state.failedRules[i].softRule) {
        onlySoftRules = false;
      }
    }
    if (!onlySoftRules) {
      return false;
    }
  }

  for (let i = 0; i < state.configuration.state.sections.length; i++) {
    const section = state.configuration.state.sections[i];
    const elements = state.configuration.state.elements.filter((element) => element.sectionId === section.id && element.sectionRepetition === section.repetition);
    if (!sectionIsValid(section, elements)) {
      return false;
    }
  }
  return true;
});

export const selectSectionIsValid = (sectionId: string, repetition: number) => createSelector(featureSelector, (state: CatalogFeatureState) => {
  const section = state.configuration.state.sections.find(s => s.id === sectionId && s.repetition === repetition);
  if (!section) {
    return false;
  }

  const elements = state.configuration.state.elements.filter((element) => element.sectionId === section.id && element.sectionRepetition === repetition);
  return sectionIsValid(section, elements);
});

function sectionIsValid(section, elements) {
  // if section is disabled we consider it as valid, so that we can go to the next sections
  if (section.disabled === true) {
    return true;
  }

  if (section.multiple === true) {
    for (let element of elements) {
      // all mandatory elements in the section that are not disabled must be selected, otherwise we consider the whole section as invalid
      if (element.disabled === false && element.mandatory === true && element.active === false) {
        return false;
      }
    }
  }

  // if section is mandatory then it must be selected
  if (section.mandatory === true && section.active === false) {
    return false;
  }

  // section is valid
  return true;
}

export const selectPartsList = createSelector(featureSelector, (state: CatalogFeatureState): PartsListPart[] => {
  return state.configuration.partsList;
});
