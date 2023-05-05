import update from 'immutability-helper';

const BatchManipulationReducerInject = ['AptoReducersProvider'];
const BatchManipulationReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_BATCH_MANIPULATION_';
    const initialState = {
        inProgress: false,
        batchMessage: '',
        processMessage: '',
        conflictMessage: ''
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.batchManipulation = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('SET_NEW_PRICES_FULFILLED'):
                let message = 'Stapelverarbeitung abgeschlossen!';
                if (action.payload.data.message.error) {
                    message = 'Fehler bei Stapelverarbeitung aufgetreten!';
                }
                newState = update(state, {
                    batchMessage: {
                        $set: message
                    }
                });
                return newState;
            case getType('FETCH_CURRENT_PRICES_ERROR'):
                newState = update(state, {
                    batchMessage: {
                        $set: action.payload
                    }
                })
                return newState;
            case getType('FETCH_CURRENT_PRICES_PENDING'):
                let bMessage = 'Stapelverarbeitung wird durchgeführt';
                newState = update(state, {
                    batchMessage: {
                        $set: bMessage
                    }
                })
                return newState;
            case getType('SET_IN_PROGRESS'):
                newState = update(state, {
                    inProgress: {
                        $set: action.payload
                    }
                })
                return newState;
            case getType('SET_BATCH_MESSAGE'):
                newState = update(state, {
                    batchMessage: {
                        $set: action.payload
                    }
                })
                return newState;
            case getType('SET_PROCESS_MESSAGE'):
                newState = update(state, {
                    processMessage: {
                        $set: action.payload
                    }
                })
                return newState;
            case getType('SET_CONFLICT_MESSAGE'):
                newState = update(state, {
                    conflictMessage: {
                        $set: action.payload
                    }
                })
                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('batchManipulation', this.batchManipulation);

    this.$get = function() {};
};

BatchManipulationReducer.$inject = BatchManipulationReducerInject;

export default ['BatchManipulationReducer', BatchManipulationReducer];