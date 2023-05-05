const PriceMatrixActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const PriceMatrixActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_PRICE_MATRIX_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchPriceMatrices(pageNumber, recordsPerPage, searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }

            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            dispatch(PageHeaderActions.setPageNumber(TYPE_NS)(pageNumber));
            dispatch(PageHeaderActions.setRecordsPerPage(TYPE_NS)(recordsPerPage));

            return dispatch({
                type: getType('FETCH_PRICE_MATRICES'),
                payload: MessageBusFactory.query('FindPriceMatricesByPage', [pageNumber, recordsPerPage, searchString])
            });
        };
    }

    function fetchPriceMatrixDetail(id) {
        return {
            type: getType('FETCH_PRICE_MATRIX_DETAIL'),
            payload: MessageBusFactory.query('FindPriceMatrix', [id])
        }
    }

    function savePriceMatrix(priceMatrixDetail) {
        return dispatch => {
            let commandArguments = [];

            commandArguments.push(priceMatrixDetail.name);

            if(typeof priceMatrixDetail.id !== "undefined") {
                commandArguments.unshift(priceMatrixDetail.id);
                return dispatch({
                    type: getType('UPDATE_PRICE_MATRIX'),
                    payload: MessageBusFactory.command('UpdatePriceMatrix', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_PRICE_MATRIX'),
                payload: MessageBusFactory.command('AddPriceMatrix', commandArguments)
            });
        }
    }

    function removePriceMatrix(id) {
        return {
            type: getType('REMOVE_PRICE_MATRIX'),
            payload: MessageBusFactory.command('RemovePriceMatrix', [id])
        }
    }

    function priceMatrixDetailReset() {
        return {
            type: getType('PRICE_MATRIX_DETAIL_RESET')
        }
    }

    function fetchPriceMatrixElements(id) {
        return {
            type: getType('FETCH_PRICE_MATRIX_ELEMENTS'),
            payload: MessageBusFactory.query('FindPriceMatrixElements', [id])
        }
    }

    function fetchPriceMatrixElementPrices(id, elementId) {
        return {
            type: getType('FETCH_PRICE_MATRIX_ELEMENT_PRICES'),
            payload: MessageBusFactory.query('FindPriceMatrixElementPrices', [id, elementId])
        }
    }

    function fetchPriceMatrixElementCustomProperties(id, elementId) {
        return {
            type: getType('FETCH_PRICE_MATRIX_ELEMENT_CUSTOM_PROPERTIES'),
            payload: MessageBusFactory.query('FindPriceMatrixElementCustomProperties', [id, elementId])
        }
    }

    function addPriceMatrixElement(id, columnValue, rowValue) {
        return {
            type: getType('ADD_PRICE_MATRIX_ELEMENT'),
            payload: MessageBusFactory.command('AddPriceMatrixElement', [id, columnValue, rowValue])
        }
    }

    function removePriceMatrixElement(id, elementId) {
        return {
            type: getType('REMOVE_PRICE_MATRIX_ELEMENT'),
            payload: MessageBusFactory.command('RemovePriceMatrixElement', [id, elementId])
        }
    }

    function addPriceMatrixElementPrice(id, elementId, amount, currency, customerGroupId) {
        return {
            type: getType('ADD_PRICE_MATRIX_ELEMENT_PRICE'),
            payload: MessageBusFactory.command('AddPriceMatrixElementPrice', [id, elementId, amount, currency, customerGroupId])
        }
    }

    function removePriceMatrixElementPrice(id, elementId, priceId) {
        return {
            type: getType('REMOVE_PRICE_MATRIX_ELEMENT_PRICE'),
            payload: MessageBusFactory.command('RemovePriceMatrixElementPrice', [id, elementId, priceId])
        }
    }

    function addPriceMatrixElementCustomProperty(id, elementId, key, value, translatable) {
        return {
            type: getType('ADD_PRICE_MATRIX_ELEMENT_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('AddPriceMatrixElementCustomProperty', [id, elementId, key, value, translatable])
        }
    }

    function removePriceMatrixElementCustomProperty(id, elementId, key) {
        return {
            type: getType('REMOVE_PRICE_MATRIX_ELEMENT_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('RemovePriceMatrixElementCustomProperty', [id, elementId, key])
        }
    }

    function resetImportReport() {
        return {
            type: getType('RESET_IMPORT_REPORT')
        }
    }

    function importFileUpload(uploadCommand, uploadArguments, files, id) {
        return dispatch => {
            dispatch({
                type: getType('UPLOAD_FILES_PENDING')
            });
            dispatch({
                type: getType('UPLOAD_FILES_PROGRESS_START'),
                payload: {id: id, total: 0, loaded: 0}
            });
            return MessageBusFactory.uploadCommand(uploadCommand, uploadArguments, files, {}).then(
                (response) => {
                    dispatch({
                        type: getType('UPLOAD_FILES_FULFILLED'),
                        payload: response
                    });
                    dispatch({
                        type: getType('UPLOAD_FILES_PROGRESS_END'),
                        payload: {id: id, total: 0, loaded: 0, response: response}
                    });
                },
                (error) => {
                    dispatch({
                        type: getType('UPLOAD_FILES_REJECTED'),
                        payload: error
                    });
                    dispatch({
                        type: getType('UPLOAD_FILES_PROGRESS_END'),
                        payload: {id: id, total: 0, loaded: 0, response: error}
                    });
                },
                (evt) => {
                    dispatch({
                        type: getType('UPLOAD_FILES_PROGRESS_UPDATE'),
                        payload: {id: id, total: evt.total, loaded: evt.loaded}
                    });
                }
            );
        };
    }

    function fetchCsvExportString(priceMatrixId, customerGroupId, currencyCode, csvType) {
        return {
            type: getType('FETCH_CSV_EXPORT_STRING'),
            payload: MessageBusFactory.query('GetPriceMatrixCsvString', [priceMatrixId, customerGroupId, currencyCode, csvType])
        }
    }

    function resetCsvExportString() {
        return {
            type: getType('RESET_CSV_EXPORT_STRING')
        }
    }

    return {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setPageNumber: PageHeaderActions.setPageNumber(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        fetchPriceMatrices: fetchPriceMatrices,
        fetchPriceMatrixDetail: fetchPriceMatrixDetail,
        savePriceMatrix: savePriceMatrix,
        removePriceMatrix: removePriceMatrix,
        priceMatrixDetailReset: priceMatrixDetailReset,
        fetchPriceMatrixElements: fetchPriceMatrixElements,
        fetchPriceMatrixElementPrices: fetchPriceMatrixElementPrices,
        fetchPriceMatrixElementCustomProperties: fetchPriceMatrixElementCustomProperties,
        addPriceMatrixElement: addPriceMatrixElement,
        removePriceMatrixElement: removePriceMatrixElement,
        addPriceMatrixElementPrice: addPriceMatrixElementPrice,
        removePriceMatrixElementPrice: removePriceMatrixElementPrice,
        addPriceMatrixElementCustomProperty: addPriceMatrixElementCustomProperty,
        removePriceMatrixElementCustomProperty: removePriceMatrixElementCustomProperty,
        resetImportReport: resetImportReport,
        importFileUpload: importFileUpload,
        resetCsvExportString: resetCsvExportString,
        fetchCsvExportString: fetchCsvExportString
    };
};

PriceMatrixActions.$inject = PriceMatrixActionsInject;

export default ['PriceMatrixActions', PriceMatrixActions];