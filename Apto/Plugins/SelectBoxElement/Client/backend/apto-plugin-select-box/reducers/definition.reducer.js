import update from 'immutability-helper';

const DefinitionReducerInject = ['AptoReducersProvider'];
const DefinitionReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_SELECT_BOX_DEFINITION_';
    const initialState = {
        selectBoxItems: [],
        selectBoxItemDetail: {
            name: []
        },
        selectBoxItemPrices: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_SELECT_BOX_ITEMS_FULFILLED'):
                state = update(state, {
                    selectBoxItems: {
                        $set: action.payload.data.result.data
                    }
                });
                break;

            case getType('FETCH_SELECT_BOX_ITEM_DETAIL_FULFILLED'):
                state = update(state, {
                    selectBoxItemDetail: {
                        $set: action.payload.data.result
                    }
                });
                break;

            case getType('RESET_SELECT_BOX_ITEM_DETAIL'):
                state = update(state, {
                    selectBoxItemDetail: {
                        $set: angular.copy(initialState.selectBoxItemDetail)
                    }
                });
                break;

            case getType('FETCH_SELECT_BOX_ITEM_PRICES_FULFILLED'):
                state = update(state, {
                    selectBoxItemPrices: {
                        $set: action.payload.data.result
                    }
                });
                break;
        }

        return state;
    };

    AptoReducersProvider.addReducer('selectBoxDefinition', this.reducer);

    this.$get = function() {};
};

DefinitionReducer.$inject = DefinitionReducerInject;

export default ['SelectBoxDefinitionReducer', DefinitionReducer];