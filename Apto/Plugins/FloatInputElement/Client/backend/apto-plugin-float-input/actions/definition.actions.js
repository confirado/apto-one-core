const DefinitionActionsInject = ['MessageBusFactory'];
const DefinitionActions = function (MessageBusFactory) {
    const TYPE_NS = 'PLUGIN_FLOAT_INPUT_DEFINITION_';
    const factory = {
        fetchAvailableCustomerGroups: fetchAvailableCustomerGroups,
        fetchFloatInputPrices: fetchFloatInputPrices,
        addFloatInputPrice: addFloatInputPrice,
        removeFloatInputPrice: removeFloatInputPrice
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchAvailableCustomerGroups() {
        return {
            type: getType('FETCH_AVAILABLE_CUSTOMER_GROUPS'),
            payload: MessageBusFactory.query('FindCustomerGroups', [''])
        }
    }

    function fetchFloatInputPrices(elementId) {
        return {
            type: getType('FETCH_FLOAT_INPUT_PRICES'),
            payload: MessageBusFactory.query('FindFloatInputPrices', [elementId])
        }
    }

    function addFloatInputPrice(productId, sectionId, elementId, amount, currencyCode, customerGroupId) {
        return {
            type: getType('ADD_FLOAT_INPUT_PRICE'),
            payload: MessageBusFactory.command('AddFloatInputPrice', [productId, sectionId, elementId, amount, currencyCode, customerGroupId])
        }
    }

    function removeFloatInputPrice(elementId, priceId) {
        return {
            type: getType('REMOVE_FLOAT_INPUT_PRICE'),
            payload: MessageBusFactory.command('RemoveFloatInputPrice', [elementId, priceId])
        }
    }

    return factory;
};

DefinitionActions.$inject = DefinitionActionsInject;

export default ['FloatInputDefinitionActions', DefinitionActions];