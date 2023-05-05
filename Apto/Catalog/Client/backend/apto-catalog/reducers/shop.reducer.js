import update from 'immutability-helper';

const ShopReducerInject = ['AptoReducersProvider'];
const ShopReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_SHOP_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Domains',
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
                    commands: ['AddShop'],
                    queries: []
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
                    label: 'Name'
                },
                domain: {
                    name: 'domain',
                    label: 'Domain'
                },
                connectorUrl: {
                    name: 'connectorUrl',
                    label: 'Connector URL',
                    translatedValue: true
                },
                description: {
                    name: 'description',
                    label: 'Beschreibung'
                },
                created: {
                    name: 'created',
                    label: 'Erstellt'
                }
            },
            listOptions: {
                card: {
                    headline: ['name'],
                    subHeadline: ['id', 'domain', 'created'],
                    content: ['connectorUrl', 'description'],
                    cardColumns: 3
                },
                table: ['id', 'name', 'domain', 'connectorUrl', 'created'],
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
                    aclMessagesRequired: {commands: ['UpdateShop'], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveShop'], queries: []}
                }
            }
        },
        shops: [],
        shopDetail: {
            categories: [],
            languages: []
        },
        availableCategories: [],
        availableLanguages: [],
        customProperties: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.shop = function (state, action) {
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
            case getType('SHOPS_FETCH_FULFILLED'):
                newState = update(state, {
                    shops: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            // shop detail
            case getType('SHOP_DETAIL_FETCH_FULFILLED'):
                newState = update(state, {
                    shopDetail: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('AVAILABLE_CATEGORIES_FETCH_FULFILLED'):
                newState = update(state, {
                    availableCategories: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            case getType('AVAILABLE_LANGUAGES_FETCH_FULFILLED'):
                newState = update(state, {
                    availableLanguages: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            case getType('SHOP_DETAIL_ASSIGN_CATEGORIES'):
                newState = update(state, {
                    shopDetail: {
                        categories: {
                            $set: action.payload
                        }
                    }
                });

                return newState;
            case getType('SHOP_DETAIL_ASSIGN_LANGUAGES'):
                newState = update(state, {
                    shopDetail: {
                        languages: {
                            $set: action.payload
                        }
                    }
                });

                return newState;
            case getType('SHOP_DETAIL_RESET'):
                newState = update(state, {
                    shopDetail: {
                        $set: angular.copy(initialState.shopDetail)
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
        }

        return state;
    };

    AptoReducersProvider.addReducer('shop', this.shop);

    this.$get = function() {};
};

ShopReducer.$inject = ShopReducerInject;

export default ['ShopReducer', ShopReducer];