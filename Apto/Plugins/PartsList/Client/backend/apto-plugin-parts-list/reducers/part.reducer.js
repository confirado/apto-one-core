import update from 'immutability-helper';

const ReducerInject = ['AptoReducersProvider'];
const Reducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_PLUGIN_PARTS_LIST_PART_';
    const initialState = {
        operatorsActive: [{
            id: 1,
            name: 'aktiv'
        }, {
            id: 0,
            name: 'nicht aktiv'
        }],
        operatorsEqual: [{
            id: 4,
            name: 'gleich'
        }, {
            id: 5,
            name: 'nicht gleich'
        }, {
            id: 2,
            name: 'kleiner'
        }, {
            id: 3,
            name: 'kleiner gleich'
        }, {
            id: 7,
            name: 'größer'
        }, {
            id: 6,
            name: 'größer gleich'
        }, {
            id: 8,
            name: 'enthält'
        }, {
            id: 9,
            name: 'enthält nicht'
        }],
        operatorsFull: [{
            id: 1,
            name: 'aktiv'
        }, {
            id: 0,
            name: 'nicht aktiv'
        }, {
            id: 4,
            name: 'gleich'
        }, {
            id: 5,
            name: 'nicht gleich'
        }, {
            id: 2,
            name: 'kleiner'
        }, {
            id: 3,
            name: 'kleiner gleich'
        }, {
            id: 7,
            name: 'größer'
        }, {
            id: 6,
            name: 'größer gleich'
        }, {
            id: 8,
            name: 'enthält'
        }, {
            id: 9,
            name: 'enthält nicht'
        }],
        pageHeaderConfig: {
            title: 'Teile',
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
                    commands: ['AptoPartsListAddPart'],
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
                active: {
                    name: 'active',
                    label: 'Aktiv'
                },
                partNumber: {
                    name: 'partNumber',
                    label: 'Teilenummer'
                },
                name: {
                    name: 'name',
                    label: 'Name',
                    translatedValue: true
                },
                description: {
                    name: 'description',
                    label: 'Beschreibung',
                    translatedValue: true
                },
                products: {
                    name: 'products',
                    label: 'Produkte'
                }
            },
            listOptions: {
                card: {
                    headline: ['active'],
                    subHeadline: ['partNumber', 'name'],
                    content: ['description', 'products'],
                    cardColumns: 3
                },
                table: ['active', 'partNumber', 'name', 'products'],
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
                    aclMessagesRequired: {commands: ['AptoPartsListUpdatePart'], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['AptoPartsListRemovePart'], queries: []}
                }
            }
        },
        list: [],
        details: {
            amount: 0,
            currencyCode: 'EUR'
        },
        elementUsageDetails: {},
        ruleUsageDetails: {},
        availableUnits: [],
        availableProducts: [],
        availableSections: [],
        availableElements: [],
        productsSectionsElements: [],
        productUsages: [],
        sectionUsages: [],
        elementUsages: [],
        ruleUsages: [],
        prices: [],
        availableCustomerGroups: []
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

            case getType('FETCH_AVAILABLE_UNITS_FULFILLED'):
                newState = update(state, {
                    availableUnits: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;

            case getType('FETCH_AVAILABLE_PRODUCTS_FULFILLED'):
                newState = update(state, {
                    availableProducts: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;

            case getType('FETCH_PRODUCTS_SECTIONS_ELEMENTS_FULFILLED'):
                newState = update(state, {
                    productsSectionsElements: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;

            case getType('FETCH_AVAILABLE_SECTIONS_FULFILLED'):
                newState = update(state, {
                    availableSections: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;

            case getType('FETCH_AVAILABLE_ELEMENTS_FULFILLED'):
                newState = update(state, {
                    availableElements: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;

            case getType('FETCH_ELEMENT_USAGE_DETAILS_FULFILLED'):
                newState = update(state, {
                    elementUsageDetails: {
                        $set: action.payload.data.result
                    }
                });

                return newState;

            case getType('AVAILABLE_CUSTOMER_GROUPS_FETCH_FULFILLED'):
                newState = update(state, {
                    availableCustomerGroups: {
                        $set: action.payload.data.result.data
                    }
                });
                return newState;

            case getType('FETCH_PRICES_FULFILLED'):
                newState = update(state, {
                    prices: {
                        $set: action.payload.data.result
                    }
                });
                return newState;

            case getType('FETCH_RULE_USAGE_DETAILS_FULFILLED'):
                newState = update(state, {
                    ruleUsageDetails: {
                        $set: action.payload.data.result
                    }
                });

                return newState;

            case getType('FETCH_PRODUCT_USAGES_FULFILLED'):
                newState = update(state, {
                    productUsages: {
                        $set: action.payload.data.result
                    }
                });

                return newState;

            case getType('FETCH_SECTION_USAGES_FULFILLED'):
                newState = update(state, {
                    sectionUsages: {
                        $set: action.payload.data.result
                    }
                });

                return newState;

            case getType('FETCH_ELEMENT_USAGES_FULFILLED'):
                newState = update(state, {
                    elementUsages: {
                        $set: action.payload.data.result
                    }
                });

                return newState;

            case getType('FETCH_RULE_USAGES_FULFILLED'):
                newState = update(state, {
                    ruleUsages: {
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

            case getType('RESET_ELEMENT_USAGE_DETAILS'):
                newState = update(state, {
                    elementUsageDetails: {
                        $set: angular.copy(initialState.elementUsageDetails)
                    }
                });
                return newState;

            case getType('RESET_RULE_USAGE_DETAILS'):
                newState = update(state, {
                    ruleUsageDetails: {
                        $set: angular.copy(initialState.ruleUsageDetails)
                    }
                });
                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('aptoPartsListPart', this.reducer);

    this.$get = function() {};
};

Reducer.$inject = ReducerInject;

export default ['AptoPartsListPartReducer', Reducer];
