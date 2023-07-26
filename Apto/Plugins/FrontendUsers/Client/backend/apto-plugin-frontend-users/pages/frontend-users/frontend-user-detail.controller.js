import FrontendUserTab from './frontend-user-tab.html';

const FrontendUserDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', '$templateCache', 'FrontendUserActions', 'targetEvent', 'showDetailsDialog', 'searchString'];
const FrontendUserDetailController = function($scope, $mdDialog, $ngRedux, $templateCache, FrontendUserActions, targetEvent, showDetailsDialog, searchString) {
    $templateCache.put('plugin/frontend-users/pages/frontend-user/frontend-user-tab.html', FrontendUserTab);

    $scope.mapStateToThis = function(state) {
        return {
            frontendUserDetail: state.frontendUser.frontendUserDetail,
            availableCustomerGroups: state.frontendUser.availableCustomerGroups
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        frontendUsersFetch: FrontendUserActions.frontendUsersFetch,
        frontendUserDetailFetch: FrontendUserActions.frontendUserDetailFetch,
        frontendUserDetailSave: FrontendUserActions.frontendUserDetailSave,
        frontendUserDetailReset: FrontendUserActions.frontendUserDetailReset
    })($scope);

    $scope.save = function (frontendUserForm, close) {
        if(frontendUserForm.$valid) {
            $scope.frontendUserDetailSave($scope.frontendUserDetail).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if(typeof $scope.frontendUserDetail.id === "undefined") {
                    $scope.frontendUserDetailReset();
                    $scope.frontendUsersFetch(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    $scope.close = function () {
        $scope.frontendUserDetailReset();
        $scope.frontendUsersFetch(
            searchString
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

FrontendUserDetailController.$inject = FrontendUserDetailControllerInject;

export default FrontendUserDetailController;
