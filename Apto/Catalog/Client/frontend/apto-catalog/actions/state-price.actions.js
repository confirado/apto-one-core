const StatePriceActionsInject = ['MessageBusFactory', 'LanguageFactory'];
const StatePriceActions = function (MessageBusFactory, LanguageFactory) {
    const TYPE_NS = 'APTO_STATE_PRICE_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchCurrentStatePrice(productId, compressedState, shopCurrency, displayCurrency, customerGroup, sessionCookies, taxState) {
        if (!taxState) {
            taxState = null;
        }

        if (!customerGroup || !customerGroup.id) {
            customerGroup = {
                id: null
            }
        }

        if (!sessionCookies) {
            sessionCookies = [];
        }

        return {
            type: getType('FETCH_CURRENT_STATE_PRICE'),
            payload: MessageBusFactory.query('FindPriceByState', [productId, compressedState, shopCurrency, displayCurrency, customerGroup.id, LanguageFactory.activeLanguage.isocode, sessionCookies, taxState])
        }
    }

    return {
        fetchCurrentStatePrice: fetchCurrentStatePrice
    };
};

StatePriceActions.$inject = StatePriceActionsInject;

export default ['StatePriceActions', StatePriceActions];