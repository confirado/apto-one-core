import { buildConfiguration, buildConfigurationElement, buildConfigurationSection, buildMockStoreInitialState } from '@apto-catalog-frontend-store-initial-state-builder';
import {
  configurationIsValid, selectCurrentPerspective, selectCurrentRenderImages, selectParameterQuantity,
  selectParameterRepetitions, selectPartsList, selectQuantity, selectRenderImagesForPerspective,
  selectSectionIsValid, selectStateActiveElements, selectSumPrice, selectTotalPrice,
} from './configuration.selectors';
import { ParameterStateTypes } from './configuration.model';

describe('configuration selectors', () => {
  const catalogState = buildMockStoreInitialState({
    aptoCatalog: {
      configuration: {
        quantity: 3, currentPerspective: 'front', renderImages: { front: [{ renderImageId: 'front-1' }], back: [{ renderImageId: 'back-1' }] } as never,
        partsList: [{ partNumber: 'part-1' }] as never,
        statePrice: { sum: { price: { amount: 19.95, formatted: '19,95 €' } }, own: { price: { amount: 19.95, formatted: '19,95 €' } }, sections: {} } as never,
        state: buildConfiguration({
          compressedState: [{ name: ParameterStateTypes.QUANTITY, value: '2' }, { name: ParameterStateTypes.REPETITIONS, value: '4' }],
          sections: [buildConfigurationSection({ mandatory: true, active: true })],
          elements: [buildConfigurationElement({ id: 'active' }), buildConfigurationElement({ id: 'inactive', active: false })],
        }),
      },
    },
  }).aptoCatalog;

  it('projects quantity, prices, perspectives, and render images', () => {
    expect(selectQuantity.projector(catalogState)).toBe(3);
    expect(selectCurrentPerspective.projector(catalogState)).toBe('front');
    expect(selectSumPrice.projector(catalogState)).toBe('19,95 €');
    expect(selectTotalPrice.projector(catalogState)).toBeCloseTo(59.85, 6);
    expect(selectCurrentRenderImages.projector(catalogState)[0].renderImageId).toBe('front-1');
    expect(selectRenderImagesForPerspective('back').projector(catalogState)[0].renderImageId).toBe('back-1');
    expect(selectRenderImagesForPerspective('side').projector(catalogState)).toEqual([]);
  });

  it('projects compressed parameters, active elements, mandatory validity, and parts', () => {
    expect(selectParameterQuantity.projector(catalogState)).toBe(2);
    expect(selectParameterRepetitions.projector(catalogState)).toBe(4);
    expect(selectStateActiveElements.projector(catalogState).map((element) => element.id)).toEqual(['active']);
    expect(selectSectionIsValid('section-1', 0).projector(catalogState)).toBeTrue();
    expect(configurationIsValid.projector(catalogState)).toBeTrue();
    expect(selectPartsList.projector(catalogState)[0].partNumber).toBe('part-1');
  });

  it('uses empty render-image lists and one as parameter defaults when optional state is absent', () => {
    const incomplete = {
      ...catalogState,
      configuration: {
        ...catalogState.configuration,
        currentPerspective: null,
        renderImages: {} as never,
        state: buildConfiguration({ ...catalogState.configuration.state, compressedState: [] }),
      },
    };

    expect(selectCurrentRenderImages.projector(incomplete)).toEqual([]);
    expect(selectRenderImagesForPerspective('front').projector(incomplete)).toEqual([]);
    expect(selectParameterQuantity.projector(incomplete)).toBe(1);
    expect(selectParameterRepetitions.projector(incomplete)).toBe(1);
  });

  it('rejects a hard failed rule while allowing a soft failed rule', () => {
    const soft = { ...catalogState, configuration: { ...catalogState.configuration, state: buildConfiguration({ ...catalogState.configuration.state, failedRules: [{ softRule: true }] }) } };
    const hard = { ...soft, configuration: { ...soft.configuration, state: buildConfiguration({ ...soft.configuration.state, failedRules: [{ softRule: false }] }) } };

    expect(configurationIsValid.projector(soft)).toBeTrue();
    expect(configurationIsValid.projector(hard)).toBeFalse();
  });
});
