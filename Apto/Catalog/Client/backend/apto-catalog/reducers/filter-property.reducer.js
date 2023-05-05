import update from 'immutability-helper';

const FilterPropertyReducerInject = ['AptoReducersProvider'];
const FilterPropertyReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_FILTER_PROPERTY_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Filter Eigenschaften',
            pagination: {
                show: false
            },
            search: {
                show: true,
                searchString: ''
            },
            add: {
                show: true,
                aclMessagesRequired: {
                    commands: ['AddFilterProperty'],
                    queries: ['FindLanguages']
                }
            },
            listStyle: {
                show: true
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
                card: {
                    headline: ['name'],
                    subHeadline: ['id', 'created'],
                    cardColumns: 3
                },
                table: ['id', 'name', 'created'],
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
                    aclMessagesRequired: {commands: ['UpdateFilterProperty'], queries: ['FindLanguages']},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveFilterProperty'], queries: []}
                }
            }
        },
        filterProperties: [],
        filterPropertyDetail: {
            filterCategories: []
        }
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.filterProperty = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
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
            case getType('FETCH_FILTER_PROPERTIES_FULFILLED'):
                newState = update(state, {
                    filterProperties: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            // group detail
            case getType('FETCH_FILTER_PROPERTY_DETAIL_FULFILLED'):
                newState = update(state, {
                    filterPropertyDetail: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('FILTER_PROPERTY_DETAIL_RESET'):
                newState = update(state, {
                    filterPropertyDetail: {
                        $set: angular.copy(initialState.filterPropertyDetail)
                    }
                });

                return newState;
            case getType('FILTER_PROPERTY_DETAIL_ASSIGN_CATEGORIES'):
                newState = update(state, {
                    filterPropertyDetail: {
                        filterCategories: {
                            $set: action.payload
                        }
                    }
                });
                return newState;
        }
        return state;
    };

    AptoReducersProvider.addReducer('filterProperty', this.filterProperty);

    this.$get = function() {};
};

FilterPropertyReducer.$inject = FilterPropertyReducerInject;

export default ['FilterPropertyReducer', FilterPropertyReducer];