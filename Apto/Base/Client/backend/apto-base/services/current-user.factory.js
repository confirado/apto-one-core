const CurrentUserFactoryInject = ['$ngRedux', 'IndexActions'];
const CurrentUserFactory = function($ngRedux, IndexActions) {
    const self = this;

    let currentUserFactory = {
        currentUser: null,
        toggleRte: toggleRte,
        isRteEnabled: isRteEnabled
    };

    function mapStateToThis(state) {
        return {
            currentUser: state.index.currentUser
        }
    }

    function reduxMapActions() {
        return {
            currentUserFetch: IndexActions.currentUserFetch,
            setSessionCurrentUserRte: IndexActions.setSessionCurrentUserRte
        }
    }

    function toggleRte() {
        if (!currentUserFactory.currentUser || currentUserFactory.currentUser.id !== 'superadmin') {
            return;
        }

        const rte = 'trumbowyg';
        self.setSessionCurrentUserRte(currentUserFactory.currentUser.rte === rte ? '' : rte);
    }

    function isRteEnabled() {
        if (!currentUserFactory.currentUser || currentUserFactory.currentUser.id !== 'superadmin' || currentUserFactory.currentUser.rte !== 'trumbowyg') {
            return false;
        }

        return true;
    }

    $ngRedux.connect(mapStateToThis)(currentUserFactory);
    $ngRedux.connect(null, reduxMapActions())(self);

    self.currentUserFetch();

    return currentUserFactory;
};

CurrentUserFactory.$inject = CurrentUserFactoryInject;

export default ['CurrentUserFactory', CurrentUserFactory];