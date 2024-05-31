import update from 'immutability-helper';

const SettingsReducerInject = ['AptoReducersProvider'];

const SettingsReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_SETTINGS_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Einstellungen',
            pagination: {
                show: false
            },
            search: {
                show: false,
                searchString: ''
            },
            add: {
                show: false,
                aclMessagesRequired: {
                    commands: [],
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
                show: false
            }
        },
        settingsDetail: {
            id: null,
            primaryColor: '',
            secondaryColor: '',
            backgroundColorHeader: '',
            fontColorHeader: '',
            backgroundColorFooter: '',
            fontColorFooter: ''
        }
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.settings = function (state, action) {
        let newState;
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_SETTINGS_FULFILLED'): {
                if (null === action.payload.data.result) {
                    return state;
                }
                newState = update(state, {
                    settingsDetail: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            }
            case getType('RESET_SETTINGS'): {
                newState = update(state, {
                    pageHeaderConfig: {
                        $set: angular.copy(initialState.pageHeaderConfig)
                    },
                    settingsDetail: {
                        $set: angular.copy(initialState.settingsDetail)
                    }
                });
                return newState;
            }
        }
        return state;
    };

    AptoReducersProvider.addReducer('settings', this.settings);
    this.$get = function() {};
};

SettingsReducer.$inject = SettingsReducerInject;

export default ['SettingsReducer', SettingsReducer];
