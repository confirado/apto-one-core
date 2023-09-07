import FrontendUserDetailTemplate from './frontend-user-detail.controller.html';
import FrontendUserDetailController from './frontend-user-detail.controller';

const FrontendUserControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'FrontendUserActions', 'IndexActions'];
const FrontendUserController = function($scope, $mdDialog, $ngRedux, FrontendUserActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.frontendUser.pageHeaderConfig,
            dataListConfig: state.frontendUser.dataListConfig,
            frontendUsers: state.frontendUser.frontendUsers,
            availableCustomerGroups: state.frontendUser.availableCustomerGroups
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: FrontendUserActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        frontendUsersFetch: FrontendUserActions.frontendUsersFetch,
        frontendUserDetailFetch: FrontendUserActions.frontendUserDetailFetch,
        frontendUserRemove: FrontendUserActions.frontendUserRemove,
        availableCustomerGroupsFetch: FrontendUserActions.availableCustomerGroupsFetch
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
                $scope.frontendUserRemove(id).then(function () {
                    $scope.frontendUsersFetch(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.frontendUsersFetch(
        $scope.pageHeaderConfig.search.searchString
    );

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);

        $scope.availableCustomerGroupsFetch();
        if(typeof id !== "undefined") {
            $scope.frontendUserDetailFetch(id);
        }

        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: FrontendUserDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString
            },
            controller: FrontendUserDetailController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

FrontendUserController.$inject = FrontendUserControllerInject;

export default ['FrontendUserController', FrontendUserController];
