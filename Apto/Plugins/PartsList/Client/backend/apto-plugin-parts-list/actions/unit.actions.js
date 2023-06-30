const ActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const Actions = function ($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_PLUGIN_PARTS_LIST_UNIT_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchList(pageNumber, recordsPerPage, searchString) {
        return dispatch => {
            if (typeof searchString === 'undefined') {
                searchString = '';
            }

            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            dispatch(PageHeaderActions.setPageNumber(TYPE_NS)(pageNumber));
            dispatch(PageHeaderActions.setRecordsPerPage(TYPE_NS)(recordsPerPage));
            dispatch({type: getType('FETCH_LIST')});

            return MessageBusFactory.query('AptoPartsListFindUnits', [pageNumber, recordsPerPage, searchString]).then(
                (response) => {
                    if (response.data.result.numberOfPages > 0 && response.data.result.data.length < 1) {
                        dispatch(fetchList(response.data.result.numberOfPages, recordsPerPage, searchString));
                    }
                    else {
                        dispatch(fetchListFulfilled(response));
                        dispatch(PageHeaderActions.setNumberOfPages(TYPE_NS)(response.data.result.numberOfPages));
                        dispatch(PageHeaderActions.setNumberOfRecords(TYPE_NS)(response.data.result.numberOfRecords));
                    }
                },
                (error) => {
                    dispatch(fetchListError(error));
                    throw error;
                }
            )
        }
    }

    function fetchListFulfilled(payload) {
        return {
            type: getType('FETCH_LIST_FULFILLED'),
            payload: payload
        }
    }

    function fetchListError(payload) {
        return {
            type: getType('FETCH_LIST_ERROR'),
            payload: payload
        }
    }

    function fetchDetails(id) {
        return {
            type: getType('FETCH_DETAILS'),
            payload: MessageBusFactory.query('AptoPartsListFindUnit', [id])
        }
    }

    function resetDetails() {
        return {
            type: getType('RESET_DETAILS')
        }
    }

    function saveDetails(details) {
        return dispatch => {
            let commandArguments = [];

            if(!details.unit) {
                details.unit = '';
            }

            commandArguments.push(details.unit);

            if (typeof details.id !== 'undefined') {
                commandArguments.unshift(details.id);
                return dispatch({
                    type: getType('UPDATE_DETAILS'),
                    payload: MessageBusFactory.command('AptoPartsListUpdateUnit', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_DETAILS'),
                payload: MessageBusFactory.command('AptoPartsListAddUnit', commandArguments)
            });
        }
    }

    function removeDetails(id) {
        return {
            type: getType('REMOVE_DETAILS'),
            payload: MessageBusFactory.command('AptoPartsListRemoveUnit', [id])
        };
    }

    return {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        fetchList: fetchList,
        fetchDetails: fetchDetails,
        saveDetails: saveDetails,
        resetDetails: resetDetails,
        removeDetails: removeDetails
    };
};

Actions.$inject = ActionsInject;

export default ['AptoPartsListUnitActions', Actions];