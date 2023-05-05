import update from 'immutability-helper';

const DefinitionReducerInject = ['AptoReducersProvider'];
const DefinitionReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_DEFINITION_';
    const initialState = {
        pools: [],
        poolMaterials: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_POOLS_FULFILLED'):
                state = update(state, {
                    pools: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('FETCH_MATERIALS_FULFILLED'):
                state = update(state, {
                    poolMaterials: {
                        $set: action.payload.data.result.data
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