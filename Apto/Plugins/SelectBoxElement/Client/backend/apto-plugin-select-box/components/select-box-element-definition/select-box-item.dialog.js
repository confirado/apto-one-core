const SelectBoxItemDialogInject = ['$scope', '$templateCache', '$ngRedux', '$mdDialog', 'LanguageFactory', 'ProductActions', 'SelectBoxDefinitionActions', 'productId', 'sectionId', 'element', 'selectBoxItemId'];
const SelectBoxItemDialog = function($scope, $templateCache, $ngRedux, $mdDialog, LanguageFactory, ProductActions, SelectBoxDefinitionActions, productId, sectionId, element, selectBoxItemId) {
    $scope.mapStateToThis = function(state) {
        return {
            availableCustomerGroups: state.product.availableCustomerGroups,
            selectBoxItemDetail: state.selectBoxDefinition.selectBoxItemDetail,
            selectBoxItemPrices: state.selectBoxDefinition.selectBoxItemPrices
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        availableCustomerGroupsFetch: ProductActions.availableCustomerGroupsFetch,
        fetchSelectBoxItemDetail: SelectBoxDefinitionActions.fetchSelectBoxItemDetail,
        saveSelectBoxItemDetail: SelectBoxDefinitionActions.saveSelectBoxItemDetail,
        fetchSelectBoxItemPrices: SelectBoxDefinitionActions.fetchSelectBoxItemPrices,
        addSelectBoxItemPrice: SelectBoxDefinitionActions.addSelectBoxItemPrice,
        removeSelectBoxItemPrice: SelectBoxDefinitionActions.removeSelectBoxItemPrice,
    })($scope);

    function init() {
        $scope.availableCustomerGroupsFetch();
        $scope.fetchSelectBoxItemDetail(selectBoxItemId);
        $scope.fetchSelectBoxItemPrices(selectBoxItemId);
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
        $scope.addSelectBoxItemPrice(selectBoxItemId, $scope.newPrice.amount, $scope.newPrice.currencyCode, $scope.newPrice.customerGroupId).then(() => {
            initNewPrice();
            $scope.fetchSelectBoxItemPrices(selectBoxItemId);
        });
    }

    function removePrice(priceId) {
        $scope.removeSelectBoxItemPrice(selectBoxItemId, priceId).then(() => {
            $scope.fetchSelectBoxItemPrices(selectBoxItemId);
        });
    }

    function save(selectBoxForm) {
        if(selectBoxForm.$valid) {
            $scope.saveSelectBoxItemDetail({
                productId: productId,
                sectionId: sectionId,
                elementId: element.id,
                id: selectBoxItemId,
                name: $scope.selectBoxItemDetail.name
            }).then(() => {
                close();
            });
        }
    }

    function close() {
        $mdDialog.cancel();
    }

    init();
    $scope.save = save;
    $scope.close = close;
    $scope.addPrice = addPrice;
    $scope.removePrice = removePrice;
    $scope.translate = LanguageFactory.translate;
    $scope.$on('$destroy', subscribedActions);
};

SelectBoxItemDialog.$inject = SelectBoxItemDialogInject;

export default SelectBoxItemDialog;