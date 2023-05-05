const DefinitionActionsInject = ['MessageBusFactory'];
const DefinitionActions = function (MessageBusFactory) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_DEFINITION_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchPools(searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_POOLS'),
            payload: MessageBusFactory.query('FindMaterialPickerPools', [searchString])
        }
    }

    function fetchAllMaterials(poolId) {
        return {
            type: getType('FETCH_MATERIALS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolItems', [poolId])
        }
    }

    return {
        fetchPools: fetchPools,
        fetchAllMaterials: fetchAllMaterials,
    };
};

DefinitionActions.$inject = DefinitionActionsInject;

export default ['MaterialPickerDefinitionActions', DefinitionActions];