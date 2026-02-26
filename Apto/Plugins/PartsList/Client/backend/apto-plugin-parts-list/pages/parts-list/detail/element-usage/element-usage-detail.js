const ElementUsageDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'AptoPartsListPartActions', 'ElementActions', 'partId', 'elementUsageId'];
const ElementUsageDetailController = function($scope, $mdDialog, $ngRedux, AptoPartsListPartActions, ElementActions, partId, elementUsageId) {

    $scope.mapStateToThis = function(state) {
        return {
            elementUsageDetails: state.aptoPartsListPart.elementUsageDetails,
            selectableValues: state.element.selectableValues,
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchElementUsageDetails: AptoPartsListPartActions.fetchElementUsageDetails,
        updateElementUsage: AptoPartsListPartActions.updateElementUsage,
        resetElementUsageDetails: AptoPartsListPartActions.resetElementUsageDetails,
        fetchElementUsages: AptoPartsListPartActions.fetchElementUsages,
        fetchSelectableValues: ElementActions.fetchSelectableValues,
    })($scope);

    function init() {
        $scope.fetchElementUsageDetails(elementUsageId).then(() => {
            $scope.selectableProperties = [];
            createElementSelectableValues();
        });
    }

    function createElementSelectableValues() {
        $scope.fetchSelectableValues($scope.elementUsageDetails.element.id).then(() => {
            const selectableValue = $scope.selectableValues;
            const keys = Object.keys(selectableValue);
            $scope.selectableProperties = keys;
        });
    }

    function onChangeFieldType() {
        $scope.elementUsageDetails.quantityCalculation.field = null;
    }

    function save() {
        $scope.updateElementUsage(partId, elementUsageId, $scope.elementUsageDetails.quantity, $scope.elementUsageDetails.value, $scope.elementUsageDetails.quantityCalculation);
    }

    function close() {
        $scope.fetchElementUsages(partId);
        $scope.resetElementUsageDetails();
        $mdDialog.cancel();
    }

    init();

    $scope.save = save;
    $scope.close = close;
    $scope.onChangeFieldType = onChangeFieldType;
    $scope.$on('$destroy', subscribedActions);
};

ElementUsageDetailController.$inject = ElementUsageDetailControllerInject;

export default ElementUsageDetailController;
