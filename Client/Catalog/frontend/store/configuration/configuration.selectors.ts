import { TranslatedValue } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { ProgressState, ProgressStep, RenderImage } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { CatalogFeatureState, featureSelector } from '@apto-catalog-frontend/store/feature';
import { createSelector } from '@ngrx/store';
import { Element, Section } from '../product/product.model';

export const selectConfiguration = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration);

export const selectHideOnePage = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.hideOnePage);

export const selectConfigurationLoading = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.loading);

export const selectProduct = createSelector(featureSelector, (state: CatalogFeatureState) => state.product);

function getDescription(section: Section, state: CatalogFeatureState, locale: string | null): string {
	const elements = state.configuration.state.elements.filter((e) => e.active && e.sectionId === section?.id).map((e) => e.id);

	const description = state.product.elements
		.filter((e) => elements.includes(e.id))
		.map((e) => e.name[locale || ''])
		.join(', ');

	return description;
}

export const selectProgressState = createSelector(featureSelector, selectLocale, (state: CatalogFeatureState, locale: string | null) => {
	const currentSection = state.product.sections.find((section) => section.id === state.configuration.currentStep);
	const sections = state.configuration.state.sections.filter((section) => !section.hidden && !section.disabled);

	let currentStep: ProgressStep | undefined;
	const afterSteps: ProgressStep[] = [];
	const beforeSteps: ProgressStep[] = [];

	for (const s of sections) {
		const pSection = state.product.sections.find((ppSection) => ppSection.id === s.id);
		if (!pSection) {
			continue;
		}

		const elements = state.configuration.state.elements.filter((e) => !e.disabled && e.sectionId === s.id).map((e) => e.id);

		const pElements = state.product.elements.filter((e) => elements.includes(e.id));

		const progressElements = pElements.map((pElement) => ({
			state: state.configuration.state.elements.find((e) => e.id === pElement?.id)!,
			element: pElement,
		}));

		const description = getDescription(pSection, state, locale);

		const section: Section = { ...pSection };

		const active: boolean = progressElements.some((e) => e.state.active);

		const fulfilledElements: boolean[] = [];

		let fulfilled: boolean = false;

		if (!s.mandatory) {
			fulfilled = true;
		}

		for (const entry of progressElements) {
			if (!entry.element.isMandatory || (entry.element.isMandatory && entry.state.active)) {
				fulfilledElements.push(true);
			}
			if (entry.element.isMandatory && !entry.state.active) {
				fulfilledElements.push(false);
			}
		}

		if (s.mandatory && fulfilledElements.every((e) => e) && active) {
			fulfilled = true;
		}

		if (section.id === state.configuration.currentStep) {
			currentStep = {
				status: 'CURRENT',
				fulfilled,
				description,
				active,
				section,
				elements: progressElements,
			};
		} else if (currentStep) {
			afterSteps.push({
				status: 'REMAINING',
				fulfilled,
				description,
				active,
				section,
				elements: progressElements,
			});
		} else {
			beforeSteps.push({
				status: 'COMPLETED',
				fulfilled,
				description,
				active,
				section,
				elements: progressElements,
			});
		}
	}
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

export const selectProgress = createSelector(selectProgressState, (state: ProgressState) =>
	Math.round((state.steps.filter((s) => s.fulfilled).length / state.steps.length) * 100)
);

export const selectCompressedState = createSelector(
	featureSelector,
	(state: CatalogFeatureState) => state.configuration.state.compressedState
);

export const selectRenderImage = createSelector(featureSelector, (state: CatalogFeatureState): RenderImage | null => {
	let currentRenderImage: RenderImage | null = null;

	// search current render image
	state.configuration.renderImages.every((renderImage) => {
		if (renderImage.perspective === state.configuration.currentPerspective) {
			currentRenderImage = renderImage;
			return false;
		}
		return true;
	});

	return currentRenderImage;
});

export const selectRenderImageByPerspective = (perspective: string) => createSelector(featureSelector, (state: CatalogFeatureState): RenderImage | null => {
    let currentRenderImage: RenderImage | null = null;

    // search current render image
    state.configuration.renderImages.every((renderImage) => {
      if (renderImage.perspective === perspective) {
        currentRenderImage = renderImage;
        return false;
      }
      return true;
    });

    return currentRenderImage;
  }
);

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

	state.configuration.renderImages.forEach((renderImage) => {
		perspectives.push(renderImage.perspective);
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
		return Object.entries(state.configuration.statePrice.sections).find(([key]) => key === section.id)?.[1].sum.price.formatted;
	});

export const selectQuantity = createSelector(featureSelector, (state: CatalogFeatureState) => state.configuration.quantity);

export const selectElementValues = (element: Element): any =>
	createSelector(featureSelector, (state: CatalogFeatureState): TranslatedValue[] => {
    if (!state.configuration.humanReadableState) {
      return [];
    }

    const x = Object.entries<any>(state.configuration.humanReadableState).find((e) => e[0] === element.id);
    if (x) {
      return Object.values(x[1]);
    }

    return [];
	});
