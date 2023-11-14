import { createSelector } from '@ngrx/store';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { CatalogFeatureState, featureSelector } from '@apto-catalog-frontend/store/feature';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectHumanReadableState as selectConfigurationHumanReadableState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';

export const selectHumanReadableState = createSelector(featureSelector, selectLocale, selectConfigurationHumanReadableState, (state: CatalogFeatureState, locale: string | null, configurationHumanReadableState) => {

  const humanReadableState: any = {};
  if (!locale) {
    return humanReadableState;
  }

  const cSections = state.configuration.state.sections.filter((section) => !section.hidden && !section.disabled && section.active);

  for (const cSection of cSections) {
    // search for section details in product or continue with next section if no details where found
    const pSection = state.product.sections.find((ppSection) => ppSection.id === cSection.id);
    if (!pSection) {
      continue;
    }

    // search for selected elements in section or continue with next section
    const elements = state.configuration.state.elements
      .filter((e) => !e.disabled && e.active && e.sectionId === cSection.id && e.sectionRepetition === cSection.repetition);

    if (elements.length < 1) {
      continue;
    }

    // set section name
    let sectionName = translate(pSection.name, locale);
    if (!sectionName) {
      sectionName = pSection.identifier;
    }

    elements.forEach((element) => {
      const pElement = state.product.elements.find((ppElement) => ppElement.id === element.id);
      // search for element details in product or continue with next element if no details where found
      if (!pElement) {
        return;
      }

      // set element name
      let elementName = translate(pElement.name, locale);
      if (!elementName) {
        elementName = element.identifier;
      }

      if (!humanReadableState.hasOwnProperty(sectionName + element.sectionRepetition)) {
        humanReadableState[sectionName + element.sectionRepetition] = [];
      }

      for (const s of configurationHumanReadableState) {
        if (s.elementId === element.id && s.repetition === element.sectionRepetition) {
          const values = Object.keys(s.values)
            .reduce((result, key) => {
              result[key] = translate(s.values[key], locale);
              return result;
            }, {});

          humanReadableState[sectionName + element.sectionRepetition].push({
            ...s,
            elementName,
            sectionName,
            values,
          });
        }
      }
    });
  }

  return humanReadableState;
});
