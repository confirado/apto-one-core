import UserTab from './user-tab.html';
import UserRolesTab from './user-roles-tab.html';
import UserSettingsTab from './user-settings-tab.html';

const UserDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', '$templateCache', 'UserActions', 'targetEvent', 'showDetailsDialog', 'searchString'];
const UserDetailController = function($scope, $mdDialog, $ngRedux, $templateCache, UserActions, targetEvent, showDetailsDialog, searchString) {
    $templateCache.put('base/pages/user/user-tab.html', UserTab);
    $templateCache.put('base/pages/user/user-roles-tab.html', UserRolesTab);
    $templateCache.put('base/pages/user/user-settings-tab.html', UserSettingsTab);

    $scope.mapStateToThis = function(state) {
        return {
            currentUser: state.index.currentUser,
            userDetail: state.user.userDetail,
            availableUserRoles: state.user.availableUserRoles
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        usersFetch: UserActions.usersFetch,
        userDetailFetch: UserActions.userDetailFetch,
        userDetailSave: UserActions.userDetailSave,
        userDetailReset: UserActions.userDetailReset,
        userDetailAssignUserRoles: UserActions.userDetailAssignUserRoles,
    })($scope);

    function onToggleUserRole(userRoles) {
        $scope.userDetailAssignUserRoles(userRoles);
    }

    $scope.onToggleUserRole = onToggleUserRole;

    $scope.save = function (userForm, close) {
        if(userForm.$valid) {
            $scope.userDetailSave($scope.userDetail).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if(typeof $scope.userDetail.id === "undefined") {
                    $scope.userDetailReset();
                    $scope.usersFetch(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    $scope.close = function () {
        $scope.userDetailReset();
        $scope.usersFetch(
            searchString
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

UserDetailController.$inject = UserDetailControllerInject;

export default UserDetailController;