import update from 'immutability-helper';

const GroupReducerInject = ['AptoReducersProvider'];
const GroupReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_GROUP_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Gruppen',
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
                    commands: ['AddGroup'],
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
                    aclMessagesRequired: {commands: ['UpdateGroup'], queries: ['FindLanguages']},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveGroup'], queries: []}
                }
            }
        },
        groups: [],
        groupDetail: {}
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.group = function (state, action) {
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
            case getType('FETCH_GROUPS_FULFILLED'):
                newState = update(state, {
                    groups: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            // group detail
            case getType('FETCH_GROUP_DETAIL_FULFILLED'):
                newState = update(state, {
                    groupDetail: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('GROUP_DETAIL_RESET'):
                newState = update(state, {
                    groupDetail: {
                        $set: angular.copy(initialState.groupDetail)
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('group', this.group);

    this.$get = function() {};
};

GroupReducer.$inject = GroupReducerInject;

export default ['GroupReducer', GroupReducer];