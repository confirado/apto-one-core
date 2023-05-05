import UserRoleTab from './user-role-tab.html';
import ChildrenTab from './children-tab.html';

const UserRoleDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', '$templateCache', 'UserRoleActions', 'targetEvent', 'showDetailsDialog', 'searchString'];
const UserRoleDetailController = function($scope, $mdDialog, $ngRedux, $templateCache, UserRoleActions, targetEvent, showDetailsDialog, searchString) {
    $templateCache.put('base/pages/user-role/user-role-tab.html', UserRoleTab);
    $templateCache.put('base/pages/user-role/children-tab.html', ChildrenTab);

    $scope.mapStateToThis = function(state) {
        return {
            userRoleDetail: state.userRole.userRoleDetail,
            availableChildren: state.userRole.availableChildren
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        userRolesFetch: UserRoleActions.userRolesFetch,
        userRoleDetailFetch: UserRoleActions.userRoleDetailFetch,
        userRoleDetailSave: UserRoleActions.userRoleDetailSave,
        userRoleDetailReset: UserRoleActions.userRoleDetailReset,
        userRoleDetailAssignChildren: UserRoleActions.userRoleDetailAssignChildren
    })($scope);

    function onToggleChild(children) {
        $scope.userRoleDetailAssignChildren(children);
    }

    $scope.onToggleChild = onToggleChild;

    $scope.save = function (userRoleForm, close) {
        if(userRoleForm.$valid) {
            $scope.userRoleDetailSave($scope.userRoleDetail).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if(typeof $scope.userRoleDetail.id === "undefined") {
                    $scope.userRoleDetailReset();
                    $scope.userRolesFetch(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    $scope.close = function () {
        $scope.userRoleDetailReset();
        $scope.userRolesFetch(
            searchString
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

UserRoleDetailController.$inject = UserRoleDetailControllerInject;

export default UserRoleDetailController;