import update from 'immutability-helper';

const PriceGroupReducerInject = ['AptoReducersProvider'];
const PriceGroupReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_PRICE_GROUP_';
    const initialState = {
        pageHeaderConfig: getPageHeaderConfig(),
        dataListConfig: getDataListConfig(),
        priceGroup: {
            priceMatrix: {
                id: null,
                row: null,
                column: null
            }
        },
        priceGroups: [],
        priceMatrices: []
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
            case getType('FETCH_PRICE_GROUPS_BY_PAGE_FULFILLED'):
                state = update(state, {
                    priceGroups: {
                        $set: action.payload.data.result.data
                    }
                });
                state = setNumberOfPages(state, action.payload.data.result.numberOfPages);
                state = setNumberOfRecords(state, action.payload.data.result.numberOfRecords);
                break;
            case getType('FETCH_PRICE_MATRICES_FULFILLED'):
                state = update(state, {
                    priceMatrices: {
                        $set: action.payload.data.result.data
                    }
                });
                return state;
            case getType('FETCH_PRICE_GROUP_FULFILLED'):
                state = update(state, {
                    priceGroup: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('RESET_PRICE_GROUP'):
                state = update(state, {
                    priceGroup: {
                        $set: angular.copy(initialState.priceGroup)
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
            title: 'Preisgruppen',
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
                    commands: [],
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
                internalName: {
                    name: 'internalName',
                    label: 'Interner Name',
                    translatedValue: true
                },
                name: {
                    name: 'name',
                    label: 'Name',
                    translatedValue: true
                },
                additionalCharge: {
                    name: 'additionalCharge',
                    label: 'Aufpreis'
                },
                created: {
                    name: 'created',
                    label: 'Erstellt'
                }
            },
            listOptions: {
                table: ['id', 'internalName', 'name', 'additionalCharge', 'created'],
                listTemplate: 'components/data-list/data-table-list.html'
            },
            itemActions: {
                edit: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: [], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: [], queries: []}
                }
            }
        }
    }

    AptoReducersProvider.addReducer('pluginMaterialPickerPriceGroup', this.reducer);

    this.$get = function() {};
};

PriceGroupReducer.$inject = PriceGroupReducerInject;

export default ['MaterialPickerPriceGroupReducer', PriceGroupReducer];