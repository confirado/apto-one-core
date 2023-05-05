const FactoryInject = ['$rootScope', '$location', '$ngRedux', 'APTO_FRONTEND_ROUTE_ACCESS'];
const Factory = function ($rootScope, $location, $ngRedux, APTO_FRONTEND_ROUTE_ACCESS) {
    const redux = { state: null, actions: null };
    let routeAccess = {
        hasAccess: false
    };

    $ngRedux.connect(mapStateToThis)((selectedState, actions) => {
        redux.state = selectedState;
        redux.actions = actions;
        routeAccess.hasAccess = hasAccess();
    });

    function mapStateToThis(state) {
        let mapping = {
            isLoggedIn: false
        };

        if (state.frontendUser) {
            mapping.isLoggedIn = state.frontendUser.isLoggedIn;
        }

        return mapping;
    }

    function hasAccess() {
        if (APTO_FRONTEND_ROUTE_ACCESS.onlyLoggedIn === false) {
            return true;
        }

        const whiteList = APTO_FRONTEND_ROUTE_ACCESS.onlyLoggedInWhiteList;
        for (let i = 0; i < whiteList.length; i++) {
            const path = $location.path();
            if (path.startsWith(whiteList[i])) {
                return true;
            }
        }

        return redux.state.isLoggedIn;
    }

    $rootScope.$on('$routeChangeStart', (event, next) => {
        routeAccess.hasAccess = hasAccess();
    });

    return {
        routeAccess: routeAccess
    };
};

Factory.$inject = FactoryInject;

export default ['RouteAccessFactory', Factory];
