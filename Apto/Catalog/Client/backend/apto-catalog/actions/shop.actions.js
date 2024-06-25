const ShopActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const ShopActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_SHOP_';
    const factory = {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        availableCategoriesFetch: availableCategoriesFetch,
        availableLanguagesFetch: availableLanguagesFetch,
        shopDetailAssignCategories: shopDetailAssignCategories,
        shopDetailAssignLanguages: shopDetailAssignLanguages,
        shopDetailFetch: shopDetailFetch,
        shopDetailSave: shopDetailSave,
        shopDetailReset: shopDetailReset,
        shopRemove: shopRemove,
        shopsFetch: shopsFetch,
        fetchShopCustomProperties: fetchShopCustomProperties,
        addShopCustomProperty: addShopCustomProperty,
        removeShopCustomProperty: removeShopCustomProperty,
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function shopsFetch(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('SHOPS_FETCH'),
                payload: MessageBusFactory.query('FindShops', [searchString])
            });
        };
    }

    function shopDetailFetch(id) {
        return {
            type: getType('SHOP_DETAIL_FETCH'),
            payload: MessageBusFactory.query('FindShop', [id])
        }
    }

    function availableCategoriesFetch() {
        return {
            type: getType('AVAILABLE_CATEGORIES_FETCH'),
            payload: MessageBusFactory.query('FindCategories', [''])
        }
    }

    function availableLanguagesFetch() {
        return {
            type: getType('AVAILABLE_LANGUAGES_FETCH'),
            payload: MessageBusFactory.query('FindLanguages', [''])
        }
    }

    function availableCustomerGroupsFetch() {
        return {
            type: getType('AVAILABLE_CUSTOMER_GROUPS_FETCH'),
            payload: MessageBusFactory.query('FindCustomerGroups', [''])
        }
    }

    function shopDetailAssignCategories(categories) {
        return {
            type: getType('SHOP_DETAIL_ASSIGN_CATEGORIES'),
            payload: categories
        }
    }

    function shopDetailAssignLanguages(languages) {
        return {
            type: getType('SHOP_DETAIL_ASSIGN_LANGUAGES'),
            payload: languages
        }
    }

    function shopDetailSave(shopDetail) {
        return dispatch => {
            let commandArguments = [];

            if(typeof shopDetail.categories === "undefined") {
                shopDetail.categories = [];
            }

            if(typeof shopDetail.languages === "undefined") {
                shopDetail.languages = [];
            }

            if (
                typeof shopDetail.connectorUrl === "undefined" ||
                null === shopDetail.connectorUrl
            ) {
                shopDetail.connectorUrl = '';
            }

            if (
                typeof shopDetail.connectorToken === "undefined" ||
                null === shopDetail.connectorToken
            ) {
                shopDetail.connectorToken = '';
            }

            commandArguments.push(shopDetail.name);
            commandArguments.push(shopDetail.domain);
            commandArguments.push(shopDetail.connectorUrl);
            commandArguments.push(shopDetail.connectorToken);
            commandArguments.push(shopDetail.templateId);
            commandArguments.push(shopDetail.currency);
            commandArguments.push(shopDetail.description);
            commandArguments.push(shopDetail.categories);
            commandArguments.push(shopDetail.languages);
            commandArguments.push(shopDetail.operatorName);
            commandArguments.push(shopDetail.operatorEmail);

            if(typeof shopDetail.id !== "undefined") {
                commandArguments.unshift(shopDetail.id);
                return dispatch({
                    type: getType('SHOP_DETAIL_UPDATE'),
                    payload: MessageBusFactory.command('UpdateShop', commandArguments)
                });
            }

            return dispatch({
                type: getType('SHOP_DETAIL_ADD'),
                payload: MessageBusFactory.command('AddShop', commandArguments)
            });
        }
    }

    function shopRemove(id) {
        return {
            type: getType('SHOP_REMOVE'),
            payload: MessageBusFactory.command('RemoveShop', [id])
        }
    }

    function shopDetailReset() {
        return {
            type: getType('SHOP_DETAIL_RESET')
        }
    }

    function fetchShopCustomProperties(shopId) {
        return {
            type: getType('FETCH_CUSTOM_PROPERTIES'),
            payload: MessageBusFactory.query('FindShopCustomProperties', [shopId])
        }
    }

    function addShopCustomProperty(shopId, key, value, translatable) {
        return {
            type: getType('ADD_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('AddShopCustomProperty', [shopId, key, value, translatable])
        }
    }

    function removeShopCustomProperty(shopId, id) {
        return {
            type: getType('REMOVE_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('RemoveShopCustomProperty', [shopId, id])
        }
    }

    return factory;
};

ShopActions.$inject = ShopActionsInject;

export default ['ShopActions', ShopActions];
