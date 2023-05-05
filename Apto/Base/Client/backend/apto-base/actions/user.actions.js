const UserActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const UserActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_USER_';
    const factory = {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        availableUserRolesFetch: availableUserRolesFetch,
        userDetailAssignUserRoles: userDetailAssignUserRoles,
        userDetailFetch: userDetailFetch,
        userDetailSave: userDetailSave,
        userDetailReset: userDetailReset,
        userRemove: userRemove,
        usersFetch: usersFetch
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function usersFetch(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('USERS_FETCH'),
                payload: MessageBusFactory.query('FindUsers', [searchString])
            });
        };
    }

    function userDetailFetch(id) {
        return {
            type: getType('USER_DETAIL_FETCH'),
            payload: MessageBusFactory.query('FindUser', [id])
        }
    }

    function availableUserRolesFetch() {
        return {
            type: getType('AVAILABLE_USER_ROLES_FETCH'),
            payload: MessageBusFactory.query('FindUserRoles', [''])
        }
    }

    function userDetailAssignUserRoles(userRoles) {
        return {
            type: getType('USER_DETAIL_ASSIGN_USER_ROLES'),
            payload: userRoles
        }
    }

    function userDetailSave(userDetail) {
        return dispatch => {
            let commandArguments = [];

            if(typeof userDetail.active === "undefined") {
                userDetail.active = false;
            }

            if(typeof userDetail.userRoles === "undefined") {
                userDetail.userRoles = [];
            }

            commandArguments.push(userDetail.active);
            commandArguments.push(userDetail.username);
            commandArguments.push(userDetail.plainPassword);
            commandArguments.push(userDetail.email);
            commandArguments.push(userDetail.userRoles);
            commandArguments.push(userDetail.rte);
            commandArguments.push(userDetail.apiKey);
            commandArguments.push(userDetail.apiOrigin);

            if(typeof userDetail.id !== "undefined") {
                commandArguments.unshift(userDetail.id);
                return dispatch({
                    type: getType('USER_DETAIL_UPDATE'),
                    payload: MessageBusFactory.command('UpdateUser', commandArguments)
                });
            }

            return dispatch({
                type: getType('USER_DETAIL_ADD'),
                payload: MessageBusFactory.command('AddUser', commandArguments)
            });
        }
    }

    function userRemove(id) {
        return {
            type: getType('USER_REMOVE'),
            payload: MessageBusFactory.command('RemoveUser', [id])
        }
    }

    function userDetailReset() {
        return {
            type: getType('USER_DETAIL_RESET')
        }
    }

    return factory;
};

UserActions.$inject = UserActionsInject;

export default ['UserActions', UserActions];