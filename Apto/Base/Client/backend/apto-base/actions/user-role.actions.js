const UserRoleActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const UserRoleActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_USER_ROLE_';
    const factory = {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        availableChildrenFetch: availableChildrenFetch,
        userRoleDetailAssignChildren: userRoleDetailAssignChildren,
        userRoleDetailFetch: userRoleDetailFetch,
        userRoleDetailSave: userRoleDetailSave,
        userRoleDetailReset: userRoleDetailReset,
        userRoleRemove: userRoleRemove,
        userRolesFetch: userRolesFetch
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function userRolesFetch(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('USER_ROLES_FETCH'),
                payload: MessageBusFactory.query('FindUserRoles', [searchString])
            });
        };
    }

    function userRoleDetailFetch(id) {
        return {
            type: getType('USER_ROLE_DETAIL_FETCH'),
            payload: MessageBusFactory.query('FindUserRole', [id])
        }
    }

    function availableChildrenFetch() {
        return {
            type: getType('AVAILABLE_CHILDREN_FETCH'),
            payload: MessageBusFactory.query('FindUserRoles', [''])
        }
    }

    function userRoleDetailAssignChildren(children) {
        return {
            type: getType('USER_ROLE_DETAIL_ASSIGN_CHILDREN'),
            payload: children
        }
    }

    function userRoleDetailSave(userRoleDetail) {
        return dispatch => {
            let commandArguments = [];

            if(typeof userRoleDetail.children === "undefined") {
                userRoleDetail.children = [];
            }

            commandArguments.push(userRoleDetail.identifier);
            commandArguments.push(userRoleDetail.name);
            commandArguments.push(userRoleDetail.children);

            if(typeof userRoleDetail.id !== "undefined") {
                commandArguments.unshift(userRoleDetail.id);
                return dispatch({
                    type: getType('USER_ROLE_DETAIL_UPDATE'),
                    payload: MessageBusFactory.command('UpdateUserRole', commandArguments)
                });
            }

            return dispatch({
                type: getType('USER_ROLE_DETAIL_ADD'),
                payload: MessageBusFactory.command('AddUserRole', commandArguments)
            });
        }
    }

    function userRoleRemove(id) {
        return {
            type: getType('USER_ROLE_REMOVE'),
            payload: MessageBusFactory.command('RemoveUserRole', [id])
        }
    }

    function userRoleDetailReset() {
        return {
            type: getType('USER_ROLE_DETAIL_RESET')
        }
    }

    return factory;
};

UserRoleActions.$inject = UserRoleActionsInject;

export default ['UserRoleActions', UserRoleActions];