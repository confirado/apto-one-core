import ElementPriceTab from './element-price-tab.html';
import CustomPropertiesTab from './custom-properties-tab.html';

const ElementPriceDialogInject = ['$scope', '$templateCache', '$ngRedux', '$mdDialog', 'ProductActions', 'PriceMatrixActions', 'priceMatrixId', 'element'];
const ElementPriceDialog = function($scope, $templateCache, $ngRedux, $mdDialog, ProductActions, PriceMatrixActions, priceMatrixId, element) {
    $templateCache.put('catalog/pages/price-matrix/element-price-tab.html', ElementPriceTab);
    $templateCache.put('catalog/pages/price-matrix/custom-properties-tab.html', CustomPropertiesTab);

    $scope.mapStateToThis = function(state) {
        return {
            availableCustomerGroups: state.product.availableCustomerGroups,
            elementPrices: state.priceMatrix.elementPrices,
            elementCustomProperties: state.priceMatrix.elementCustomProperties
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        availableCustomerGroupsFetch: ProductActions.availableCustomerGroupsFetch,
        fetchPriceMatrixElementPrices: PriceMatrixActions.fetchPriceMatrixElementPrices,
        addPriceMatrixElementPrice: PriceMatrixActions.addPriceMatrixElementPrice,
        removePriceMatrixElementPrice: PriceMatrixActions.removePriceMatrixElementPrice,
        fetchPriceMatrixElementCustomProperties: PriceMatrixActions.fetchPriceMatrixElementCustomProperties,
        addPriceMatrixElementCustomProperty: PriceMatrixActions.addPriceMatrixElementCustomProperty,
        removePriceMatrixElementCustomProperty: PriceMatrixActions.removePriceMatrixElementCustomProperty
    })($scope);

    function init() {
        $scope.availableCustomerGroupsFetch();
        $scope.fetchPriceMatrixElementPrices(priceMatrixId, element.cellId);
        $scope.fetchPriceMatrixElementCustomProperties(priceMatrixId, element.cellId);
        $scope.element = element;
        initNewPrice();
    }

    function initNewPrice() {
        $scope.newPrice = {
            amount: '',
            currencyCode: 'EUR',
            customerGroupId: ''
        };
    }

    function addPrice() {
        $scope.addPriceMatrixElementPrice(priceMatrixId, element.cellId, $scope.newPrice.amount, $scope.newPrice.currencyCode, $scope.newPrice.customerGroupId).then(() => {
            initNewPrice();
            $scope.fetchPriceMatrixElementPrices(priceMatrixId, element.cellId);
        });
    }

    function removePrice(priceId) {
        $scope.removePriceMatrixElementPrice(priceMatrixId, element.cellId, priceId).then(() => {
            $scope.fetchPriceMatrixElementPrices(priceMatrixId, element.cellId);
        });
    }

    function addCustomProperty(key, value, translatable) {
        $scope.addPriceMatrixElementCustomProperty(priceMatrixId, element.cellId, key, value, translatable).then(() => {
            $scope.fetchPriceMatrixElementCustomProperties(priceMatrixId, element.cellId);
        });
    }

    function removeCustomProperty(id) {
        $scope.removePriceMatrixElementCustomProperty(priceMatrixId, element.cellId, id).then(() => {
            $scope.fetchPriceMatrixElementCustomProperties(priceMatrixId, element.cellId);
        });
    }

    function close() {
        $mdDialog.cancel();
    }

    init();
    $scope.close = close;
    $scope.addPrice = addPrice;
    $scope.removePrice = removePrice;
    $scope.addCustomProperty = addCustomProperty;
    $scope.removeCustomProperty = removeCustomProperty;
    $scope.$on('$destroy', subscribedActions);
};

ElementPriceDialog.$inject = ElementPriceDialogInject;

export default ElementPriceDialog;
