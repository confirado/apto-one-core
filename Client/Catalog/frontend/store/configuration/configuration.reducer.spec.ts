import { MessageBusResponseMessage } from '@apto-base-core/models/message-bus-response';
import {
  buildConfiguration,
  buildConfigurationSection,
} from '@apto-catalog-frontend-store-initial-state-builder';
import {
  addToBasket,
  addToBasketSuccess,
  createLoadingFlagAction,
  getConfigurationStateSuccess,
  onError,
  resetLoadingFlagAction,
  setNextPerspective,
  setNextStep,
  setPrevPerspective,
  setPrevStep,
  setQuantity,
  setSectionTouched,
  updateConfigurationState,
} from './configuration.actions';
import { configurationInitialState, configurationReducer } from './configuration.reducer';
import { ParameterStateTypes } from './configuration.model';

const errorMessage: MessageBusResponseMessage = {
  messageName: 'error', message: 'invalid', date: '', duration: 0, error: true,
  errorType: 'InvalidConfigurationStateChangeException' as MessageBusResponseMessage['errorType'],
  errorPayload: { rule: 'width' }, uuid: '', url: '',
};

describe('configurationReducer', () => {
  it('tracks loading over configuration and basket request lifecycles', () => {
    const updating = configurationReducer(configurationInitialState, updateConfigurationState({ updates: {} }));
    const loading = configurationReducer(updating, addToBasket({ payload: { type: 'ADD_TO_BASKET' } }));
    const finished = configurationReducer(loading, addToBasketSuccess());

    expect(updating.loading).toBeTrue();
    expect(finished.loading).toBeFalse();
    expect(configurationReducer(configurationInitialState, createLoadingFlagAction()).loading).toBeTrue();
    expect(configurationReducer(loading, resetLoadingFlagAction()).loading).toBeFalse();
  });

  it('stores a successful state without mutating the prior state and clears an old error', () => {
    const previous = { ...configurationInitialState, configurationError: { errorType: 'old', errorPayload: {} } };
    const configuration = buildConfiguration({ compressedState: [{ name: ParameterStateTypes.QUANTITY, value: 3 }] });
    const result = configurationReducer(previous, getConfigurationStateSuccess({
      payload: {
        productId: 'product-1', configuration, renderImages: [], computedValues: {}, perspectives: ['front'],
        currentPerspective: 'front', statePrice: null, updates: {},
      },
    }));

    expect(result).not.toBe(previous);
    expect(previous.configurationError).not.toBeNull();
    expect(result.state).toEqual(configuration);
    expect(result.configurationError).toBeNull();
    expect(result.loading).toBeFalse();
  });

  it('cycles multiple perspectives and leaves zero or one perspective unchanged', () => {
    const state = { ...configurationInitialState, perspectives: ['front', 'back'], currentPerspective: 'front' };

    expect(configurationReducer(state, setPrevPerspective()).currentPerspective).toBe('back');
    expect(configurationReducer({ ...state, currentPerspective: 'back' }, setNextPerspective()).currentPerspective).toBe('front');
    expect(configurationReducer({ ...state, perspectives: [] }, setNextPerspective()).currentPerspective).toBe('front');
    expect(configurationReducer({ ...state, perspectives: ['front'] }, setNextPerspective()).currentPerspective).toBe('front');
  });

  it('navigates repeated enabled sections and leaves disabled sections out of the sequence', () => {
    const first = buildConfigurationSection({ id: 'first', repetition: 0 });
    const disabled = buildConfigurationSection({ id: 'disabled', repetition: 0, disabled: true });
    const repeated = buildConfigurationSection({ id: 'repeat', repetition: 1 });
    const state = { ...configurationInitialState, currentStep: { id: 'first', repetition: 0 }, state: buildConfiguration({ sections: [first, disabled, repeated] }) };

    expect(configurationReducer(state, setNextStep({ payload: null })).currentStep).toEqual({ id: 'repeat', repetition: 1 });
    expect(configurationReducer({ ...state, currentStep: { id: 'repeat', repetition: 1 } }, setPrevStep({ payload: null })).currentStep).toEqual({ id: 'first', repetition: 0 });
  });

  it('skips hidden sections because progress navigation does not render them', () => {
    const first = buildConfigurationSection({ id: 'first', repetition: 0 });
    const hidden = buildConfigurationSection({ id: 'hidden', repetition: 0, hidden: true });
    const last = buildConfigurationSection({ id: 'last', repetition: 0 });
    const state = { ...configurationInitialState, currentStep: { id: 'first', repetition: 0 }, state: buildConfiguration({ sections: [first, hidden, last] }) };

    expect(configurationReducer(state, setNextStep({ payload: null })).currentStep).toEqual({ id: 'last', repetition: 0 });
  });

  it('recovers a missing current step by selecting the first navigable section', () => {
    const first = buildConfigurationSection({ id: 'first', repetition: 0 });
    const state = { ...configurationInitialState, currentStep: null, state: buildConfiguration({ sections: [first] }) };

    expect(configurationReducer(state, setNextStep({ payload: null })).currentStep).toEqual({ id: 'first', repetition: 0 });
  });

  it('tracks touched state independently per section repetition', () => {
    const initial = { ...configurationInitialState, tempState: [{ sectionId: 'section-1', repetition: 0, touched: false }] };
    const updated = configurationReducer(initial, setSectionTouched({ payload: { sectionId: 'section-1', repetition: 0, touched: true } }));
    const repeated = configurationReducer(updated, setSectionTouched({ payload: { sectionId: 'section-1', repetition: 1, touched: true } }));

    expect(repeated.tempState).toEqual([
      { sectionId: 'section-1', repetition: 0, touched: true },
      { sectionId: 'section-1', repetition: 1, touched: true },
    ]);
  });

  it('retains only relevant configuration errors and stores the selected quantity', () => {
    const relevant = configurationReducer(configurationInitialState, onError({ message: errorMessage }));
    const irrelevant = configurationReducer(relevant, onError({ message: { ...errorMessage, errorType: 'Unauthorized' as MessageBusResponseMessage['errorType'] } }));

    expect(relevant.configurationError).toEqual({ errorType: errorMessage.errorType, errorPayload: { rule: 'width' } });
    expect(irrelevant.configurationError).toBeNull();
    expect(configurationReducer(configurationInitialState, setQuantity({ quantity: 4 })).quantity).toBe(4);
  });
});
