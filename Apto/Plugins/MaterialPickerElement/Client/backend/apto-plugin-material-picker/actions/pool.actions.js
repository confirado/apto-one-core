const PoolActionsInject = ['MessageBusFactory', 'PageHeaderActions'];
const PoolActions = function (MessageBusFactory, PageHeaderActions) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_POOL_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchPool(id) {
        return {
            type: getType('FETCH_POOL'),
            payload: MessageBusFactory.query('FindMaterialPickerPool', [id])
        }
    }

    function fetchPoolItems(id) {
        return {
            type: getType('FETCH_POOL_ITEMS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolItems', [id])
        }
    }

    function fetchMaterials(poolId, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_MATERIALS'),
            payload: MessageBusFactory.query('FindMaterialPickerNotInPoolMaterials', [poolId, searchString])
        }
    }

    function fetchPriceGroups(searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_PRICE_GROUPS'),
            payload: MessageBusFactory.query('FindMaterialPickerPriceGroups', [searchString])
        }
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

    function fetchPoolsByPage(pageNumber, recordsPerPage, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_POOLS_BY_PAGE'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolsByPage', [pageNumber, recordsPerPage, searchString])
        };
    }

    function savePool(pool) {
        return dispatch => {
            let commandArguments = [];

            commandArguments.push(pool.name);

            if(typeof pool.id !== "undefined") {
                commandArguments.unshift(pool.id);
                return dispatch({
                    type: getType('UPDATE_POOL'),
                    payload: MessageBusFactory.command('UpdateMaterialPickerPool', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_POOL'),
                payload: MessageBusFactory.command('AddMaterialPickerPool', commandArguments)
            });
        }
    }

    function copyPool(id) {
        return {
            type: getType('COPY_POOL'),
            payload: MessageBusFactory.command('CopyMaterialPickerPool', [id])
        }
    }

    function removePool(id) {
        return {
            type: getType('REMOVE_POOL'),
            payload: MessageBusFactory.command('RemoveMaterialPickerPool', [id])
        }
    }

    function addPoolItem(id, materialId, priceGroupId) {
        return {
            type: getType('ADD_POOL_ITEM'),
            payload: MessageBusFactory.command('AddMaterialPickerPoolItem', [id, materialId, priceGroupId])
        }
    }

    function removePoolItem(id, poolItemId) {
        return {
            type: getType('REMOVE_POOL_ITEM'),
            payload: MessageBusFactory.command('RemoveMaterialPickerPoolItem', [id, poolItemId])
        }
    }

    function resetPool() {
        return {
            type: getType('RESET_POOL')
        }
    }

    function fetchAllMaterials(poolId) {
        return {
            type: getType('FETCH_MATERIALS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolItems', [poolId])
        }
    }

    return {
        setPageNumber: PageHeaderActions.setPageNumber(TYPE_NS),
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        fetchPool: fetchPool,
        fetchPoolItems: fetchPoolItems,
        fetchMaterials: fetchMaterials,
        fetchPriceGroups: fetchPriceGroups,
        fetchPools: fetchPools,
        fetchPoolsByPage: fetchPoolsByPage,
        savePool: savePool,
        copyPool: copyPool,
        removePool: removePool,
        addPoolItem: addPoolItem,
        removePoolItem: removePoolItem,
        resetPool: resetPool,
        fetchAllMaterials: fetchAllMaterials,
    };
};

PoolActions.$inject = PoolActionsInject;

export default ['MaterialPickerPoolActions', PoolActions];