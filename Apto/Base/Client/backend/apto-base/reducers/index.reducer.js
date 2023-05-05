import update from 'immutability-helper';

const IndexReducerInject = ['AptoReducersProvider'];
const IndexReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_INDEX_';
    const initialState = {
        lockSidebarRight: false,
        messageLog: {
            messages: [],
            maxMessages: 100
        },
        messagesGranted: {
            commands: {},
            queries: {}
        },
        currentUser: {},
        languages: [],
        activeLanguage: {}
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.index = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case 'APTO_MESSAGE_BUS_CLEAR_MESSAGE_LOG':
                newState = update(state, {
                    messageLog: {
                        messages: {
                            $set: []
                        }
                    }
                });

                return newState;
            case 'APTO_MESSAGE_BUS_ADD_MESSAGE_LOG_MESSAGE':
                newState = update(state, {
                    messageLog: {
                        messages: {
                            $unshift: [action.payload]
                        }
                    }
                });

                while(newState.messageLog.messages.length > newState.messageLog.maxMessages) {
                    newState.messageLog.messages.pop();
                }

                return newState;
            case getType('TOGGLE_SIDEBAR_RIGHT'):
                newState = update(state, {
                    lockSidebarRight: {
                        $set: action.payload
                    }
                });

                return newState;
            case getType('MESSAGES_GRANTED_RECEIVED'):
                newState = update(state, {
                    messagesGranted: {
                        commands: {
                            $set: action.payload.commands
                        },
                        queries: {
                            $set: action.payload.queries
                        }
                    }
                });

                return newState;
            case getType('CURRENT_USER_RECEIVED'):
                newState = update(state, {
                    currentUser: {
                        $set: action.payload
                    }
                });

                return newState;
            case getType('LANGUAGES_FETCH_FULFILLED'):
                newState = update(state, {
                    languages: {
                        $set: action.payload.data.result.data
                    }
                });

                if (!state.activeLanguage.id) {
                    newState = update(newState, {
                        activeLanguage: {
                            $set: newState.languages[0]
                        }
                    });
                }
                return newState;
            case getType('SET_ACTIVE_LANGUAGE'):
                newState = update(state, {
                    activeLanguage: {
                        $set: action.payload
                    }
                });
                return newState;
            case getType('SET_SESSION_CURRENT_USER_RTE'):
                newState = update(state, {
                    currentUser: {
                        rte: {
                            $set: action.payload
                        }
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('index', this.index);

    this.$get = function() {};
};

IndexReducer.$inject = IndexReducerInject;

export default ['IndexReducer', IndexReducer];