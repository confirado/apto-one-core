const DomainEventLogActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions'];
const DomainEventLogActions = function($ngRedux, MessageBusFactory, PageHeaderActions) {
    const TYPE_NS = 'APTO_DOMAIN_EVENT_LOG_';
    const factory = {
        domainEventsFetch: domainEventsFetch,
        groupedTypeNamesFetch: groupedTypeNamesFetch,
        groupedUsersFetch: groupedUsersFetch,
        setQueryFilter: setQueryFilter,
        resetQueryFilter: resetQueryFilter
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function setQueryFilter(payload) {
        return {
            type: getType('SET_QUERY_FILTER'),
            payload: payload
        }
    }

    function resetQueryFilter() {
        return {
            type: getType('RESET_QUERY_FILTER')
        }
    }

    // fetch domain events
    function domainEventsFetch(pageNumber, recordsPerPage, queryFilter) {
        // Redux Thunk will inject dispatch here:
        return (dispatch) => {
            // Reducers may handle this to set a flag like isFetching
            dispatch(setQueryFilter(queryFilter));
            dispatch(PageHeaderActions.setPageNumber(TYPE_NS)(pageNumber));
            dispatch(PageHeaderActions.setRecordsPerPage(TYPE_NS)(recordsPerPage));
            dispatch({ type: getType('DOMAIN_EVENTS_FETCH'), payload: {pageNumber:pageNumber, recordsPerPage:recordsPerPage, queryFilter: queryFilter}});
            const searchString = queryFilter.eventBody;
            const filter = {
                fromDate: queryFilter.fromDate,
                toDate: queryFilter.toDate,
                userIds: queryFilter.userIds,
                typeNames: queryFilter.typeNames
            };

            // Perform the actual API call
            return MessageBusFactory.query('FindDomainEventLog', [pageNumber, recordsPerPage, searchString, filter]).then(
                (response) => {
                    // Reducers may handle this to show the data and reset isFetching
                    if (response.data.result.numberOfPages > 0 && response.data.result.data.length < 1) {
                        dispatch(domainEventsFetch(response.data.result.numberOfPages, recordsPerPage, queryFilter));
                    }
                    else {
                        dispatch(domainEventsReceived(response.data.result.data));
                        dispatch(PageHeaderActions.setNumberOfPages(TYPE_NS)(response.data.result.numberOfPages));
                        dispatch(PageHeaderActions.setNumberOfRecords(TYPE_NS)(response.data.result.numberOfRecords));
                    }
                },
                (error) => {
                    // Reducers may handle this to reset isFetching
                    dispatch(domainEventsFetchError(error));
                    // Rethrow so returned Promise is rejected
                    throw error;
                }
            )
        }
    }

    function domainEventsReceived(payload) {
        return {
            type: getType('DOMAIN_EVENTS_RECEIVED'),
            payload: payload
        }
    }

    function domainEventsFetchError(payload) {
        return {
            type: getType('DOMAIN_EVENTS_FETCH_ERROR'),
            payload: payload
        }
    }

    // fetch grouped type names
    function groupedTypeNamesFetch() {
        return {
            type: getType('GROUPED_TYPE_NAMES_FETCH'),
            payload: MessageBusFactory.query('FindGroupedTypeNames', [])
        };
    }

    function groupedUsersFetch() {
        return dispatch => {
            return dispatch(groupedUserIdsFetch()).then((response) => {
                const userIds = response.action.payload.data.result;
                return dispatch(usersByUserIdsFetch(userIds)).then((response) => {
                    const users = response.action.payload.data.result;
                    const groupedUsers = mergeUsers(users, userIds);
                    return dispatch(groupedUsersReceived(groupedUsers));
                });
            });
        };
    }


    // fetch domain event grouped user ids names
    function groupedUserIdsFetch() {
        return {
            type: getType('GROUPED_USER_IDS_FETCH'),
            payload: MessageBusFactory.query('FindGroupedUserIds', [])
        };
    }

    // fetch users by user ids
    function usersByUserIdsFetch(userIds) {
        return {
            type: getType('USERS_BY_USER_IDS_FETCH'),
            payload: MessageBusFactory.query('FindUsersByUserIds', [userIds])
        };
    }

    function groupedUsersReceived(payload) {
        return {
            type: getType('GROUPED_USERS_RECEIVED'),
            payload: payload
        }
    }

    function mergeUsers(users, userIds) {
        let userIndex, userIdIndex, userIdObj = {}, mergedUsers = [], mergedUsersObj = {};

        for (userIndex = 0; userIndex < users.length; userIndex++) {
            userIdObj[users[userIndex].id] = users[userIndex].username;
        }

        for (userIdIndex = 0; userIdIndex < userIds.length; userIdIndex++) {
            let userId = userIds[userIdIndex]['userId'];
            if (!(userId in userIdObj)) {
                mergedUsersObj[userId] = {
                    id: userId,
                    username: userId
                };
                mergedUsers.push({
                    id: userId,
                    username: userId
                });
            }
        }

        for (userIndex = 0; userIndex < users.length; userIndex++) {
            let user = users[userIndex];
            if (user.id in userIdObj) {
                mergedUsersObj[user.id] = {
                    id: user.id,
                    username: user.username
                };
                mergedUsers.push({
                    id: user.id,
                    username: user.username
                });
            }
        }

        return {
            asArray: mergedUsers,
            asObject: mergedUsersObj
        };
    }

    return factory;
};

DomainEventLogActions.$inject = DomainEventLogActionsInject;

export default ['DomainEventLogActions', DomainEventLogActions];