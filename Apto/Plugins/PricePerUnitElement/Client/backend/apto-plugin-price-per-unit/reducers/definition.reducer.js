import update from 'immutability-helper';

const DefinitionReducerInject = ['AptoReducersProvider'];
const DefinitionReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_PRICE_PER_UNIT_DEFINITION_';
    const initialState = {
        sections: [],
        availableCustomerGroups: [],
        pricePerUnitPrices: [],
        sectionIdentifiers: [],
        elementIdentifiers: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
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

            case getType('FETCH_AVAILABLE_CUSTOMER_GROUPS_FULFILLED'):
                state = update(state, {
                    availableCustomerGroups: {
                        $set: action.payload.data.result.data
                    }
                });
                break;

            case getType('FETCH_PRICE_PER_UNIT_PRICES_FULFILLED'):
                state = update(state, {
                    pricePerUnitPrices: {
                        $set: action.payload.data.result
                    }
                });
                break;
        }

        return state;
    };

    AptoReducersProvider.addReducer('pricePerUnitDefinition', this.reducer);

    this.$get = function() {};
};

DefinitionReducer.$inject = DefinitionReducerInject;

export default ['PricePerUnitDefinitionReducer', DefinitionReducer];