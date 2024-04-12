import update from 'immutability-helper';

const SectionReducerInject = ['AptoReducersProvider'];
const SectionReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_SECTION_';
    const initialState = {
        detail: {
            name: {},
            allowMultiple: false
        },
        elements: [],
        prices: [],
        discounts: [],
        groups: [],
        customProperties: [],
        conditions: [],
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.section = function (state, action) {
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
            case getType('FETCH_ELEMENTS_FULFILLED'):
                newState = update(state, {
                    elements: {
                        $set: action.payload.data.result.elements
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
            case getType('FETCH_DISCOUNTS_FULFILLED'):
                newState = update(state, {
                    discounts: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('FETCH_GROUPS_FULFILLED'):
                newState = update(state, {
                    groups: {
                        $set: action.payload.data.result.data
                    }
                });
                return newState;
            case getType('FETCH_CONDITIONS_FULFILLED'):
                newState = update(state, {
                    conditions: {
                        $set: action.payload.data.result.conditions
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
            case getType('SET_DETAIL_VALUE'):
                let detailUpdate = {};
                detailUpdate[action.payload.key] = {
                    $set: action.payload.value
                };

                newState = update(state, {
                    detail: detailUpdate
                });
                return newState;
            case getType('RESET'):
                newState = update(state, {
                    $set: angular.copy(initialState)
                });
                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('section', this.section);

    this.$get = function() {};
};

SectionReducer.$inject = SectionReducerInject;

export default ['SectionReducer', SectionReducer];
