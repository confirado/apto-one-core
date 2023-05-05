import update from 'immutability-helper';

const CustomerReducerInject = ['AptoReducersProvider'];
const CustomerReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_CUSTOMER_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Kunden',
            pagination: {
                show: false
            },
            search: {
                show: true,
                searchString: ''
            },
            add: {
                show: false,
                aclMessagesRequired: {commands: [], queries: []}
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
                username: {
                    name: 'username',
                    label: 'Benutzername'
                },
                email: {
                    name: 'email',
                    label: 'E-Mail'
                },
                externalId: {
                    name: 'externalId',
                    label: 'externe Id'
                }
            },
            listOptions: {
                card: {
                    headline: ['username'],
                    subHeadline: ['id', 'email'],
                    content: ['externalId'],
                    cardColumns: 3
                },
                table: ['id', 'username', 'email', 'externalId'],
                listTemplate: 'components/data-list/data-table-list.html'
            },
            itemActions: {
                select: {
                    show: false,
                    field: 'id',
                    selected: {}
                },
                edit: {
                    show: false,
                    field: 'id',
                    aclMessagesRequired: {commands: [], queries: []},
                },
                remove: {
                    show: false,
                    field: 'id',
                    aclMessagesRequired: {commands: [], queries: []}
                }
            }
        },
        customers: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.customer = function (state, action) {
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
            case getType('CUSTOMERS_FETCH_FULFILLED'):
                newState = update(state, {
                    customers: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('customer', this.customer);

    this.$get = function() {};
};

CustomerReducer.$inject = CustomerReducerInject;

export default ['CustomerReducer', CustomerReducer];