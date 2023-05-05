import update from 'immutability-helper';

const DefinitionReducerInject = ['AptoReducersProvider'];
const DefinitionReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_FLOAT_INPUT_DEFINITION_';
    const initialState = {
        availableCustomerGroups: [],
        floatInputPrices: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_AVAILABLE_CUSTOMER_GROUPS_FULFILLED'):
                state = update(state, {
                    availableCustomerGroups: {
                        $set: action.payload.data.result.data
                    }
                });
                break;

            case getType('FETCH_FLOAT_INPUT_PRICES_FULFILLED'):
                state = update(state, {
                    floatInputPrices: {
                        $set: action.payload.data.result
                    }
                });
                break;
        }

        return state;
    };

    AptoReducersProvider.addReducer('floatInputDefinition', this.reducer);

    this.$get = function() {};
};

DefinitionReducer.$inject = DefinitionReducerInject;

export default ['FloatInputDefinitionReducer', DefinitionReducer];