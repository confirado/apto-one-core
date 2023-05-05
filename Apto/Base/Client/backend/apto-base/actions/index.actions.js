const IndexActionsInject = ['$mdSidenav', '$ngRedux', 'MessageBusFactory'];
const IndexActions = function($mdSidenav, $ngRedux, MessageBusFactory) {
    const TYPE_NS = 'APTO_INDEX_';
    const factory = {
        toggleSidebarRight: toggleSidebarRight,
        messagesGrantedFetch: messagesGrantedFetch,
        currentUserFetch: currentUserFetch,
        languagesFetch: languagesFetch,
        setActiveLanguage: setActiveLanguage,
        clearAptoCache: clearAptoCache,
        setSessionCurrentUserRte: setSessionCurrentUserRte
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function toggleSidebarRight() {
        let payload = false;
        if($mdSidenav('right').isLockedOpen()) {
            $mdSidenav('right').close();
        }
        else {
            payload = true;
            $mdSidenav('right').open();
        }
        return {
            type: getType('TOGGLE_SIDEBAR_RIGHT'),
            payload: payload
        }
    }

    function messagesGrantedFetch() {
        MessageBusFactory.messagesIsGranted().then(function (response) {
            $ngRedux.dispatch(messagesGrantedReceived(response.data.result.messagesGranted));
        }, function (response) {
            console.log(response);
        });

        return {
            type: getType('MESSAGES_GRANTED_FETCH')
        }
    }

    function messagesGrantedReceived(payload) {
        return {
            type: getType('MESSAGES_GRANTED_RECEIVED'),
            payload: payload
        }
    }

    function currentUserFetch() {
        MessageBusFactory.query('FindCurrentUser', [aptoBackendCurrentUserName]).then(function (response) {
            $ngRedux.dispatch(currentUserReceived(response.data.result));
        }, function (response) {
            console.log(response);
        });

        return {
            type: getType('CURRENT_USER_FETCH')
        }
    }

    function currentUserReceived(payload) {
        return {
            type: getType('CURRENT_USER_RECEIVED'),
            payload: payload
        }
    }

    function languagesFetch() {
        return {
            type: getType('LANGUAGES_FETCH'),
            payload: MessageBusFactory.query('FindLanguages', [])
        }
    }

    function setActiveLanguage(payload) {
        return {
            type: getType('SET_ACTIVE_LANGUAGE'),
            payload: payload
        }
    }

    function clearAptoCache(types) {
        if (!types) {
            types = [];
        }

        return {
            type: getType('CLEAR_MEDIA_FILE_CACHE'),
            payload: MessageBusFactory.command('ClearAptoCache', [types])
        }
    }

    function setSessionCurrentUserRte(rte) {
        return {
            type: getType('SET_SESSION_CURRENT_USER_RTE'),
            payload: rte
        }
    }

    return factory;
};

IndexActions.$inject = IndexActionsInject;

export default ['IndexActions', IndexActions];