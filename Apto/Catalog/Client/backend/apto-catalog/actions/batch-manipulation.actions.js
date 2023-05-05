const BatchManipulationActionsInject = ['$ngRedux', 'MessageBusFactory'];
const BatchManipulationActions = function($ngRedux, MessageBusFactory) {
    const TYPE_NS = 'APTO_BATCH_MANIPULATION_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function setBatchPrices(productIds, multiplier, filter, query = 'BatchManipulationFindPrices') {
        return (dispatch) => {
            return dispatch({
                type: getType('FETCH_CURRENT_PRICES'),
                payload: MessageBusFactory.query(query, [productIds, filter]).then(
                    (response) => {
                        return dispatch({
                            type: getType('SET_NEW_PRICES'),
                            payload: MessageBusFactory.command('BatchManipulationSetPrices', [response.data.result, multiplier])
                        })
                    },
                    (error) => {
                        return dispatch({
                            type: getType('FETCH_CURRENT_PRICES_ERROR'),
                            payload: error
                        })
                    }
                )
            })
        }
    }

    function setBatchPricesByFormula(productIds, formula, filter, query = 'BatchManipulationFindPrices') {
        return (dispatch) => {
            return dispatch({
                type: getType('FETCH_CURRENT_PRICES'),
                payload: MessageBusFactory.query(query, [productIds, filter]).then(
                    (response) => {
                        return dispatch({
                            type: getType('SET_NEW_PRICES'),
                            payload: MessageBusFactory.command('BatchManipulationSetPricesByFormula', [response.data.result, formula])
                        })
                    },
                    (error) => {
                        return dispatch({
                            type: getType('FETCH_CURRENT_PRICES_ERROR'),
                            payload: error
                        })
                    }
                )
            })
        }
    }

    function setInProgress(inProgress) {
        return {
            type: getType('SET_IN_PROGRESS'),
            payload: inProgress
        }
    }

    function setBatchMessage(message) {
        return {
            type: getType('SET_BATCH_MESSAGE'),
            payload: message
        }
    }

    function setProcessMessage(message) {
        return {
            type: getType('SET_PROCESS_MESSAGE'),
            payload: message
        }
    }

    function setConflictMessage(message) {
        return {
            type: getType('SET_CONFLICT_MESSAGE'),
            payload: message
        }
    }

    return {
        setBatchPrices: setBatchPrices,
        setBatchPricesByFormula: setBatchPricesByFormula,
        setInProgress: setInProgress,
        setBatchMessage: setBatchMessage,
        setProcessMessage: setProcessMessage,
        setConflictMessage: setConflictMessage
    };
};

BatchManipulationActions.$inject = BatchManipulationActionsInject;

export default ['BatchManipulationActions', BatchManipulationActions];
