import update from 'immutability-helper';

const ProductReducerInject = ['AptoReducersProvider'];
const ProductReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_PRODUCT_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Produkte',
            pagination: {
                show: true,
                pageNumber: 1,
                recordsPerPage: 20,
                numberOfRecords: 0,
                numberOfPages: 1
            },
            search: {
                show: false
            },
            add: {
                show: true,
                aclMessagesRequired: {
                    commands: ['AddProduct'],
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
                position: {
                    name: 'position',
                    label: 'Position'
                },
                active: {
                    name: 'active',
                    label: 'Aktiv'
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
                shops: {
                    name: 'shops',
                    label: 'Shops',
                    displayField: 'name'
                },
                created: {
                    name: 'created',
                    label: 'Erstellt'
                }
            },
            listOptions: {
                card: {
                    headline: ['name'],
                    subHeadline: ['id', 'active', 'created'],
                    content: ['description', 'shops'],
                    cardColumns: 3
                },
                table: ['id', 'position', 'active', 'name', 'description','created'],
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
                    aclMessagesRequired: {commands: ['UpdateProduct'], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveProduct'], queries: []}
                },
                copy: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['CopyProduct'], queries: []}
                }
            }
        },
        products: [],
        productDetail: {
            shops: [],
            categories: [],
            sections: [],
            useStepByStep: false,
            filterProperties: [],
            keepSectionOrder: true,
        },
        batchMessage: '',
        availableCategories: [],
        availableShops: [],
        availableCustomerGroups: [],
        availablePriceCalculators: [],
        sections: [],
        sectionsElements: [],
        rules: [],
        prices: [],
        discounts: [],
        customProperties: [],
        computedValues: [],
        computedValueDetail: {},
        nextPosition: 0
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.product = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            // page header changes
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
            // data list changes
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
            // products changes
            case getType('PRODUCTS_RECEIVED'):
                newState = update(state, {
                    products: {
                        $set: action.payload
                    }
                });
                return newState;
            // product detail
            case getType('PRODUCT_DETAIL_FETCH_FULFILLED'):
                newState = update(state, {
                    productDetail: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('AVAILABLE_CATEGORIES_FETCH_FULFILLED'):
                newState = update(state, {
                    availableCategories: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('FETCH_CATEGORIES_FULFILLED'):
                newState = update(state, {
                    availableCategories: {
                        $set: action.payload.data.result.data
                    }
                });
                return newState;
            case getType('AVAILABLE_SHOPS_FETCH_FULFILLED'):
                newState = update(state, {
                    availableShops: {
                        $set: action.payload.data.result.data
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
            case getType('AVAILABLE_PRICE_CALCULATORS_FETCH_FULFILLED'):
                newState = update(state, {
                    availablePriceCalculators: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('PRODUCT_DETAIL_ASSIGN_SHOPS'):
                newState = update(state, {
                    productDetail: {
                        shops: {
                            $set: action.payload
                        }
                    }
                });
                return newState;
            case getType('PRODUCT_DETAIL_ASSIGN_CATEGORIES'):
                newState = update(state, {
                    productDetail: {
                        categories: {
                            $set: action.payload
                        }
                    }
                });
                return newState;
            case getType('FETCH_SECTIONS_FULFILLED'):
                newState = update(state, {
                    sections: {
                        $set: action.payload.data.result.sections
                    }
                });
                return newState;
            case getType('FETCH_SECTIONS_ELEMENTS_FULFILLED'):
                newState = update(state, {
                    sectionsElements: {
                        $set: action.payload.data.result.sections
                    }
                });
                return newState;
            case getType('PRODUCT_DETAIL_RESET'):
                newState = update(state, {
                    productDetail: {
                        $set: angular.copy(initialState.productDetail)
                    },
                    sections: {
                        $set: angular.copy(initialState.sections)
                    }
                });
                return newState;
            case getType('FETCH_RULES_FULFILLED'):
                newState = update(state, {
                    rules: {
                        $set: action.payload.data.result.rules
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
            case getType('FETCH_DISCOUNTS_FULFILLED'):
                newState = update(state, {
                    discounts: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('FETCH_CUSTOM_PROPERTIES_FULFILLED'):1
                newState = update(state, {
                    customProperties: {
                        $set: action.payload.data.result.customProperties
                    }
                });
                return newState;
            case getType('FETCH_COMPUTED_VALUES_FULFILLED'):
                newState = update(state, {
                    computedValues: {
                        $set: action.payload.data.result.computedProductValues
                    }
                });
                return newState;
            case getType('FETCH_COMPUTED_VALUE'):
                const computedValueDetail = getComputedValueDetail(state, action.payload);
                newState = update(state, {
                    computedValueDetail: {
                        $set: computedValueDetail
                    }
                });
                return newState;
            case getType('SET_DETAIL_VALUE'):
                let detailUpdate = {};
                detailUpdate[action.payload.key] = {
                    $set: action.payload.value
                };

                newState = update(state, {
                    productDetail: detailUpdate
                });
                return newState;
            case ('APTO_BATCH_MANIPULATION_SET_NEW_PRICES_FULFILLED'):
                let message = 'Stapelverarbeitung abgeschlossen!';
                if (action.payload.data.message.error) {
                    message = 'Fehler bei Stapelverarbeitung aufgetreten!';
                }
                newState = update(state, {
                    batchMessage: {
                        $set: message
                    }
                });
                return newState;
            case ('APTO_BATCH_MANIPULATION_FETCH_CURRENT_PRICES_ERROR'):
                newState = update(state, {
                        batchMessage: {
                            $set: action.payload
                        }
                })
                return newState;
            case ('APTO_BATCH_MANIPULATION_FETCH_CURRENT_PRICES_PENDING'):
                let bMessage = 'Stapelverarbeitung wird durchgeführt';
                newState = update(state, {
                        batchMessage: {
                            $set: bMessage
                        }
                })
                return newState;
            case getType('GET_NEXT_POSITION_FULFILLED'):
                newState = update(state, {
                        nextPosition: {
                            $set: action.payload.data.result
                        }
                })
                return newState;
            case getType('PRODUCT_DETAIL_ASSIGN_PROPERTIES'):
                newState = update(state, {
                    productDetail: {
                        filterProperties: {
                            $set: action.payload
                        }
                    }
                });
                return newState;
        }
        return state;
    };

    function getComputedValueDetail(state, valueId) {
        for (let i = 0; i < state.computedValues.length; i++) {
            if (state.computedValues[i].id === valueId) {
                return state.computedValues[i];
            }
        }
        return {}
    }

    AptoReducersProvider.addReducer('product', this.product);

    this.$get = function() {};
};

ProductReducer.$inject = ProductReducerInject;

export default ['ProductReducer', ProductReducer];
