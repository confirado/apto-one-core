const CustomerActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const CustomerActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_CUSTOMER_';
    const factory = {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        customersFetch: customersFetch
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function customersFetch(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('CUSTOMERS_FETCH'),
                payload: MessageBusFactory.query('FindCustomers', [searchString])
            });
        };
    }

    return factory;
};

CustomerActions.$inject = CustomerActionsInject;

export default ['CustomerActions', CustomerActions];