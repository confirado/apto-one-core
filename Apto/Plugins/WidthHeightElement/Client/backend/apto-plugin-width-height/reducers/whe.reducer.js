import update from 'immutability-helper';

const WidthHeightElementReducerInject = ['AptoReducersProvider'];
const WidthHeightElementReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_WIDTH_HEIGHT_ELEMENT_';
    const initialState = {
        priceMatrices: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.widthHeightElement = function (state, action) {
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

    AptoReducersProvider.addReducer('widthHeightElement', this.widthHeightElement);

    this.$get = function() {};
};

WidthHeightElementReducer.$inject = WidthHeightElementReducerInject;

export default ['WidthHeightElementReducer', WidthHeightElementReducer];