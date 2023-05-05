import update from 'immutability-helper';

const CustomerGroupReducerInject = ['AptoReducersProvider'];
const CustomerGroupReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_CUSTOMER_GROUP_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Kundengruppen',
            pagination: {
                show: false
            },
            search: {
                show: true,
                searchString: ''
            },
            add: {
                show: true,
                aclMessagesRequired: {commands: ['AddCustomerGroup'], queries: ['FindCustomerGroups']}
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
                inputGross: {
                    name: 'inputGross',
                    label: 'Eingabe Brutto'
                },
                showGross: {
                    name: 'showGross',
                    label: 'Anzeige Brutto'
                },
                externalId: {
                    name: 'externalId',
                    label: 'externe Id'
                },
                fallback: {
                    name: 'fallback',
                    label: 'Fallback'
                }
            },
            listOptions: {
                card: {
                    headline: ['name'],
                    subHeadline: ['id'],
                    content: ['inputGross', 'showGross', 'externalId', 'fallback'],
                    cardColumns: 3
                },
                table: ['id', 'name', 'inputGross', 'showGross', 'externalId', 'fallback'],
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
                    aclMessagesRequired: {commands: ['UpdateCustomerGroup'], queries: ['FindCustomerGroup', 'FindCustomerGroups']},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveCustomerGroup'], queries: ['FindCustomerGroups']}
                }
            }
        },
        customerGroups: [],
        details: {}
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.customerGroup = function (state, action) {
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
            // customer list
            case getType('CUSTOMER_GROUPS_FETCH_FULFILLED'):
                newState = update(state, {
                    customerGroups: {
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

            case getType('RESET_DETAILS'):
                newState = update(state, {
                    details: {
                        $set: angular.copy(initialState.details)
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('customerGroup', this.customerGroup);

    this.$get = function() {};
};

CustomerGroupReducer.$inject = CustomerGroupReducerInject;

export default ['CustomerGroupReducer', CustomerGroupReducer];