import undoable, { includeAction } from 'redux-undo';
import update from 'immutability-helper';
import immutable from 'object-path-immutable';

const ConfigurationReducerInject = ['AptoReducersProvider', 'AptoExtendProvider', 'LanguageFactoryProvider'];
const ConfigurationReducer = function(AptoReducersProvider, AptoExtendProvider, LanguageFactoryProvider) {
    const TYPE_NS = 'APTO_CONFIGURATION_';
    const self = this;
    const initialState = {
        raw: {
            product: null,
            configuration: null
        },
        productView: 'configuration',
        configurationId: null,
        configurationType: null,
        configurationState: {},
        sortOrderSections: [],
        rules: [],
        acceptedSoftRules: [],
        affectedRules: [],
        failedRules: [],
        failedHardRules: [],
        initialized: false,
        productId: '',
        productSeoUrl: null,
        useStepByStep: false,
        toggleState: false
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function initReducerState(state) {
        if (!state.raw.product) {
            return state;
        } else {
            // init product
            state = initAptoProduct(state);

            // init configuration
            if (state.raw.configuration) {
                state = initAptoConfiguration(state);
            }
        }

        // return state
        return state;
    }

    function initAptoProduct(state) {
        let newSortOrderSections = [];
        for (let i = 0; i < state.raw.product.sections.length; i++) {
            newSortOrderSections.push(state.raw.product.sections[i].id);
        }

        state = update(state, {
            productId: {
                $set: state.raw.product.id
            },
            productSeoUrl: {
                $set: state.raw.product.seoUrl
            },
            useStepByStep: {
                $set: state.raw.product.useStepByStep
            }
        });

        state = update(state, {
            sortOrderSections: {
                $set: newSortOrderSections
            }
        });

        return state;
    }

    function initAptoConfiguration(state) {
        state = update(state, {
            configurationId: {
                $set: state.raw.configuration.id
            },
            configurationType: {
                $set: state.raw.configuration.type
            }
        });

        return state;
    }

    function getElementErrorMessage(state, sectionId, elementId) {
        if (
            !state.configurationState[sectionId] ||
            !state.configurationState[sectionId]['elements'] ||
            !state.configurationState[sectionId]['elements'][elementId] ||
            !state.configurationState[sectionId]['elements'][elementId]['errorMessage']
        ) {
            return null;
        }

        return state.configurationState[sectionId]['elements'][elementId]['errorMessage'];
    }

    function configuration (state, action) {
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('IS_INITIALIZED'): {
                return update(state, {
                    initialized: {
                        $set: action.payload
                    }
                });
            }

            case getType('INIT_PRODUCT'): {
                // init state with default values
                state = angular.copy(initialState);

                // set raw product
                state = update(state, {
                    raw: {
                        product: {
                            $set: action.payload.product
                        }
                    }
                });

                // set raw configuration
                state = update(state, {
                    raw: {
                        configuration: {
                            $set: action.payload.configuration
                        }
                    }
                });

                // init reducer
                return initReducerState(state);
            }

            case getType('GET_CONFIGURATION_STATE_FULFILLED'): {
                let message = action.payload.data.message;

                if (message.error) {
                    switch (message.errorType) {
                        case 'InitConfigurationStateException': {
                            throw {
                                initStateException: true,
                                messages: [
                                    {
                                        de_DE: message.message,
                                        en_EN: message.message
                                    }
                                ]
                            };
                        }
                        case 'InvalidConfigurationStateChangeException':
                        case 'InvalidStateException': {
                            let sectionId = message.errorPayload.section;
                            let elementId = message.errorPayload.element;
                            const elementErrorMessage = getElementErrorMessage(state, sectionId, elementId);

                            let msg = LanguageFactoryProvider.merge(
                                elementErrorMessage ? elementErrorMessage : {},
                                {
                                    de_DE: 'Der eingegebene Wert ist unzulässig.',
                                    en_EN: 'The entered value is not acceptable.'
                                }
                            );

                            throw {
                                validateValueException: true,
                                messages: [msg]
                            };
                        }
                        case 'FailedRulesException': {
                            self.throwFailedRulesException(message.errorPayload)
                            return state;
                        }
                        default: {
                            return state;
                        }
                    }
                }

                state = update(state, {
                    configurationState: {
                        $set: action.payload.data.result.configurationState
                    }
                });
                return state;
            }

            case getType('SET_ACCEPTED_SOFT_RULES'): {
                let acceptedSoftRules = angular.copy(state.acceptedSoftRules);

                acceptedSoftRules = acceptedSoftRules.concat(action.payload.acceptedSoftRules);
                for (let i = 0; i < acceptedSoftRules.length; ++i) {
                    for (let j = i + 1; j < acceptedSoftRules.length; ++j) {
                        if (acceptedSoftRules[i] === acceptedSoftRules[j])
                            acceptedSoftRules.splice(j--, 1);
                    }
                }

                state = update(state, {
                    acceptedSoftRules: {
                        $set: acceptedSoftRules
                    }
                });

                return state;
            }

            case getType('SET_CONFIGURATION_ID'): {
                state = update(state, {
                    configurationId: {
                        $set: action.payload
                    }
                });
                return state;
            }

            case getType('SET_CONFIGURATION_TYPE'): {
                state = update(state, {
                    configurationType: {
                        $set: action.payload
                    }
                });
                return state;
            }

            case getType('ADD_STATE_TO_HISTORY'): {
                // push state to history by simple change a boolean value (this is not really a nice solution, maybe we find another way to force redux thunk to push state to history)
                state = update(state, {
                    toggleState: {
                        $set: !state.toggleState
                    }
                });

                return state;
            }

            case getType('SET_PRODUCT_VIEW'): {
                state = update(state, {
                    productView: {
                        $set: action.payload
                    }
                });
                return state;
            }
        }

        return state;
    }

    self.throwFailedRulesException = function (failedRules) {
        let messages = [], messagesSoftRules = [] , hardRule = false;

        for (let i = 0; i < failedRules.length; i++) {
            if(!failedRules[i].softRule){
                hardRule = true;
                messages.push(failedRules[i].errorMessage);
            } else {
                // @todo op-10902
                // save accepted soft rules
                // old code:
                //if (acceptedSoftRules.indexOf(failedRules[i].id) === -1) {
                //    messagesSoftRules.push(failedRules[i].errorMessage);
                //}
                messagesSoftRules.push(failedRules[i].errorMessage);
            }
        }

        throw {
            failedRules: failedRules,
            messages: hardRule ? messages : messagesSoftRules,
            hardRule: hardRule
        };
    };

    AptoReducersProvider.addReducer('configuration', undoable(configuration, { filter: includeAction([getType('ADD_STATE_TO_HISTORY')]) }));
    self.$get = function() {};
};

ConfigurationReducer.$inject = ConfigurationReducerInject;

export default ['ConfigurationReducer', ConfigurationReducer];