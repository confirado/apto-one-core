import update from 'immutability-helper';

const PropertyReducerInject = ['AptoReducersProvider'];
const PropertyReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_PROPERTY_';
    const initialState = {
        pageHeaderConfig: getPageHeaderConfig(),
        dataListConfig: getDataListConfig(),
        group: {},
        groups: [],
        properties: [],
        property: {},
        propertyCustomProperties: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            // group listing
            case getType('SET_PAGE_NUMBER'):
                state = setPageNumber(state, action.payload);
                break;
            case getType('SET_SEARCH_STRING'):
                state = setSearchString(state, action.payload);
                break;
            case getType('FETCH_GROUPS_BY_PAGE_FULFILLED'):
                state = update(state, {
                    groups: {
                        $set: action.payload.data.result.data
                    }
                });
                state = setNumberOfPages(state, action.payload.data.result.numberOfPages);
                state = setNumberOfRecords(state, action.payload.data.result.numberOfRecords);
                break;
            // group details
            case getType('FETCH_GROUP_FULFILLED'):
                state = update(state, {
                    group: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('FETCH_GROUP_PROPERTIES_FULFILLED'):
                state = update(state, {
                    properties: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('RESET_GROUP'):
                state = update(state, {
                    group: {
                        $set: angular.copy(initialState.group)
                    },
                    properties: {
                        $set: angular.copy(initialState.properties)
                    }
                });
                break;
            // property details
            case getType('FETCH_PROPERTY_FULFILLED'):
                state = update(state, {
                    property: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('FETCH_PROPERTY_CUSTOM_PROPERTIES_FULFILLED'):
                state = update(state, {
                    propertyCustomProperties: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('RESET_PROPERTY'):
                state = update(state, {
                    property: {
                        $set: angular.copy(initialState.property)
                    },
                    propertyCustomProperties: {
                        $set: angular.copy(initialState.propertyCustomProperties)
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
            title: 'Eigenschaften',
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

    AptoReducersProvider.addReducer('pluginMaterialPickerProperty', this.reducer);

    this.$get = function() {};
};

PropertyReducer.$inject = PropertyReducerInject;

export default ['MaterialPickerPropertyReducer', PropertyReducer];