const ControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'CustomerGroupActions', 'targetEvent', 'showDetailsDialog', 'searchString'];
const Controller = function($scope, $mdDialog, $ngRedux, CustomerGroupActions, targetEvent, showDetailsDialog, searchString) {
    $scope.mapStateToThis = function(state) {
        return {
            details: state.customerGroup.details
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        customerGroupsFetch: CustomerGroupActions.customerGroupsFetch,
        fetchDetails: CustomerGroupActions.fetchDetails,
        saveDetails: CustomerGroupActions.saveDetails,
        resetDetails: CustomerGroupActions.resetDetails
    })($scope);

    $scope.save = function (customerGroupForm, close) {
        if(customerGroupForm.$valid) {
            $scope.saveDetails($scope.details).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if (!$scope.details.id) {
                    $scope.resetDetails();
                    $scope.customerGroupsFetch(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    $scope.close = function () {
        $scope.resetDetails();
        $scope.customerGroupsFetch(
            searchString
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

Controller.$inject = ControllerInject;

export default Controller;