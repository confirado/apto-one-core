const AclIsGrantedFactoryInject = ['$ngRedux', 'IndexActions'];
const AclIsGrantedFactory = function($ngRedux, IndexActions) {
    var factory = {
        allGranted: allGranted,
        oneGranted: oneGranted,
        showGrantedInfo: showGrantedInfo,
        messagesGranted: {
            commands: {},
            queries: {}
        }
    };

    this.mapStateToThis = function(state) {
        return {
            messagesGranted: state.index.messagesGranted
        }
    };

    $ngRedux.connect(this.mapStateToThis)(factory);
    $ngRedux.connect(null, {
        messagesGrantedFetch: IndexActions.messagesGrantedFetch
    })(this);

    this.messagesGrantedFetch();

    function allGranted(messages) {
        let ci, qi;
        for (ci = 0; ci < messages.commands.length; ci++) {
            if (!factory.messagesGranted.commands[messages.commands[ci]]) {
                return false;
            }
        }

        for(qi = 0; qi < messages.queries.length; qi++) {
            if (!factory.messagesGranted.queries[messages.queries[qi]]) {
                return false;
            }
        }
        return true;
    }

    function oneGranted(messages) {
        let ci, qi;
        if (messages.commands.length < 1 && messages.queries.length < 1) {
            return true;
        }
        for (ci = 0; ci < messages.commands.length; ci++) {
            if (factory.messagesGranted.commands[messages.commands[ci]]) {
                return true;
            }
        }

        for(qi = 0; qi < messages.queries.length; qi++) {
            if (factory.messagesGranted.queries[messages.queries[qi]]) {
                return true;
            }
        }
        return false;
    }

    function showGrantedInfo() {
        let messages = {
            commands: ['AddAclPermission', 'RemoveAclPermission'],
            queries: ['FindMessageBusMessages', 'FindAclEntriesByAclClass', 'FindUserRoles']
        };
        return allGranted(messages);
    }

    return factory;
};

AclIsGrantedFactory.$inject = AclIsGrantedFactoryInject;

export default ['AclIsGrantedFactory', AclIsGrantedFactory];