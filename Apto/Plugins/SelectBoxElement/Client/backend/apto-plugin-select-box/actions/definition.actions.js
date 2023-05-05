const DefinitionActionsInject = ['MessageBusFactory'];
const DefinitionActions = function (MessageBusFactory) {
    const TYPE_NS = 'PLUGIN_SELECT_BOX_DEFINITION_';
    const factory = {
        fetchSelectBoxItems: fetchSelectBoxItems,
        fetchSelectBoxItemDetail: fetchSelectBoxItemDetail,
        resetSelectBoxItemDetail: resetSelectBoxItemDetail,
        saveSelectBoxItemDetail: saveSelectBoxItemDetail,
        addSelectBoxItems: addSelectBoxItems,
        removeSelectBoxItem: removeSelectBoxItem,
        removeSelectBoxItems: removeSelectBoxItems,
        fetchSelectBoxItemPrices: fetchSelectBoxItemPrices,
        addSelectBoxItemPrice: addSelectBoxItemPrice,
        removeSelectBoxItemPrice: removeSelectBoxItemPrice,
        setSelectBoxItemIsDefault: setSelectBoxItemIsDefault
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchSelectBoxItems(elementId) {
        return {
            type: getType('FETCH_SELECT_BOX_ITEMS'),
            payload: MessageBusFactory.query('FindSelectBoxItems', [elementId])
        }
    }

    function fetchSelectBoxItemDetail(id) {
        return {
            type: getType('FETCH_SELECT_BOX_ITEM_DETAIL'),
            payload: MessageBusFactory.query('FindSelectBoxItem', [id])
        }
    }

    function resetSelectBoxItemDetail() {
        return {
            type: getType('RESET_SELECT_BOX_ITEM_DETAIL')
        }
    }

    function saveSelectBoxItemDetail(details) {
        return dispatch => {
            let commandArguments = [];

            commandArguments.push(details.productId);
            commandArguments.push(details.sectionId);
            commandArguments.push(details.elementId);
            commandArguments.push(details.name);

            if (typeof details.id !== 'undefined') {
                commandArguments.unshift(details.id);
                return dispatch({
                    type: getType('UPDATE_SELECT_BOX_ITEM_DETAIL'),
                    payload: MessageBusFactory.command('UpdateSelectBoxItem', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_SELECT_BOX_ITEM_DETAIL'),
                payload: MessageBusFactory.command('AddSelectBoxItem', commandArguments)
            });
        }
    }

    function addSelectBoxItems(productId, sectionId, elementId, items) {
        return {
            type: getType('ADD_SELECT_BOX_ITEMS'),
            payload: MessageBusFactory.command('AddSelectBoxItems', [productId, sectionId, elementId, items])
        }
    }

    function removeSelectBoxItem(id) {
        return {
            type: getType('REMOVE_SELECT_BOX_ITEM'),
            payload: MessageBusFactory.command('RemoveSelectBoxItem', [id])
        };
    }

    function removeSelectBoxItems(ids) {
        return {
            type: getType('REMOVE_SELECT_BOX_ITEMS'),
            payload: MessageBusFactory.command('RemoveSelectBoxItems', [ids])
        };
    }

    function fetchSelectBoxItemPrices(selectBoxItemId) {
        return {
            type: getType('FETCH_SELECT_BOX_ITEM_PRICES'),
            payload: MessageBusFactory.query('FindSelectBoxItemPrices', [selectBoxItemId])
        }
    }

    function addSelectBoxItemPrice(selectBoxItemId, amount, currencyCode, customerGroupId) {
        return {
            type: getType('ADD_SELECT_BOX_ITEM_PRICE'),
            payload: MessageBusFactory.command('AddSelectBoxItemPrice', [selectBoxItemId, amount, currencyCode, customerGroupId])
        }
    }

    function removeSelectBoxItemPrice(selectBoxItemId, priceId) {
        return {
            type: getType('REMOVE_SELECT_BOX_ITEM_PRICE'),
            payload: MessageBusFactory.command('RemoveSelectBoxItemPrice', [selectBoxItemId, priceId])
        }
    }

    function setSelectBoxItemIsDefault(elementId, selectBoxItemId, isDefault) {
        return {
            type: getType('SET_SELECT_BOX_ITEM_IS_DEFAULT'),
            payload: MessageBusFactory.command('SetSelectBoxItemIsDefault', [elementId, selectBoxItemId, isDefault])
        }
    }

    return factory;
};

DefinitionActions.$inject = DefinitionActionsInject;

export default ['SelectBoxDefinitionActions', DefinitionActions];