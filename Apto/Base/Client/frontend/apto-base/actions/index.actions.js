const IndexActionsInject = ['$timeout', 'MessageBusFactory', 'XDomainRequestFactory', 'APTO_SHOP_CONTEXT'];
const IndexActions = function($timeout, MessageBusFactory, XDomainRequestFactory, APTO_SHOP_CONTEXT) {
    const TYPE_NS = 'APTO_INDEX_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function languagesFetch() {
        return {
            type: getType('LANGUAGES_FETCH'),
            payload: MessageBusFactory.query('FindLanguages', [])
        }
    }

    function setActiveLanguage(payload) {
        return {
            type: getType('SET_ACTIVE_LANGUAGE'),
            payload: payload
        }
    }

    function setLocale(payload) {
        return {
            type: getType('SET_LOCALE'),
            payload: MessageBusFactory.setLocale(payload)
        }
    }

    function shopSessionFetch(locale) {
        return (dispatch, getState) => {
            // please use state in action creators rarely, this is normally not the place where we access the redux store
            if (getState().index.shopConnectorConfigured) {
                return dispatch({
                    type: getType('SHOP_SESSION_FETCH'),
                    payload: XDomainRequestFactory.query('GetState', [], locale)
                });
            } else {
                return dispatch({
                    type: getType('SHOP_SESSION_NOT_AVAILABLE')
                });
            }
        };
    }

    function addGuestConfigurationToCustomer(snippet, user, articleNumber) {
        return {
            type: getType('ADD_GUEST_CONFIGURATION_TO_CUSTOMER'),
            payload: XDomainRequestFactory.query('AddGuestConfigurationToCustomer', [snippet, user, articleNumber])
        }
    }

    function shopRemoveFromBasket(payload) {
        return (dispatch, getState) => {
            // please use state in action creators rarely, this is normally not the place where we access the redux store
            if (getState().index.shopConnectorConfigured) {
                return dispatch({
                    type: getType('SHOP_REMOVE_FROM_BASKET'),
                    payload: XDomainRequestFactory.query('RemoveFromBasket', [payload])
                })
            } else {
                dispatch({
                    type: getType('SHOP_SESSION_NOT_AVAILABLE')
                });
            }
        };
    }

    function setQuantity(quantity) {
        return {
            type: getType('SET_QUANTITY'),
            payload: quantity
        };
    }

    function openSidebarRight() {
        return (dispatch) => {
            dispatch({
                type: getType('OPEN_SIDEBAR_RIGHT')
            });
            dispatch({
                type: getType('SET_SIDEBAR_RIGHT_HTML')
            });
        };
    }

    function closeSidebarRight() {
        return (dispatch) => {
            dispatch({
                type: getType('CLOSE_SIDEBAR_RIGHT')
            });
            $timeout(function () {
                dispatch({
                    type: getType('SET_SIDEBAR_RIGHT_HTML')
                });
            }, 200);
        };
    }

    function setScrollbarWidth(width) {
        if (typeof width === "undefined") {
            width = 0;
        }
        return {
            type: getType('SET_SCROLLBAR_WIDTH'),
            payload: width
        }
    }

    function setMetaData(metaData) {
        return {
            type: getType('SET_META_DATA'),
            payload: metaData
        }
    }

    function fetchShopSessionCustomerGroupByExternalId(customerGroup) {
        return {
            type: getType('FETCH_SHOP_SESSION_CUSTOMER_GROUP_BY_EXTERNAL_ID'),
            payload: MessageBusFactory.query('FindCustomerGroupByShopAndExternalId', [APTO_SHOP_CONTEXT.id, customerGroup])
        }
    }

    return {
        languagesFetch: languagesFetch,
        setActiveLanguage: setActiveLanguage,
        shopSessionFetch: shopSessionFetch,
        addGuestConfigurationToCustomer: addGuestConfigurationToCustomer,
        shopRemoveFromBasket: shopRemoveFromBasket,
        setQuantity: setQuantity,
        openSidebarRight: openSidebarRight,
        closeSidebarRight: closeSidebarRight,
        setScrollbarWidth: setScrollbarWidth,
        setLocale: setLocale,
        setMetaData: setMetaData,
        fetchShopSessionCustomerGroupByExternalId: fetchShopSessionCustomerGroupByExternalId
    };
};

IndexActions.$inject = IndexActionsInject;

export default ['IndexActions', IndexActions];