import UserRoleDetailTemplate from './user-role-detail.controller.html';
import UserRoleDetailController from './user-role-detail.controller';

const UserRoleControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'UserRoleActions', 'IndexActions'];
const UserRoleController = function($scope, $mdDialog, $ngRedux, UserRoleActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.userRole.pageHeaderConfig,
            dataListConfig: state.userRole.dataListConfig,
            userRoles: state.userRole.userRoles
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: UserRoleActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        userRolesFetch: UserRoleActions.userRolesFetch,
        userRoleDetailFetch: UserRoleActions.userRoleDetailFetch,
        userRoleRemove: UserRoleActions.userRoleRemove,
        availableChildrenFetch: UserRoleActions.availableChildrenFetch
    })($scope);

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: $scope.userRolesFetch
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
                $scope.userRoleRemove(id).then(function () {
                    $scope.userRolesFetch(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.userRolesFetch(
        $scope.pageHeaderConfig.search.searchString
    );

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);
        if(typeof id !== "undefined") {
            $scope.userRoleDetailFetch(id);
        }
        $scope.availableChildrenFetch();
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: UserRoleDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString
            },
            controller: UserRoleDetailController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

UserRoleController.$inject = UserRoleControllerInject;

export default ['UserRoleController', UserRoleController];