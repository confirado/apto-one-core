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
        }
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.settings = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {

            case getType('RESET'): {
                newState = update(state, {
                    pageHeaderConfig: {
                        $set: angular.copy(initialState.pageHeaderConfig)
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
