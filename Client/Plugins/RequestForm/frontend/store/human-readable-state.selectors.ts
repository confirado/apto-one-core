import { createSelector } from '@ngrx/store';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { CatalogFeatureState, featureSelector } from '@apto-catalog-frontend/store/feature';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { selectHumanReadableState as selectConfigurationHumanReadableState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { SectionTypes } from '@apto-catalog-frontend/store/configuration/configuration.model';

export const selectHumanReadableState = createSelector(featureSelector, selectLocale, selectConfigurationHumanReadableState, (state: CatalogFeatureState, locale: string | null, configurationHumanReadableState) => {
  let humanReadableState: any = {};
  if (!locale) {
    return humanReadableState;
  }

  const sections = state.configuration.state.sections.filter((section) => !section.hidden && !section.disabled && section.active);
  sections.forEach((section) => {
    // search for selected elements in section or continue with next section
    const elements = state.configuration.state.elements.filter((element) => !element.disabled && element.active && element.sectionId === section.id && element.sectionRepetition === section.repetition);
    if (elements.length < 1) {
      return;
    }

    // search for section details in product or continue with next section if no details where found
    let pSection = state.product.sections.find(pSection => pSection.id === section.id);
    if (!pSection) {
      return;
    }

    // set section name
    let sectionName = translate(pSection.name, locale);
    if (!sectionName) {
      sectionName = pSection.identifier;
    }

    if (section.repeatableType === SectionTypes.WIEDERHOLBAR) {
      sectionName += ' ' + (section.repetition + 1);
    }

    // init section elements
    humanReadableState[sectionName] = [];
    elements.forEach((element) => {
      const pElements = state.product.elements.filter(pElement => pElement.id === element.id);
      // search for element details in product or continue with next element if no details where found
      if (pElements.length < 1) {
        return;
      }
      const pElement = pElements[0];

      // set element name
      let elementName = translate(pElement.name, locale);
      if (!elementName) {
        elementName = element.identifier;
      }

      // collect element definition values from configurations readable state
      let values = {};
      let hrsElement = configurationHumanReadableState.find(e => e.sectionId === section.id && e.elementId === element.id && e.repetition === element.sectionRepetition);
      if (hrsElement) {
        Object.keys(hrsElement.values).forEach((value) => {
          values[value] = translate(hrsElement.values[value], locale);
        });
      }

      // add element to human-readable state
      humanReadableState[sectionName].push({
        id: element.id,
        name: elementName,
        values: values
      });
    });
  });

  return humanReadableState;
});
