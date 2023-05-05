const GroupDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'GroupActions', 'targetEvent', 'showDetailsDialog', 'searchString'];
const GroupDetailController = function($scope, $mdDialog, $ngRedux, GroupActions, targetEvent, showDetailsDialog, searchString) {

    $scope.mapStateToThis = function(state) {
        return {
            groupDetail: state.group.groupDetail
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchGroups: GroupActions.fetchGroups,
        fetchGroupDetail: GroupActions.fetchGroupDetail,
        saveGroup: GroupActions.saveGroup,
        groupDetailReset: GroupActions.groupDetailReset
    })($scope);

    $scope.save = function (groupForm, close) {
        if(groupForm.$valid) {
            $scope.saveGroup($scope.groupDetail).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if(typeof $scope.groupDetail.id === "undefined") {
                    $scope.groupDetailReset();
                    $scope.fetchGroups(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    $scope.close = function () {
        $scope.groupDetailReset();
        $scope.fetchGroups(
            searchString
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

GroupDetailController.$inject = GroupDetailControllerInject;

export default GroupDetailController;