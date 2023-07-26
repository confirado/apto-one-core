import update from 'immutability-helper';

const ReducerInject = ['AptoReducersProvider'];
const Reducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_PLUGIN_PARTS_LIST_UNIT_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Einheiten',
            pagination: {
                show: true,
                pageNumber: 1,
                recordsPerPage: 20,
                numberOfRecords: 0,
                numberOfPages: 1
            },
            search: {
                show: true,
                searchString: ''
            },
            add: {
                show: true,
                aclMessagesRequired: {
                    commands: ['AptoPartsListAddUnit'],
                    queries: []
                }
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
        dataListConfig: {
            listFields: {
                id: {
                    name: 'id',
                    label: 'Id'
                },
                unit: {
                    name: 'unit',
                    label: 'Einheit'
                }
            },
            listOptions: {
                card: {
                    headline: ['id'],
                    subHeadline: ['unit'],
                    cardColumns: 3
                },
                table: ['id', 'unit'],
                listTemplate: 'components/data-list/data-table-list.html'
            },
            itemActions: {
                select: {
                    show: false,
                    field: 'id',
                    selected: {}
                },
                edit: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['AptoPartsListUpdateUnit'], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['AptoPartsListRemoveUnit'], queries: []}
                }
            }
        },
        list: [],
        details: {}
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        let newState;
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('SET_SEARCH_STRING'):
                newState = update(state, {
                    pageHeaderConfig: {
                        search: {
                            searchString: {
                                $set: action.payload
                            }
                        }
                    }
                });
                return newState;

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
                if (action.payload > state.pageHeaderConfig.pagination.numberOfPages ) {
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
                if (state.pageHeaderConfig.pagination.pageNumber >  action.payload) {
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

            case getType('SET_LIST_TEMPLATE'):
                newState = update(state, {
                    dataListConfig: {
                        listOptions: {
                            listTemplate: {
                                $set: action.payload
                            }
                        }
                    }
                });
                return newState;

            case getType('SET_SELECTED'):
                newState = update(state, {
                    dataListConfig: {
                        itemActions: {
                            select: {
                                selected: {
                                    $set: action.payload
                                }
                            }
                        }
                    }
                });
                return newState;

            case getType('FETCH_LIST_FULFILLED'):
                newState = update(state, {
                    list: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;

            case getType('FETCH_DETAILS_FULFILLED'):
                newState = update(state, {
                    details: {
                        $set: action.payload.data.result
                    }
                });
                return newState;

            case getType('RESET_DETAILS'):
                newState = update(state, {
                    details: {
                        $set: angular.copy(initialState.details)
                    }
                });
                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('aptoPartsListUnit', this.reducer);

    this.$get = function() {};
};

Reducer.$inject = ReducerInject;

export default ['AptoPartsListUnitReducer', Reducer];