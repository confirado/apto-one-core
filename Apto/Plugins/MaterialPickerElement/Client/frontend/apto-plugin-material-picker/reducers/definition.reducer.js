import update from 'immutability-helper';
import immutable from 'object-path-immutable';

const DefinitionReducerInject = ['AptoReducersProvider'];
const DefinitionReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_DEFINITION_';
    const initialState = {
        poolItems: [],
        poolItemsPopular: [],
        priceGroups: [],
        propertyGroups: [],
        numberOfMaterials: 0,
        poolItemsPopularLimit: 8,
        colors: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_POOL_ITEMS_FULFILLED'):
                const poolItems = action.payload.data.result.data;

                let statePoolItems = [],
                    statePoolItemsPopular = [];

                for(let i = 0; i < poolItems.length; i++) {
                    const poolItem = poolItems[i];

                    if (i < state.poolItemsPopularLimit) {
                        // push row into popular pool items
                        statePoolItemsPopular.push(poolItem);
                    } else {
                        // push row into pool items
                        statePoolItems.push(poolItem);
                    }
                }

                state = update(state, {
                    poolItems: {
                        $set: statePoolItems
                    },
                    poolItemsPopular: {
                        $set: statePoolItemsPopular
                    },
                    numberOfMaterials: {
                        $set: action.payload.data.result.numberOfRecords
                    }
                });
                break;
            case getType('FETCH_POOL_PRICE_GROUPS_FULFILLED'):
                state = update(state, {
                    priceGroups: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('FETCH_POOL_PROPERTY_GROUPS_FULFILLED'):
                state = update(state, {
                    propertyGroups: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('FETCH_POOL_COLORS_FULFILLED'):
                state = update(state, {
                    colors: {
                        $set: action.payload.data.result
                    }
                });
                break;
        }

        return state;
    };

    AptoReducersProvider.addReducer('pluginMaterialPickerDefinition', this.reducer);

    this.$get = function() {};
};

DefinitionReducer.$inject = DefinitionReducerInject;

export default ['MaterialPickerDefinitionReducer', DefinitionReducer];
