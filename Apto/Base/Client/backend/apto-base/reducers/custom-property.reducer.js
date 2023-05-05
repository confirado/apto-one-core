import update from 'immutability-helper';

const ReducerInject = ['AptoReducersProvider'];
const Reducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_CUSTOM_PROPERTY_';
    const initialState = {
        usedCustomPropertyKeys: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_USED_CUSTOM_PROPERTY_KEYS_FULFILLED'):
                newState = update(state, {
                    usedCustomPropertyKeys: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('customProperty', this.reducer);

    this.$get = function() {};
};

Reducer.$inject = ReducerInject;

export default ['CustomPropertyReducer', Reducer];