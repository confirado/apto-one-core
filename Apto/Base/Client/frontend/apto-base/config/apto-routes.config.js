const AptoRoutesConfigInject = ['$routeProvider', '$httpProvider', 'APTO_ANGULAR_ROUTES'];
const AptoRoutesConfig = function ($routeProvider, $httpProvider, APTO_ANGULAR_ROUTES) {
    //Enable cross domain calls
    $httpProvider.defaults.useXDomain = true;

    // function to register routes
    const registerRoutes = function(routes) {
        if (!routes) {
            return;
        }

        // add angular routes
        for(let route in routes) {
            if (
                !routes.hasOwnProperty(route) ||
                !routes[route].template ||
                !routes[route].controller
            ) {
                continue;
            }

            $routeProvider.when(route, routes[route]);
        }
    };

    // register routes who are valid for all symfony routes
    registerRoutes(APTO_ANGULAR_ROUTES.all);

    // if route is not found call 404
    $routeProvider.otherwise('/404');
};
AptoRoutesConfig.$inject = AptoRoutesConfigInject;

export default ['AptoRoutesConfig', AptoRoutesConfig];