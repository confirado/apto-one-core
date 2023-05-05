import update from 'immutability-helper';

const MaterialReducerInject = ['AptoReducersProvider'];
const MaterialReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_MATERIAL_';
    const initialState = {
        pageHeaderConfig: getPageHeaderConfig(),
        dataListConfig: getDataListConfig(),
        material: {
            active: true,
            previewImage: {}
        },
        materials: [],
        galleryImages: [],
        properties: [],
        notAssignedProperties: [],
        colorRatings: [],
        notAssignedPools: [],
        poolItems: [],
        renderImages: [],
        pools: [],
        availableCustomerGroups: [],
        prices: []
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
            case getType('FETCH_MATERIALS_BY_PAGE_FULFILLED'):
                state = update(state, {
                    materials: {
                        $set: action.payload.data.result.data
                    }
                });
                state = setNumberOfPages(state, action.payload.data.result.numberOfPages);
                state = setNumberOfRecords(state, action.payload.data.result.numberOfRecords);
                break;
            case getType('FETCH_MATERIAL_FULFILLED'):
                state = update(state, {
                    material: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('FETCH_GALLERY_IMAGES_FULFILLED'):
                state = update(state, {
                    galleryImages: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('FETCH_COLOR_RATINGS_FULFILLED'):
                state = update(state, {
                    colorRatings: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('FETCH_PROPERTIES_FULFILLED'):
                state = update(state, {
                    properties: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('FETCH_NOT_ASSIGNED_PROPERTIES_FULFILLED'):
                state = update(state, {
                    notAssignedProperties: {
                        $set: action.payload.data.result.data
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
            case getType('FETCH_NOT_ASSIGNED_POOLS_FULFILLED'):
                state = update(state, {
                    notAssignedPools: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('FETCH_RENDER_IMAGES_FULFILLED'):
                state = update(state, {
                    renderImages: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('FETCH_POOLS_FULFILLED'):
                state = update(state, {
                    pools: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('AVAILABLE_CUSTOMER_GROUPS_FETCH_FULFILLED'):
                state = update(state, {
                    availableCustomerGroups: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            case getType('FETCH_PRICES_FULFILLED'):
                state = update(state, {
                    prices: {
                        $set: action.payload.data.result
                    }
                });
                break;
            case getType('RESET_MATERIAL'):
                state = update(state, {
                    material: {
                        $set: angular.copy(initialState.material)
                    },
                    galleryImages: {
                        $set: angular.copy(initialState.galleryImages)
                    },
                    properties: {
                        $set: angular.copy(initialState.properties)
                    },
                    notAssignedProperties: {
                        $set: angular.copy(initialState.properties)
                    },
                    notAssignedPools: {
                        $set: angular.copy(initialState.properties)
                    },
                    poolItems: {
                        $set: angular.copy(initialState.poolItems)
                    },
                    availableCustomerGroups: {
                        $set: angular.copy(initialState.availableCustomerGroups)
                    },
                    prices: {
                        $set: angular.copy(initialState.prices)
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
            title: 'Stoffe',
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
                active: {
                    name: 'active',
                    label: 'Active'
                },
                name: {
                    name: 'name',
                    label: 'Name',
                    translatedValue: true
                },
                identifier: {
                    name: 'identifier',
                    label: 'Identifier'
                },
                position: {
                    name: 'position',
                    label: 'Position'
                },
                clicks: {
                    name: 'clicks',
                    label: 'Klicks'
                },
                created: {
                    name: 'created',
                    label: 'Erstellt'
                }
            },
            listOptions: {
                table: ['id', 'active', 'name', 'identifier', 'position', 'clicks', 'created'],
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

    AptoReducersProvider.addReducer('pluginMaterialPickerMaterial', this.reducer);

    this.$get = function() {};
};

MaterialReducer.$inject = MaterialReducerInject;

export default ['MaterialPickerMaterialReducer', MaterialReducer];
