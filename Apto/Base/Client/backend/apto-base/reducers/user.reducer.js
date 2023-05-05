import update from 'immutability-helper';

const UserReducerInject = ['AptoReducersProvider'];
const UserReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_USER_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Benutzer',
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
                    commands: ['AddUser'],
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
                username: {
                    name: 'username',
                    label: 'Benutzername'
                },
                email: {
                    name: 'email',
                    label: 'E-Mail'
                },
                userRoles: {
                    name: 'userRoles',
                    label: 'Rollen',
                    displayField: 'name'
                }
            },
            listOptions: {
                card: {
                    headline: ['username'],
                    subHeadline: ['id', 'email'],
                    content: ['userRoles'],
                    cardColumns: 3
                },
                table: ['id', 'username', 'email', 'userRoles'],
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
                    aclMessagesRequired: {commands: ['UpdateUser'], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveUser'], queries: []}
                }
            }
        },
        users: [],
        userDetail: {
            userRoles: []
        },
        availableUserRoles: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.user = function (state, action) {
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
            case getType('USERS_FETCH_FULFILLED'):
                newState = update(state, {
                    users: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            // user detail
            case getType('USER_DETAIL_FETCH_FULFILLED'):
                newState = update(state, {
                    userDetail: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('AVAILABLE_USER_ROLES_FETCH_FULFILLED'):
                newState = update(state, {
                    availableUserRoles: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            case getType('USER_DETAIL_ASSIGN_USER_ROLES'):
                newState = update(state, {
                    userDetail: {
                        userRoles: {
                            $set: action.payload
                        }
                    }
                });

                return newState;
            case getType('USER_DETAIL_RESET'):
                newState = update(state, {
                    userDetail: {
                        $set: angular.copy(initialState.userDetail)
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('user', this.user);

    this.$get = function() {};
};

UserReducer.$inject = UserReducerInject;

export default ['UserReducer', UserReducer];