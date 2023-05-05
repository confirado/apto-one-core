import update from 'immutability-helper';

const DomainEventLogReducerInject = ['AptoReducersProvider'];
const DomainEventLogReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_DOMAIN_EVENT_LOG_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Domain Event Log',
            pagination: {
                show: true,
                pageNumber: 1,
                recordsPerPage: 20,
                numberOfRecords: 0,
                numberOfPages: 1
            },
            search: {
                show: false,
                searchString: ''
            },
            add: {
                show: false,
                aclMessagesRequired: {}
            },
            listStyle: {
                show: false
            },
            listSettings: {
                show: false
            },
            selectAll: {
                show: false
            },
            toggleSideBarRight: {
                show: true
            }
        },
        queryFilter: {
            fromDate: null,
            toDate: null,
            userIds: {},
            typeNames: {},
            eventBody: '',
        },
        groupedTypeNames: [],
        groupedUsers: {
            asArray: [],
            asObject: {}
        },
        domainEvents: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.domainEventLog = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            // data list changes
            case getType('SET_PAGE_NUMBER'):
                newState = update(state, {
                    pageHeaderConfig: {
                        pagination: {
                            pageNumber: {
                                $set: action.payload
                            }
                        }
                    }
                });

                if (action.payload < 1 ) {
                    newState.pageHeaderConfig.pagination.pageNumber = 1;
                }

                if (action.payload > newState.pageHeaderConfig.pagination.numberOfPages ) {
                    newState.pageHeaderConfig.pagination.pageNumber = newState.pageHeaderConfig.pagination.numberOfPages;
                }

                return newState;
            case getType('SET_RECORDS_PER_PAGE'):
                newState = update(state, {
                    pageHeaderConfig: {
                        pagination: {
                            recordsPerPage: {
                                $set: action.payload
                            }
                        }
                    }
                });

                return newState;
            case getType('SET_NUMBER_OF_PAGES'):
                newState = update(state, {
                    pageHeaderConfig: {
                        pagination: {
                            numberOfPages: {
                                $set: action.payload
                            }
                        }
                    }
                });

                if (action.payload < 1) {
                    newState.pageHeaderConfig.pagination.numberOfPages = 1;
                }

                if (newState.pageHeaderConfig.pagination.pageNumber >  action.payload) {
                    newState = update(newState, {
                        pageHeaderConfig: {
                            pagination: {
                                pageNumber: {
                                    $set: newState.pageHeaderConfig.pagination.numberOfPages
                                }
                            }
                        }
                    });
                }

                return newState;
            case getType('SET_NUMBER_OF_RECORDS'):
                newState = update(state, {
                    pageHeaderConfig: {
                        pagination: {
                            numberOfRecords: {
                                $set: action.payload
                            }
                        }
                    }
                });

                return newState;
            // filter changes
            case getType('SET_QUERY_FILTER'):
                newState = update(state, {
                    queryFilter: {
                        $set: action.payload
                    }
                });

                return newState;
            case getType('GROUPED_TYPE_NAMES_FETCH_FULFILLED'):
                newState = update(state, {
                    groupedTypeNames: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('GROUPED_USERS_RECEIVED'):
                newState = update(state, {
                    groupedUsers: {
                        $set: action.payload
                    }
                });

                return newState;
            // domainEvents changes
            case getType('DOMAIN_EVENTS_RECEIVED'):
                newState = update(state, {
                    domainEvents: {
                        $set: action.payload
                    }
                });

                return newState;
            case getType('RESET_QUERY_FILTER'):
                newState = update(state, {
                    queryFilter: {
                        $set: {
                            fromDate: null,
                            toDate: null,
                            userIds: {},
                            typeNames: {},
                            eventBody: '',
                        }
                    },
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('domainEventLog', this.domainEventLog);

    this.$get = function() {};
};

DomainEventLogReducer.$inject = DomainEventLogReducerInject;

export default ['DomainEventLogReducer', DomainEventLogReducer];