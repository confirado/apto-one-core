import update from 'immutability-helper';

const ReducerInject = ['AptoReducersProvider'];
const Reducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_AREA_ELEMENT_';
    const initialState = {
        priceMatrices: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_PRICE_MATRICES_FULFILLED'):
                state = update(state, {
                    priceMatrices: {
                        $set: action.payload.data.result.data
                    }
                });

                return state;
        }

        return state;
    };

    AptoReducersProvider.addReducer('areaElement', this.reducer);

    this.$get = function() {};
};

Reducer.$inject = ReducerInject;

export default ['AreaReducer', Reducer];