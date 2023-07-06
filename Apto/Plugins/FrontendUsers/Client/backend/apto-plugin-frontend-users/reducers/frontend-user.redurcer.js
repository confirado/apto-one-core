import update from 'immutability-helper';

const FrontendUserReducerInject = ['AptoReducersProvider'];
const FrontendUserReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_FRONTEND_USER_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Frontend Benutzer',
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
                    commands: ['AddFrontendUser'],
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
                active: {
                  name: 'active',
                  label: 'Aktiv'
                },
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
                externalCustomerGroupId: {
                    name: 'externalCustomerGroupId',
                    label: 'Kundengruppe (External Id)'
                },
                customerNumber: {
                    name: 'customerNumber',
                    label: 'Kundennummer'
                }
            },
            listOptions: {
                card: {
                    headline: ['username'],
                    subHeadline: ['id', 'email', 'externalCustomerGroupId', 'customerNumber'],
                    cardColumns: 3
                },
                table: ['id', 'username', 'email', 'externalCustomerGroupId', 'customerNumber'],
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
                    aclMessagesRequired: {commands: ['UpdateFrontendUser'], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveFrontendUser'], queries: []}
                }
            }
        },
        frontendUsers: [],
        frontendUserDetail: {},
        availableCustomerGroups: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.frontendUser = function (state, action) {
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
            // user list
            case getType('FRONTEND_USERS_FETCH_FULFILLED'):
                newState = update(state, {
                    frontendUsers: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            // user detail
            case getType('FRONTEND_USER_DETAIL_FETCH_FULFILLED'):
                newState = update(state, {
                    frontendUserDetail: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('FRONTEND_USER_DETAIL_RESET'):
                newState = update(state, {
                    frontendUserDetail: {
                        $set: angular.copy(initialState.frontendUserDetail)
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
        }

        return state;
    };

    AptoReducersProvider.addReducer('frontendUser', this.frontendUser);

    this.$get = function() {};
};

FrontendUserReducer.$inject = FrontendUserReducerInject;

export default ['FrontendUserReducer', FrontendUserReducer];
