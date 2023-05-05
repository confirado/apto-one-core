const ActionsInject = ['MessageBusFactory'];
const Actions = function (MessageBusFactory) {
    const TYPE_NS = 'APTO_PRODUCT_LIST_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchProductList(filter) {
        if (!filter) {
            filter = {};
        }

        return {
            type: getType('FETCH_PRODUCT_LIST'),
            payload: MessageBusFactory.query('FindProductsByFilter', [filter])
        }
    }

    function fetchCategories() {
        return {
            type: getType('FETCH_CATEGORIES'),
            payload: MessageBusFactory.query('FindCategories', [])
        }
    }

    function fetchCategoryTree(searchString) {
        return {
            type: getType('FETCH_CATEGORY_TREE'),
            payload: MessageBusFactory.query('FindCategoryTree', [searchString])
        }
    }

    function fetchFilterProperties() {
        return {
            type: getType('FETCH_FILTER_PROPERTIES'),
            payload: MessageBusFactory.query('FindFilterProperties')
        }
    }

    function setProductPropertyFilter(filter, properties) {
        return {
            type: getType('SET_PRODUCT_PROPERTY_FILTER'),
            payload: {
                filter: filter,
                properties: properties
            }
        }
    }

    function resetPropertyFilter() {
        return {
            type: getType('RESET_PRODUCT_PROPERTY_FILTER')
        }
    }

    function updateCategoryFilter(category) {
        return {
            type: getType('UPDATE_CATEGORY_FILTER'),
            payload: category
        }
    }

    return {
        fetchProductList: fetchProductList,
        fetchCategories: fetchCategories,
        fetchCategoryTree: fetchCategoryTree,
        fetchFilterProperties: fetchFilterProperties,
        setProductPropertyFilter: setProductPropertyFilter,
        resetPropertyFilter: resetPropertyFilter,
        updateCategoryFilter: updateCategoryFilter
    };
};

Actions.$inject = ActionsInject;

export default ['ProductListActions', Actions];