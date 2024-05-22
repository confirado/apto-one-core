import ShopTab from './shop-tab.html';
import CategoryTab from './category-tab.html';
import LanguageTab from './language-tab.html';
import CustomPropertiesTab from './custom-properties-tab.html';

const ShopDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', '$templateCache', 'ShopActions', 'targetEvent', 'showDetailsDialog', 'searchString', 'shopId'];
const ShopDetailController = function($scope, $mdDialog, $ngRedux, $templateCache, ShopActions, targetEvent, showDetailsDialog, searchString, shopId) {
    $templateCache.put('catalog/pages/shop/shop-tab.html', ShopTab);
    $templateCache.put('catalog/pages/shop/category-tab.html', CategoryTab);
    $templateCache.put('catalog/pages/shop/language-tab.html', LanguageTab);
    $templateCache.put('catalog/pages/shop/custom-properties-tab.html', CustomPropertiesTab);

    $scope.mapStateToThis = function(state) {
        return {
            shopDetail: state.shop.shopDetail,
            availableCategories: state.shop.availableCategories,
            availableLanguages: state.shop.availableLanguages,
            customProperties: state.shop.customProperties
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        shopsFetch: ShopActions.shopsFetch,
        shopDetailFetch: ShopActions.shopDetailFetch,
        shopDetailSave: ShopActions.shopDetailSave,
        shopDetailReset: ShopActions.shopDetailReset,
        shopDetailAssignCategories: ShopActions.shopDetailAssignCategories,
        shopDetailAssignLanguages: ShopActions.shopDetailAssignLanguages,
        fetchShopCustomProperties: ShopActions.fetchShopCustomProperties,
        addShopCustomProperty: ShopActions.addShopCustomProperty,
        removeShopCustomProperty: ShopActions.removeShopCustomProperty,
    })($scope);

    function init() {
        if(typeof shopId !== "undefined") {
            $scope.shopDetailFetch(shopId);
            $scope.fetchShopCustomProperties(shopId);
        }
    }

    function save(shopForm, close) {
        if(shopForm.$valid) {
            $scope.shopDetailSave($scope.shopDetail).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if(typeof $scope.shopDetail.id === "undefined") {
                    $scope.shopDetailReset();
                    $scope.shopsFetch(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    }

    function close() {
        $scope.shopDetailReset();
        $scope.shopsFetch(
            searchString
        );
        $mdDialog.cancel();
    }

    function onToggleCategory(categories) {
        $scope.shopDetailAssignCategories(categories);
    }

    function onToggleLanguage(languages) {
        $scope.shopDetailAssignLanguages(languages);
    }


    function addCustomProperty(key, value, translatable) {
        $scope.addShopCustomProperty(shopId, key, value, translatable).then(() => {
            $scope.fetchShopCustomProperties(shopId);
        });
    }

    function removeCustomProperty(id) {
        $scope.removeShopCustomProperty(shopId, id).then(() => {
            $scope.fetchShopCustomProperties(shopId);
        });
    }

    init();

    $scope.save = save;
    $scope.close = close;
    $scope.onToggleCategory = onToggleCategory;
    $scope.onToggleLanguage = onToggleLanguage;
    $scope.addCustomProperty = addCustomProperty;
    $scope.removeCustomProperty = removeCustomProperty;

    $scope.$on('$destroy', subscribedActions);
};

ShopDetailController.$inject = ShopDetailControllerInject;

export default ShopDetailController;
