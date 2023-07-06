const FrontendUserActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const FrontendUserActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_FRONTEND_USER_';
    const factory = {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        frontendUserDetailFetch: frontendUserDetailFetch,
        frontendUserDetailSave: frontendUserDetailSave,
        frontendUserDetailReset: frontendUserDetailReset,
        frontendUserRemove: frontendUserRemove,
        frontendUsersFetch: frontendUsersFetch,
        availableCustomerGroupsFetch: availableCustomerGroupsFetch,
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function frontendUsersFetch(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('FRONTEND_USERS_FETCH'),
                payload: MessageBusFactory.query('FindFrontendUsers', [searchString])
            });
        };
    }

    function frontendUserDetailFetch(id) {
        return {
            type: getType('FRONTEND_USER_DETAIL_FETCH'),
            payload: MessageBusFactory.query('FindFrontendUser', [id])
        }
    }

    function frontendUserDetailSave(frontendUserDetail) {
        return dispatch => {
            let commandArguments = [];

            if(typeof frontendUserDetail.active === "undefined") {
                frontendUserDetail.active = false;
            }

            if(typeof frontendUserDetail.externalCustomerGroupId === "undefined") {
                frontendUserDetail.externalCustomerGroupId = '';
            }

            if(typeof frontendUserDetail.customerNumber === "undefined") {
                frontendUserDetail.customerNumber = '';
            }

            commandArguments.push(frontendUserDetail.active);
            commandArguments.push(frontendUserDetail.username);
            commandArguments.push(frontendUserDetail.plainPassword);
            commandArguments.push(frontendUserDetail.email);
            commandArguments.push(frontendUserDetail.externalCustomerGroupId);
            commandArguments.push(frontendUserDetail.customerNumber);

            if(typeof frontendUserDetail.id !== "undefined") {
                commandArguments.unshift(frontendUserDetail.id);
                return dispatch({
                    type: getType('FRONTEND_USER_DETAIL_UPDATE'),
                    payload: MessageBusFactory.command('UpdateFrontendUser', commandArguments)
                });
            }

            return dispatch({
                type: getType('FRONTEND_USER_DETAIL_ADD'),
                payload: MessageBusFactory.command('AddFrontendUser', commandArguments)
            });
        }
    }

    function frontendUserRemove(id) {
        return {
            type: getType('FRONTEND_USER_REMOVE'),
            payload: MessageBusFactory.command('RemoveFrontendUser', [id])
        }
    }

    function frontendUserDetailReset() {
        return {
            type: getType('FRONTEND_USER_DETAIL_RESET')
        }
    }

    function availableCustomerGroupsFetch() {
        return {
            type: getType('AVAILABLE_CUSTOMER_GROUPS_FETCH'),
            payload: MessageBusFactory.query('FindCustomerGroups', [''])
        }
    }

    return factory;
};

FrontendUserActions.$inject = FrontendUserActionsInject;

export default ['FrontendUserActions', FrontendUserActions];
