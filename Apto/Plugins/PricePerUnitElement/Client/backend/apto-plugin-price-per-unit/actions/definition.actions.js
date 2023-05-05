const DefinitionActionsInject = ['MessageBusFactory'];
const DefinitionActions = function (MessageBusFactory) {
    const TYPE_NS = 'PLUGIN_PRICE_PER_UNIT_DEFINITION_';
    const factory = {
        fetchSections: fetchSections,
        fetchAvailableCustomerGroups: fetchAvailableCustomerGroups,
        fetchPricePerUnitPrices: fetchPricePerUnitPrices,
        addPricePerUnitPrice: addPricePerUnitPrice,
        removePricePerUnitPrice: removePricePerUnitPrice
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchSections(productId) {
        return {
            type: getType('FETCH_SECTIONS'),
            payload: MessageBusFactory.query('FindProductSectionsElements', [productId])
        }
    }

    function fetchAvailableCustomerGroups() {
        return {
            type: getType('FETCH_AVAILABLE_CUSTOMER_GROUPS'),
            payload: MessageBusFactory.query('FindCustomerGroups', [''])
        }
    }

    function fetchPricePerUnitPrices(elementId) {
        return {
            type: getType('FETCH_PRICE_PER_UNIT_PRICES'),
            payload: MessageBusFactory.query('FindPricePerUnitPrices', [elementId])
        }
    }

    function addPricePerUnitPrice(productId, sectionId, elementId, amount, currencyCode, customerGroupId) {
        return {
            type: getType('ADD_PRICE_PER_UNIT_PRICE'),
            payload: MessageBusFactory.command('AddPricePerUnitPrice', [productId, sectionId, elementId, amount, currencyCode, customerGroupId])
        }
    }

    function removePricePerUnitPrice(elementId, priceId) {
        return {
            type: getType('REMOVE_PRICE_PER_UNIT_PRICE'),
            payload: MessageBusFactory.command('RemovePricePerUnitPrice', [elementId, priceId])
        }
    }

    return factory;
};

DefinitionActions.$inject = DefinitionActionsInject;

export default ['PricePerUnitDefinitionActions', DefinitionActions];