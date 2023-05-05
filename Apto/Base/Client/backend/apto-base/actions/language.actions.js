const LanguageActionsInject = ['MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const LanguageActions = function(MessageBusFactory, PageHeaderActions, DataListActions) {
    var self = this;
    const TYPE_NS = 'APTO_LANGUAGE_';
    const factory = {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        languagesFetch: languagesFetch,
        languageDetailFetch: languageDetailFetch,
        languageDetailSave: languageDetailSave,
        languageRemove: languageRemove,
        languageDetailReset: languageDetailReset
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function languagesFetch(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('LANGUAGES_FETCH'),
                payload: MessageBusFactory.query('FindLanguages', [searchString])
            });
        };
    }

    function languageDetailFetch(id) {
        return {
            type: getType('LANGUAGE_DETAIL_FETCH'),
            payload: MessageBusFactory.query('FindLanguage', [id])
        }
    }

    function languageDetailSave(language) {
        return dispatch => {
            let commandArguments = [];
            commandArguments.push(language.name);
            commandArguments.push(language.isocode);

            if(typeof language.id !== "undefined") {
                commandArguments.unshift(language.id);
                return dispatch({
                    type: getType('LANGUAGE_DETAIL_UPDATE'),
                    payload: MessageBusFactory.command('UpdateLanguage', commandArguments)
                });
            }

            return dispatch({
                type: getType('LANGUAGE_DETAIL_ADD'),
                payload: MessageBusFactory.command('AddLanguage', commandArguments)
            });
        }
    }

    function languageRemove(id) {
        return {
            type: getType('LANGUAGE_REMOVE'),
            payload: MessageBusFactory.command('RemoveLanguage', [id])
        }
    }

    function languageDetailReset() {
        return {
            type: getType('LANGUAGE_DETAIL_RESET')
        }
    }

    return factory;
};

LanguageActions.$inject = LanguageActionsInject;

export default ['LanguageActions', LanguageActions];