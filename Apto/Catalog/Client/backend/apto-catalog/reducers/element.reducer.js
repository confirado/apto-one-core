import update from 'immutability-helper';

const ElementReducerInject = ['AptoReducersProvider'];
const ElementReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_ELEMENT_';
    const initialState = {
        detail: {
            name: {},
            errorMessage: {}
        },
        definition: {
            className: '',
            values: {}
        },
        registeredDefinitions: [],
        prices: [],
        priceFormulas: [],
        discounts: [],
        sections: [],
        sectionIdentifiers: [],
        elementIdentifiers: [],
        renderImages: [],
        customProperties: [],
        attachments: [],
        gallery: [],
        availablePriceMatrices: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.element = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_DETAIL_FULFILLED'):
                newState = update(state, {
                    detail: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('FETCH_REGISTERED_DEFINITIONS_FULFILLED'):
                newState = update(state, {
                    registeredDefinitions: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('FETCH_PRICES_FULFILLED'):
                newState = update(state, {
                    prices: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('FETCH_PRICE_FORMULAS_FULFILLED'):
                newState = update(state, {
                    priceFormulas: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('FETCH_DISCOUNTS_FULFILLED'):
                newState = update(state, {
                    discounts: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('FETCH_SECTIONS_FULFILLED'):
                const sectionsResult = action.payload.data.result.sections;
                let sections = [];
                let sectionIdentifiers = {};
                let elementIdentifiers = {};

                for (let i = 0; i < sectionsResult.length; i++) {
                    let selectionResult = sectionsResult[i];
                    let sectionHasSpecialElements = false;

                    for (let j = 0; j < selectionResult.elements.length; j++) {
                        let resultElement = selectionResult.elements[j];
                        if (resultElement.definition.properties) {
                            sectionHasSpecialElements = true;
                            break;
                        }
                    }

                    if (sectionHasSpecialElements) {
                        sections.push(selectionResult);
                    }
                }

                for (let i = 0; i < sections.length; i++) {
                    const section = sections[i];
                    sectionIdentifiers[section.id] = section.identifier;

                    for (let i = 0; i < section.elements.length; i++) {
                        const element = section.elements[i];
                        elementIdentifiers[element.id] = element.identifier;
                    }
                }

                state = update(state, {
                    sections: {
                        $set: sections
                    },
                    sectionIdentifiers: {
                        $set: sectionIdentifiers
                    },
                    elementIdentifiers: {
                        $set: elementIdentifiers
                    }
                });

                break;
            case getType('FETCH_RENDER_IMAGES_FULFILLED'):
                newState = update(state, {
                    renderImages: {
                        $set: action.payload.data.result.renderImages
                    }
                });
                return newState;
            case getType('FETCH_CUSTOM_PROPERTIES_FULFILLED'):
                newState = update(state, {
                    customProperties: {
                        $set: action.payload.data.result.customProperties
                    }
                });
                return newState;
            case getType('SET_DEFINITION_CLASS_NAME'):
                newState = update(state, {
                    definition: {
                        className: {
                            $set: action.payload
                        }
                    }
                });
                return newState;
            case getType('SET_DEFINITION_VALUES'):
                newState = update(state, {
                    definition: {
                        values: {
                            $set: action.payload
                        }
                    }
                });
                return newState;
            case getType('SET_DETAIL_VALUE'):
                let detailUpdate = {};
                detailUpdate[action.payload.key] = {
                    $set: action.payload.value
                };

                newState = update(state, {
                    detail: detailUpdate
                });
                return newState;
            case getType('RESET_DEFINITION_VALUES'):
                newState = update(state, {
                    definition: {
                        values: {
                            $set: angular.copy(initialState.definition.values)
                        }
                    }
                });
                return newState;
            case getType('RESET'):
                newState = update(state, {
                    $set: angular.copy(initialState)
                });
                return newState;
            case getType('FETCH_ATTACHMENTS_FULFILLED'):
                newState = update(state, {
                    attachments: {
                        $set: action.payload.data.result.attachments
                    }
                });
                return newState;
            case getType('FETCH_GALLERY_FULFILLED'):
                newState = update(state, {
                    gallery: {
                        $set: action.payload.data.result.gallery
                    }
                });
                return newState;
            case getType('FETCH_PRICE_MATRICES_FULFILLED'):
                state = update(state, {
                    availablePriceMatrices: {
                        $set: action.payload.data.result.data
                    }
                });

                return state;
        }

        return state;
    };

    AptoReducersProvider.addReducer('element', this.element);

    this.$get = function() {};
};

ElementReducer.$inject = ElementReducerInject;

export default ['ElementReducer', ElementReducer];
