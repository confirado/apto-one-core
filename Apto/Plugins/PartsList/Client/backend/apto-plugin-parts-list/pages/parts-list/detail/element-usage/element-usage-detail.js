const ElementUsageDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'AptoPartsListPartActions', 'partId', 'elementUsageId'];
const ElementUsageDetailController = function($scope, $mdDialog, $ngRedux, AptoPartsListPartActions, partId, elementUsageId) {

    $scope.mapStateToThis = function(state) {
        return {
            elementUsageDetails: state.aptoPartsListPart.elementUsageDetails
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchElementUsageDetails: AptoPartsListPartActions.fetchElementUsageDetails,
        updateElementUsage: AptoPartsListPartActions.updateElementUsage,
        resetElementUsageDetails: AptoPartsListPartActions.resetElementUsageDetails,
        fetchElementUsages: AptoPartsListPartActions.fetchElementUsages
    })($scope);

    function init() {
        $scope.fetchElementUsageDetails(elementUsageId);
    }

    function onChangeFieldType() {
        $scope.elementUsageDetails.quantityCalculation.field = null;
    }

    function save() {
        $scope.updateElementUsage(partId, elementUsageId, $scope.elementUsageDetails.quantity, $scope.elementUsageDetails.quantityCalculation);
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