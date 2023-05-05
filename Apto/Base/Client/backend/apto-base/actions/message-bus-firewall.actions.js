const MessageBusFirewallActionsInject = ['$mdSidenav', '$ngRedux', 'MessageBusFactory'];
const MessageBusFirewallActions = function($mdSidenav, $ngRedux, MessageBusFactory) {
    const TYPE_NS = 'APTO_MESSAGE_BUS_FIREWALL_';
    const factory = {
        messageBusMessagesFetch: messageBusMessagesFetch,
        aclEntriesFetch: aclEntriesFetch,
        fetchAclEntriesByAclClass: fetchAclEntriesByAclClass,
        aclPermissionAdd: aclPermissionAdd,
        aclPermissionRemove: aclPermissionRemove
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function messageBusMessagesFetch(refreshUserRoles) {
        MessageBusFactory.query('FindMessageBusMessages', []).then(function (response) {
            $ngRedux.dispatch(messageBusMessagesReceived(response.data.result, refreshUserRoles));
        }, function (response) {
            console.log(response);
        });

        return {
            type: getType('MESSAGE_BUS_MESSAGES_FETCH')
        }
    }

    function messageBusMessagesReceived(payload, refreshUserRoles) {
        // Redux Thunk will inject dispatch here:
        return (dispatch) => {
            // Reducers may handle this to set a flag like isFetching
            dispatch({
                type: getType('MESSAGE_BUS_MESSAGES_RECEIVED'),
                payload: payload
            });

            if (!refreshUserRoles) {
                return;
            }

            // @todo make a query that gives all results(commands and queries) in one query
            if (payload.commands) {
                for (let command in payload.commands) {
                    if (!payload.commands.hasOwnProperty(command)) {
                        return;
                    }

                    dispatch(fetchAclEntriesByAclClass(payload.commands[command]));
                }
            }

            if (payload.queries) {
                for (let query in payload.queries) {
                    if (!payload.queries.hasOwnProperty(query)) {
                        return;
                    }

                    dispatch(fetchAclEntriesByAclClass(payload.queries[query]));
                }
            }
        }
    }

    function fetchAclEntriesByAclClass(aclClass) {
        return {
            type: getType('FETCH_ACL_ENTRIES_BY_ACL_CLASS'),
            payload: MessageBusFactory.query('FindAclEntriesByAclClass', [aclClass])
        }
    }

    function aclEntriesFetch(className) {
        // Redux Thunk will inject dispatch here:
        return (dispatch) => {
            // Reducers may handle this to set a flag like isFetching
            dispatch({ type: getType('ACL_ENTRIES_FETCH') });

            // Perform the actual API call
            return MessageBusFactory.query('FindAclEntriesByAclClass', [className]).then(
                (response) => {
                    // Reducers may handle this to show the data and reset isFetching
                    dispatch(aclEntriesReceived(response.data.result.data));
                },
                (error) => {
                    // Reducers may handle this to reset isFetching
                    dispatch(aclEntriesFetchError(error));
                    // Rethrow so returned Promise is rejected
                    throw error;
                }
            )
        }
    }

    function aclEntriesReceived(payload) {
        return {
            type: getType('ACL_ENTRIES_RECEIVED'),
            payload: payload
        }
    }

    function aclEntriesFetchError(payload) {
        return {
            type: getType('ACL_ENTRIES_FETCH_ERROR'),
            payload: payload
        }
    }

    function aclPermissionAdd(shopId, roleId, entityClass, entityId, entityField, permissions) {
        // Redux Thunk will inject dispatch here:
        return (dispatch) => {
            // Reducers may handle this to set a flag like isFetching
            dispatch({ type: getType('ACL_PERMISSION_ADD') });

            // Perform the actual API call
            return MessageBusFactory.command('AddAclPermission', [shopId, roleId, entityClass, entityId, entityField, permissions]).then(
                (response) => {
                    // Reducers may handle this to show the data and reset isFetching
                    dispatch(aclPermissionAdded(response));
                },
                (error) => {
                    // Reducers may handle this to reset isFetching
                    dispatch(aclPermissionAddError(error));
                    // Rethrow so returned Promise is rejected
                    throw error;
                }
            )
        }
    }

    function aclPermissionAdded(payload) {
        return {
            type: getType('ACL_PERMISSION_ADDED'),
            payload: payload
        }
    }

    function aclPermissionAddError(payload) {
        return {
            type: getType('ACL_PERMISSION_ADD_ERROR'),
            payload: payload
        }
    }

    function aclPermissionRemove(shopId, roleId, entityClass, entityId, entityField, permissions) {
        // Redux Thunk will inject dispatch here:
        return (dispatch) => {
            // Reducers may handle this to set a flag like isFetching
            dispatch({ type: getType('ACL_PERMISSION_REMOVE') });

            // Perform the actual API call
            return MessageBusFactory.command('RemoveAclPermission', [shopId, roleId, entityClass, entityId, entityField, permissions]).then(
                (response) => {
                    // Reducers may handle this to show the data and reset isFetching
                    dispatch(aclPermissionRemoved(response));
                },
                (error) => {
                    // Reducers may handle this to reset isFetching
                    dispatch(aclPermissionRemoveError(error));
                    // Rethrow so returned Promise is rejected
                    throw error;
                }
            )
        }
    }

    function aclPermissionRemoved(payload) {
        return {
            type: getType('ACL_PERMISSION_REMOVED'),
            payload: payload
        }
    }

    function aclPermissionRemoveError(payload) {
        return {
            type: getType('ACL_PERMISSION_REMOVE_ERROR'),
            payload: payload
        }
    }

    return factory;
};

MessageBusFirewallActions.$inject = MessageBusFirewallActionsInject;

export default ['MessageBusFirewallActions', MessageBusFirewallActions];