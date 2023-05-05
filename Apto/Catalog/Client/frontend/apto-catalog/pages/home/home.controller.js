const AptoCatalogHomeControllerInject = ['$scope', 'ConfigurationService', 'RouteAccessFactory'];
const AptoCatalogHomeController = function ($scope, ConfigurationService, RouteAccessFactory) {
    ConfigurationService.initShopSession();
    $scope.routeAccess = RouteAccessFactory.routeAccess;
};

AptoCatalogHomeController.$inject = AptoCatalogHomeControllerInject;

export default ['AptoCatalogHomeController', AptoCatalogHomeController];
