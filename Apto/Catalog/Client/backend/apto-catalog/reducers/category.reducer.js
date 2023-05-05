import update from 'immutability-helper';

const CategoryReducerInject = ['AptoReducersProvider'];
const CategoryReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_CATEGORY_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Kategorien',
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
                    commands: ['AddCategory'],
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
        categoryTree: [],
        categoryDetail: {},
        customProperties: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.category = function (state, action) {
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
            case getType('TREE_FETCH_FULFILLED'):
                newState = update(state, {
                    categoryTree: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            // category detail
            case getType('CATEGORY_DETAIL_FETCH_FULFILLED'):
                newState = update(state, {
                    categoryDetail: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('CATEGORY_DETAIL_RESET'):
                newState = update(state, {
                    categoryDetail: {
                        $set: angular.copy(initialState.categoryDetail)
                    }
                });

                return newState;

            case getType('FETCH_CUSTOM_PROPERTIES_FULFILLED'):
                newState = update(state, {
                    customProperties: {
                        $set: action.payload.data.result.customProperties
                    }
                });
                return newState;

            case getType('SET_DETAIL_VALUE'):
                let detailUpdate = {};
                detailUpdate[action.payload.key] = {
                    $set: action.payload.value
                };

                newState = update(state, {
                    categoryDetail: detailUpdate
                });
                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('category', this.category);

    this.$get = function() {};
};

CategoryReducer.$inject = CategoryReducerInject;

export default ['CategoryReducer', CategoryReducer];