import SortOptions from "../../../enums/sortOptions";

const DefinitionActionsInject = ['MessageBusFactory'];
const DefinitionActions = function (MessageBusFactory) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_DEFINITION_';
    const sortOptions = SortOptions;

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchPoolItems(poolId, filter, sortBy = sortOptions.CLICKS.id, orderBy = 'asc') {
        return {
            type: getType('FETCH_POOL_ITEMS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolItemsFiltered', [poolId, filter, sortBy, orderBy])
        }
    }

    function fetchPoolPriceGroups(poolId) {
        return {
            type: getType('FETCH_POOL_PRICE_GROUPS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolPriceGroups', [poolId])
        }
    }

    function fetchPoolPropertyGroups(poolId) {
        return {
            type: getType('FETCH_POOL_PROPERTY_GROUPS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolPropertyGroups', [poolId])
        }
    }

    function incrementMaterialClicks(materialId) {
        return {
            type: getType('INCREMENT_MATERIAL_CLICKS'),
            payload: MessageBusFactory.command('IncrementMaterialPickerMaterialClicks', [materialId])
        }
    }

    function fetchPoolColors(poolId, filter) {
        return {
            type: getType('FETCH_POOL_COLORS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolColors', [poolId, filter])
        }
    }

    return {
        fetchPoolItems: fetchPoolItems,
        fetchPoolPriceGroups: fetchPoolPriceGroups,
        fetchPoolPropertyGroups: fetchPoolPropertyGroups,
        incrementMaterialClicks: incrementMaterialClicks,
        fetchPoolColors: fetchPoolColors
    };
};

DefinitionActions.$inject = DefinitionActionsInject;

export default ['MaterialPickerDefinitionActions', DefinitionActions];
