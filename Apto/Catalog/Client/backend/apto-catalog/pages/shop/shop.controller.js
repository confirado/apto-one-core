import ShopDetailTemplate from './shop-detail.controller.html';
import ShopDetailController from './shop-detail.controller';

const ShopControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'ShopActions', 'IndexActions'];
const ShopController = function($scope, $mdDialog, $ngRedux, ShopActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.shop.pageHeaderConfig,
            dataListConfig: state.shop.dataListConfig,
            shops: state.shop.shops
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: ShopActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        shopsFetch: ShopActions.shopsFetch,
        shopDetailFetch: ShopActions.shopDetailFetch,
        shopRemove: ShopActions.shopRemove,
        availableCategoriesFetch: ShopActions.availableCategoriesFetch,
        availableLanguagesFetch: ShopActions.availableLanguagesFetch
    })($scope);

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: $scope.shopsFetch
        },
        listStyle: {
            fnc: $scope.setListTemplate
        },
        toggleSideBarRight: {
            fnc: $scope.toggleSidebarRight
        }
    };

    $scope.dataListActions = {
        edit: {
            fnc: showDetailsDialog
        },
        remove: {
            fnc: function ($event, id) {
                $scope.shopRemove(id).then(function () {
                    $scope.shopsFetch(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.shopsFetch(
        $scope.pageHeaderConfig.search.searchString
    );

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);

        $scope.availableCategoriesFetch();
        $scope.availableLanguagesFetch();
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: ShopDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString,
                shopId: id
            },
            controller: ShopDetailController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

ShopController.$inject = ShopControllerInject;

export default ['ShopController', ShopController];