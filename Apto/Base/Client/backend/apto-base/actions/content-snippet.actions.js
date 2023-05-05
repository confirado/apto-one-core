const ContentSnippetActionsInject = ['MessageBusFactory', 'PageHeaderActions'];
const ContentSnippetActions = function(MessageBusFactory, PageHeaderActions) {
    var self = this;
    const TYPE_NS = 'APTO_CONTENT_SNIPPET_';
    const factory = {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        contentSnippetTreeFetch: contentSnippetTreeFetch,
        contentSnippetDetailFetch: contentSnippetDetailFetch,
        contentSnippetDetailSave: contentSnippetDetailSave,
        contentSnippetRemove: contentSnippetRemove,
        contentSnippetDetailReset: contentSnippetDetailReset
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function contentSnippetTreeFetch(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('TREE_FETCH'),
                payload: MessageBusFactory.query('FindContentSnippetTree', [searchString])
            });
        };
    }

    function contentSnippetDetailFetch(id) {
        return {
            type: getType('DETAIL_FETCH'),
            payload: MessageBusFactory.query('FindContentSnippet', [id])
        }
    }

    function contentSnippetDetailSave(ContentSnippet) {
        return dispatch => {
            let commandArguments = [];
            commandArguments.push(ContentSnippet.name);
            commandArguments.push(ContentSnippet.active);
            commandArguments.push(ContentSnippet.content);
            commandArguments.push(ContentSnippet.parent);
            commandArguments.push(ContentSnippet.html);

            if(typeof ContentSnippet.id !== "undefined") {
                commandArguments.unshift(ContentSnippet.id);
                return dispatch({
                    type: getType('DETAIL_UPDATE'),
                    payload: MessageBusFactory.command('UpdateContentSnippet', commandArguments)
                });
            }

            return dispatch({
                type: getType('DETAIL_ADD'),
                payload: MessageBusFactory.command('AddContentSnippet', commandArguments)
            });
        }
    }

    function contentSnippetRemove(id) {
        return {
            type: getType('REMOVE'),
            payload: MessageBusFactory.command('RemoveContentSnippet', [id])
        }
    }

    function contentSnippetDetailReset() {
        return {
            type: getType('DETAIL_RESET')
        }
    }

    return factory;
};

ContentSnippetActions.$inject = ContentSnippetActionsInject;

export default ['ContentSnippetActions', ContentSnippetActions];