import update from 'immutability-helper';

const ContentSnippetReducerInject = ['AptoReducersProvider'];
const ContentSnippetReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_CONTENT_SNIPPET_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Textbausteine',
            pagination: {
                show: false,
            },
            search: {
                show: false,
                searchString: '',
            },
            add: {
                show: true,
                aclMessagesRequired: {
                    commands: ['AddContentSnippet'],
                    queries: ['FindContentSnippetTree'],
                },
            },
            listStyle: {
                show: false,
            },
            listSettings: {
                show: false,
            },
            selectAll: {
                show: false,
            },
            toggleSideBarRight: {
                show: true,
            },
        },
        contentSnippetTree: [],
        contentSnippetDetails: {
            parent: null,
        },
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.contentSnippet = function (state, action) {
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
                                $set: action.payload,
                            },
                        },
                    },
                });

                return newState;

            case getType('TREE_FETCH_FULFILLED'):
                newState = update(state, {
                    contentSnippetTree: {
                        $set: action.payload.data.result,
                    },
                });

                return newState;
            case getType('DETAIL_FETCH_FULFILLED'):
                newState = update(state, {
                    contentSnippetDetails: {
                        $set: action.payload.data.result,
                    },
                });

                return newState;
            case getType('DETAIL_RESET'):
                newState = update(state, {
                    contentSnippetDetails: {
                        $set: angular.copy(initialState.contentSnippetDetails),
                    },
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('contentSnippet', this.contentSnippet);

    this.$get = function() {};
};

ContentSnippetReducer.$inject = ContentSnippetReducerInject;

export default ['ContentSnippetReducer', ContentSnippetReducer];
