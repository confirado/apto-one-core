const PriceGroupActionsInject = ['MessageBusFactory', 'PageHeaderActions'];
const PriceGroupActions = function (MessageBusFactory, PageHeaderActions) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_PRICE_GROUP_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchPriceGroup(id) {
        return {
            type: getType('FETCH_PRICE_GROUP'),
            payload: MessageBusFactory.query('FindMaterialPickerPriceGroup', [id])
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

    function fetchPriceGroupsByPage(pageNumber, recordsPerPage, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_PRICE_GROUPS_BY_PAGE'),
            payload: MessageBusFactory.query('FindMaterialPickerPriceGroupsByPage', [pageNumber, recordsPerPage, searchString])
        };
    }

    function fetchPriceMatrices(searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_PRICE_MATRICES'),
            payload: MessageBusFactory.query('FindPriceMatrices', [searchString])
        };
    }

    function savePriceGroup(priceGroup) {
        return dispatch => {
            let commandArguments = [];

            commandArguments.push(priceGroup.name);
            commandArguments.push(priceGroup.internalName);
            commandArguments.push(priceGroup.additionalCharge);
            commandArguments.push(priceGroup.priceMatrix);

            if(typeof priceGroup.id !== "undefined") {
                commandArguments.unshift(priceGroup.id);
                return dispatch({
                    type: getType('UPDATE_PRICE_GROUP'),
                    payload: MessageBusFactory.command('UpdateMaterialPickerPriceGroup', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_PRICE_GROUP'),
                payload: MessageBusFactory.command('AddMaterialPickerPriceGroup', commandArguments)
            });
        }
    }

    function removePriceGroup(id) {
        return {
            type: getType('REMOVE_PRICE_GROUP'),
            payload: MessageBusFactory.command('RemoveMaterialPickerPriceGroup', [id])
        }
    }

    function resetPriceGroup() {
        return {
            type: getType('RESET_PRICE_GROUP')
        }
    }

    return {
        setPageNumber: PageHeaderActions.setPageNumber(TYPE_NS),
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        fetchPriceGroup: fetchPriceGroup,
        fetchPriceGroups: fetchPriceGroups,
        fetchPriceGroupsByPage: fetchPriceGroupsByPage,
        fetchPriceMatrices: fetchPriceMatrices,
        savePriceGroup: savePriceGroup,
        removePriceGroup: removePriceGroup,
        resetPriceGroup: resetPriceGroup
    };
};

PriceGroupActions.$inject = PriceGroupActionsInject;

export default ['MaterialPickerPriceGroupActions', PriceGroupActions];