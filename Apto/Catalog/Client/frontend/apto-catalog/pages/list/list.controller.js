const AptoCatalogListControllerInject = ['$scope', 'ConfigurationService', 'RouteAccessFactory'];
const AptoCatalogListController = function ($scope, ConfigurationService, RouteAccessFactory) {
    ConfigurationService.initShopSession();
    $scope.routeAccess = RouteAccessFactory.routeAccess;
};

AptoCatalogListController.$inject = AptoCatalogListControllerInject;

export default ['AptoCatalogListController', AptoCatalogListController];
