const CustomerGroupActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const CustomerGroupActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_CUSTOMER_GROUP_';
    const factory = {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        customerGroupsFetch: customerGroupsFetch,
        customerGroupsSynchronize: customerGroupsSynchronize,
        fetchDetails: fetchDetails,
        resetDetails: resetDetails,
        saveDetails: saveDetails,
        removeCustomerGroup: removeCustomerGroup
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function customerGroupsFetch(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('CUSTOMER_GROUPS_FETCH'),
                payload: MessageBusFactory.query('FindCustomerGroups', [searchString])
            });
        };
    }

    function customerGroupsSynchronize() {
        return {
            type: getType('CUSTOMER_GROUPS_SYNCHRONIZE'),
            payload: MessageBusFactory.command('SynchronizeCustomerGroups', [])
        };
    }

    function fetchDetails(id) {
        return {
            type: getType('FETCH_DETAILS'),
            payload: MessageBusFactory.query('FindCustomerGroup', [id])
        };
    }

    function resetDetails() {
        return {
            type: getType('RESET_DETAILS')
        }
    }

    function saveDetails(details) {
        return dispatch => {
            let commandArguments = [];

            commandArguments.push(details.name);
            commandArguments.push(details.inputGross ? details.inputGross : false);
            commandArguments.push(details.showGross ? details.showGross : false);
            commandArguments.push(details.shopId);
            commandArguments.push(details.externalId);
            commandArguments.push(details.fallback ? details.fallback : false);

            if(typeof details.id !== "undefined") {

                commandArguments.unshift(details.id);

                return dispatch({
                    type: getType('UPDATE_CUSTOMER_GROUP'),
                    payload: MessageBusFactory.command('UpdateCustomerGroup', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_CUSTOMER_GROUP'),
                payload: MessageBusFactory.command('AddCustomerGroup', commandArguments)
            });
        }
    }

    function removeCustomerGroup(id) {
        return {
            type: getType('REMOVE_CUSTOMER_GROUP'),
            payload: MessageBusFactory.command('RemoveCustomerGroup', [id])
        };
    }

    return factory;
};

CustomerGroupActions.$inject = CustomerGroupActionsInject;

export default ['CustomerGroupActions', CustomerGroupActions];