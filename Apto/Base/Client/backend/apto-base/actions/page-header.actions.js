//@todo handling of setting variables for pagination is very confuse, need to find a better solution
const PageHeaderActionsInject = [];
const PageHeaderActions = function() {
    function setNumberOfPages(typeNs) {
        return function (payload) {
            return {
                type: typeNs + 'SET_NUMBER_OF_PAGES',
                    payload: payload
            }
        }
    }

    function setRecordsPerPage(typeNs) {
        return function (payload) {
            return {
                type: typeNs + 'SET_RECORDS_PER_RECORDS',
                payload: payload
            }
        }
    }

    function setNumberOfRecords(typeNs) {
        return function (payload) {
            return {
                type: typeNs + 'SET_NUMBER_OF_RECORDS',
                payload: payload
            }
        }
    }

    function setPageNumber(typeNs) {
        return function (payload) {
            return {
                type: typeNs + 'SET_PAGE_NUMBER',
                payload: payload
            }
        }
    }

    function setListTemplate(typeNs) {
        return function (payload) {
            return {
                type: typeNs + 'SET_LIST_TEMPLATE',
                payload: payload
            }
        }
    }

    function setSearchString(typeNs) {
        return function (payload) {
            return {
                type: typeNs + 'SET_SEARCH_STRING',
                payload: payload
            }
        }
    }

    return {
        setPageNumber: setPageNumber,
        setRecordsPerPage: setRecordsPerPage,
        setNumberOfPages: setNumberOfPages,
        setNumberOfRecords: setNumberOfRecords,
        setSearchString: setSearchString,
        setListTemplate: setListTemplate
    };
};

PageHeaderActions.$inject = PageHeaderActionsInject;

export default ['PageHeaderActions', PageHeaderActions];