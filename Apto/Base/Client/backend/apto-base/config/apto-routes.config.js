const AptoRoutesConfigInject = ['$routeProvider', 'APTO_ENVIRONMENT'];
const AptoRoutesConfig = function ($routeProvider, APTO_ENVIRONMENT) {
    for(let route in APTO_ENVIRONMENT.routes.angular) {
        if (APTO_ENVIRONMENT.routes.angular.hasOwnProperty(route)) {
            $routeProvider.when(
                route,
                APTO_ENVIRONMENT.routes.angular[route]
            );
        }
    }
};
AptoRoutesConfig.$inject = AptoRoutesConfigInject;

export default ['AptoRoutesConfig', AptoRoutesConfig];