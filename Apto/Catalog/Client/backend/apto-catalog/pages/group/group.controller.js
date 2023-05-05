import GroupDetailTemplate from './group-detail.controller.html';
import GroupDetailController from './group-detail.controller';

const GroupControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'GroupActions', 'IndexActions'];
const GroupController = function($scope, $mdDialog, $ngRedux, GroupActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.group.pageHeaderConfig,
            dataListConfig: state.group.dataListConfig,
            groups: state.group.groups
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: GroupActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        fetchGroups: GroupActions.fetchGroups,
        fetchGroupDetail: GroupActions.fetchGroupDetail,
        removeGroup: GroupActions.removeGroup
    })($scope);

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: $scope.fetchGroups
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
                $scope.removeGroup(id).then(function () {
                    $scope.fetchGroups(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.fetchGroups(
        $scope.pageHeaderConfig.search.searchString
    );

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);
        if(typeof id !== "undefined") {
            $scope.fetchGroupDetail(id);
        }
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: GroupDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString
            },
            controller: GroupDetailController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

GroupController.$inject = GroupControllerInject;

export default ['GroupController', GroupController];