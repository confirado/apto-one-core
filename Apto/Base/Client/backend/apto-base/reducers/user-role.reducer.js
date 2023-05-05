import update from 'immutability-helper';

const UserRoleReducerInject = ['AptoReducersProvider'];
const UserRoleReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_USER_ROLE_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Benutzerrollen',
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
                    commands: ['AddUserRole'],
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
                identifier: {
                    name: 'identifier',
                    label: 'Identifier'
                },
                name: {
                    name: 'name',
                    label: 'Name'
                },
                children: {
                    name: 'children',
                    label: 'Erbt von',
                    displayField: 'name'
                }
            },
            listOptions: {
                card: {
                    headline: ['name'],
                    subHeadline: ['id', 'identifier'],
                    content: ['children'],
                    cardColumns: 3
                },
                table: ['id', 'name', 'identifier', 'children'],
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
                    aclMessagesRequired: {commands: ['UpdateUserRole'], queries: []},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveUserRole'], queries: []}
                }
            }
        },
        userRoles: [],
        userRoleDetail: {
            children: []
        },
        availableChildren: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.userRole = function (state, action) {
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
            // userRole list
            case getType('USER_ROLES_FETCH_FULFILLED'):
                newState = update(state, {
                    userRoles: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            // userRole detail
            case getType('USER_ROLE_DETAIL_FETCH_FULFILLED'):
                newState = update(state, {
                    userRoleDetail: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('AVAILABLE_CHILDREN_FETCH_FULFILLED'):
                newState = update(state, {
                    availableChildren: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            case getType('USER_ROLE_DETAIL_ASSIGN_CHILDREN'):
                newState = update(state, {
                    userRoleDetail: {
                        children: {
                            $set: action.payload
                        }
                    }
                });

                return newState;
            case getType('USER_ROLE_DETAIL_RESET'):
                newState = update(state, {
                    userRoleDetail: {
                        $set: angular.copy(initialState.userRoleDetail)
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('userRole', this.userRole);

    this.$get = function() {};
};

UserRoleReducer.$inject = UserRoleReducerInject;

export default ['UserRoleReducer', UserRoleReducer];