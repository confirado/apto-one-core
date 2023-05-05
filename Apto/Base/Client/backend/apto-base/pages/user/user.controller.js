import UserDetailTemplate from './user-detail.controller.html';
import UserDetailController from './user-detail.controller';

const UserControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'UserActions', 'IndexActions'];
const UserController = function($scope, $mdDialog, $ngRedux, UserActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.user.pageHeaderConfig,
            dataListConfig: state.user.dataListConfig,
            users: state.user.users
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: UserActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        usersFetch: UserActions.usersFetch,
        userDetailFetch: UserActions.userDetailFetch,
        userRemove: UserActions.userRemove,
        availableUserRolesFetch: UserActions.availableUserRolesFetch,
    })($scope);

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: $scope.usersFetch
        },
        listStyle: {
            fnc: $scope.setListTemplate
        },
        toggleSideBarRight: {
            fnc: $scope.toggleSidebarRight
        }
    };

    $scope.dataListActions = {
        edit: {
            fnc: showDetailsDialog
        },
        remove: {
            fnc: function ($event, id) {
                $scope.userRemove(id).then(function () {
                    $scope.usersFetch(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.usersFetch(
        $scope.pageHeaderConfig.search.searchString
    );

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);
        if(typeof id !== "undefined") {
            $scope.userDetailFetch(id);
        }
        $scope.availableUserRolesFetch();
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: UserDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString
            },
            controller: UserDetailController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

UserController.$inject = UserControllerInject;

export default ['UserController', UserController];