import update from 'immutability-helper';

const LanguageReducerInject = ['AptoReducersProvider'];
const LanguageReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_LANGUAGE_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Sprachen',
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
                    commands: ['AddLanguage'],
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
                isocode: {
                    name: 'isocode',
                    label: 'Isocode'
                }
            },
            listOptions: {
                card: {
                    headline: ['name'],
                    subHeadline: ['id', 'isocode'],
                    content: [],
                    cardColumns: 3
                },
                table: ['id', 'name', 'isocode'],
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
                    aclMessagesRequired: {commands: ['UpdateLanguage'], queries: ['FindLanguages']},
                },
                remove: {
                    show: true,
                    field: 'id',
                    aclMessagesRequired: {commands: ['RemoveLanguage'], queries: []}
                }
            }
        },
        languages: [],
        languageDetails: {}
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.language = function (state, action) {
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
            case getType('LANGUAGES_FETCH_FULFILLED'):
                newState = update(state, {
                    languages: {
                        $set: action.payload.data.result.data
                    }
                });

                return newState;
            case getType('LANGUAGE_DETAIL_FETCH_FULFILLED'):
                newState = update(state, {
                    languageDetails: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            case getType('LANGUAGE_DETAIL_RESET'):
                newState = update(state, {
                    languageDetails: {
                        $set: angular.copy(initialState.languageDetails)
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('language', this.language);

    this.$get = function() {};
};

LanguageReducer.$inject = LanguageReducerInject;

export default ['LanguageReducer', LanguageReducer];