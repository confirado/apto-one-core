import update from 'immutability-helper';

const FilterCategoryReducerInject = ['AptoReducersProvider'];
const FilterCategoryReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_FILTER_CATEGORY_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Filter Kategorien',
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
                    commands: ['AddFilterCategory'],
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
                    aclMessagesRequired: {commands: ['UpdateFilterCategory'], queries: ['FindLanguages']},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveFilterCategory'], queries: []}
                }
            }
        },
        filterCategories: [],
        filterCategoryDetail: {}
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.filterCategory = function (state, action) {
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
            case getType('FETCH_FILTER_CATEGORIES_FULFILLED'):
                newState = update(state, {
                    filterCategories: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            // category detail
            case getType('FETCH_FILTER_CATEGORY_DETAIL_FULFILLED'):
                newState = update(state, {
                    filterCategoryDetail: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('FILTER_CATEGORY_DETAIL_RESET'):
                newState = update(state, {
                    filterCategoryDetail: {
                        $set: angular.copy(initialState.filterCategoryDetail)
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('filterCategory', this.filterCategory);

    this.$get = function() {};
};

FilterCategoryReducer.$inject = FilterCategoryReducerInject;

export default ['FilterCategoryReducer', FilterCategoryReducer];