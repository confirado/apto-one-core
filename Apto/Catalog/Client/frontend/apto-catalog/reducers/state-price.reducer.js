import update from 'immutability-helper';

const StatePriceReducerInject = ['AptoReducersProvider'];
const StatePriceReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_STATE_PRICE_';
    const initialState = {
        statePrice: {
            currency: 'EUR',
            discount: {
                description: null,
                discount: 0,
                name: null
            },
            own: {
                price: {},
                pseudoPrice: {}
            },
            sum: {
                price: {},
                pseudoPrice: {},
                netPrice: {},
                grossPrice: {}
            },
            sections: {}
        }
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_CURRENT_STATE_PRICE_FULFILLED'): {
                state = update(state, {
                    statePrice: {
                        $set: action.payload.data.result
                    }
                });
                return state;
            }
        }

        return state;
    };

    AptoReducersProvider.addReducer('statePrice', this.reducer);

    this.$get = function() {};
};

StatePriceReducer.$inject = StatePriceReducerInject;

export default ['StatePriceReducer', StatePriceReducer];