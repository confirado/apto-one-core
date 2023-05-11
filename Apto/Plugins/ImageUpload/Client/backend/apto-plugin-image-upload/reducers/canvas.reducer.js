import update from 'immutability-helper';

const ReducerInject = ['AptoReducersProvider'];
const Reducer = function(AptoReducersProvider) {
    const TYPE_NS = 'PLUGIN_IMAGE_UPLOAD_CANVAS_';
    const initialState = {
        pageHeaderConfig: getPageHeaderConfig(),
        dataListConfig: getDataListConfig(),
        list: [],
        ids: [],
        detail: {
            imageSettings: {
                active: true,
                previewSize: 250,
                maxFileSize: 4,
                minWidth: 0,
                minHeight: 0,
                allowedFileTypes: ['jpg', 'jpeg', 'png']
            },
            textSettings: {
                active: false,
                default: 'Mein Text!',
                fontSize: 25,
                textAlign: 'center',
                fill: '#ffffff',
                multiline: false,
                fonts: []
            },
            areaSettings: {
                image: null,
                width: 1000,
                height: 600,
                perspective: 'persp1',
                layer: '0',
                area: {
                    width: 0,
                    height: 0,
                    left: 0,
                    top: 0
                }
            },
            priceSettings: {
                surchargePrices: [],
                useSurchargeAsReplacement: false
            }
        }
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('SET_PAGE_NUMBER'): {
                state = setPageNumber(state, action.payload);
                break;
            }
            case getType('SET_SEARCH_STRING'): {
                state = setSearchString(state, action.payload);
                break;
            }
            case getType('FETCH_CANVAS_FULFILLED'): {
                state = update(state, {
                    detail: {
                        $set: action.payload.data.result
                    }
                });
                break;
            }
            case getType('FETCH_CANVAS_LIST_FULFILLED'): {
                state = update(state, {
                    list: {
                        $set: action.payload.data.result.data
                    }
                });
                state = setNumberOfPages(state, action.payload.data.result.numberOfPages);
                state = setNumberOfRecords(state, action.payload.data.result.numberOfRecords);
                break;
            }
            case getType('FETCH_CANVAS_IDS_FULFILLED'): {
                state = update(state, {
                    ids: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
            }
            case getType('RESET_CANVAS'): {
                state = update(state, {
                    detail: {
                        $set: angular.copy(initialState.detail)
                    }
                });
                break;
            }
        }

        switch (action.type) {
            case getType('FETCH_CANVAS_LIST_FULFILLED'):
                state = update(state, {
                    list: {
                        $set: action.payload.data.result.data
                    }
                });
                break;
        }

        return state;
    };

    function getPageHeaderConfig() {
        return {
            title: 'Druckbereiche',
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
                    commands: ['ImageUploadAddCanvas'],
                    queries: ['ImageUploadFindCanvas']
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
                identifier: {
                    name: 'identifier',
                    label: 'Identifier'
                },
                created: {
                    name: 'created',
                    label: 'Erstellt'
                }
            },
            listOptions: {
                table: ['id', 'identifier', 'created'],
                listTemplate: 'components/data-list/data-table-list.html'
            },
            itemActions: {
                edit: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['ImageUploadUpdateCanvas'], queries: ['ImageUploadFindCanvas']},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['ImageUploadRemoveCanvas'], queries: ['ImageUploadFindCanvasList']}
                }
            }
        }
    }

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

    AptoReducersProvider.addReducer('pluginImageUploadCanvas', this.reducer);

    this.$get = function() {};
};

Reducer.$inject = ReducerInject;

export default ['ImageUploadCanvasReducer', Reducer];
