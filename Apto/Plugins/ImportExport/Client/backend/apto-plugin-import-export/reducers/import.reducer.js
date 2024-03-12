import update from "immutability-helper";

const ReducerInject = ['AptoReducersProvider'];
const Reducer = function (AptoReducersProvider) {
    const TYPE_NS = 'APTO_PLUGIN_IMPORT_EXPORT_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Import',
            pagination: {
                show: false
            },
            search: {
                show: false,
                searchString: ''
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
                show: true
            }
        },
        results: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('IMPORT_FILE_FULFILLED'): {
                if (action.payload.data) {
                    state = update(state, {
                        results: {
                            $set: action.payload.data
                        }
                    });
                }

                break;
            }
            case getType('RESET_RESULTS'): {
                state = update(state, {
                    results: {
                        $set: []
                    }
                });

                break;
            }
        }
        return state;
    };

    AptoReducersProvider.addReducer('pluginImportExportImport', this.reducer);

    this.$get = function () {
    };
};

Reducer.$inject = ReducerInject;

export default ['PluginImportExportImportReducer', Reducer];