import update from 'immutability-helper';

const PoolReducerInject = ['AptoReducersProvider'];
const PoolReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_POOL_';
    const initialState = {
        pageHeaderConfig: getPageHeaderConfig(),
        dataListConfig: getDataListConfig(),
        pool: {},
        poolItems: [],
        materials: [],
        priceGroups: [],
        pools: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('SET_PAGE_NUMBER'):
                state = setPageNumber(state, action.payload);
                break;
            case getType('SET_SEARCH_STRING'):
                state = setSearchString(state, action.payload);
                break;
            case getType('FETCH_POOLS_BY_PAGE_FULFILLED'):
                state = update(state, {
                    pools: {
                        $set: action.payload.data.result.data
                    }
                });
                state = setNumberOfPages(state, action.payload.data.result.numberOfPages);
                state = setNumberOfRecords(state, action.payload.data.result.numberOfRecords);
                break;
            case getType('FETCH_POOL_FULFILLED'):
                state = update(state, {
                    pool: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('FETCH_POOL_ITEMS_FULFILLED'):
                state = update(state, {
                    poolItems: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('FETCH_MATERIALS_FULFILLED'):
                state = update(state, {
                    materials: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('FETCH_PRICE_GROUPS_FULFILLED'):
                state = update(state, {
                    priceGroups: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('RESET_POOL'):
                state = update(state, {
                    pool: {
                        $set: angular.copy(initialState.pool)
                    }
                });
                break;
        }

        return state;
    };

    function setPageNumber(state, pageNumber) {
        let newState = update(state, {
            pageHeaderConfig: {
                pagination: {
                    pageNumber: {
                        $set: pageNumber
                    }
                }
            }
        });

        if (pageNumber < 1 ) {
            newState.pageHeaderConfig.pagination.pageNumber = 1;
        }

        if (pageNumber > state.pageHeaderConfig.pagination.numberOfPages ) {
            newState.pageHeaderConfig.pagination.pageNumber = newState.pageHeaderConfig.pagination.numberOfPages;
        }

        return newState;
    }

    function setSearchString(state, searchString) {
        return update(state, {
            pageHeaderConfig: {
                search: {
                    searchString: {
                        $set: searchString
                    }
                }
            }
        });
    }

    function setNumberOfPages(state, numberOfPages) {
        let newState = update(state, {
            pageHeaderConfig: {
                pagination: {
                    numberOfPages: {
                        $set: numberOfPages
                    }
                }
            }
        });

        if (numberOfPages < 1) {
            newState.pageHeaderConfig.pagination.numberOfPages = 1;
        }

        if (state.pageHeaderConfig.pagination.pageNumber >  numberOfPages) {
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
    }

    function setNumberOfRecords(state, numberOfRecords) {
        state = update(state, {
            pageHeaderConfig: {
                pagination: {
                    numberOfRecords: {
                        $set: numberOfRecords
                    }
                }
            }
        });
        return state;
    }

    function getPageHeaderConfig() {
        return {
            title: 'Pools',
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
                    commands: ['AddMaterialPickerPool'],
                    queries: []
                }
            },
            toggleSideBarRight: {
                show: true
            }
        }
    }

    function getDataListConfig() {
        return {
            listFields: {
                id: {
                    name: 'id',
                    label: 'Id'
                },
                name: {
                    name: 'name',
                    label: 'Name',
                    translatedValue: true
                },
                created: {
                    name: 'created',
                    label: 'Erstellt'
                }
            },
            listOptions: {
                table: ['id', 'name', 'created'],
                listTemplate: 'components/data-list/data-table-list.html'
            },
            itemActions: {
                edit: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['UpdateMaterialPickerPool'], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveMaterialPickerPool'], queries: []}
                },
                copy: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['CopyMaterialPickerPool'], queries: []}
                }
            }
        }
    }

    AptoReducersProvider.addReducer('pluginMaterialPickerPool', this.reducer);

    this.$get = function() {};
};

PoolReducer.$inject = PoolReducerInject;

export default ['MaterialPickerPoolReducer', PoolReducer];