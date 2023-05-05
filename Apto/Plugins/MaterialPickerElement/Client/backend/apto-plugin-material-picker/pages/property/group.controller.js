import GroupDetailTemplate from './group-detail.controller.html';
import GroupDetailController from './group-detail.controller';

const GroupControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'IndexActions', 'MaterialPickerPropertyActions', 'LanguageFactory'];
const GroupController = function($scope, $mdDialog, $ngRedux, IndexActions, MaterialPickerPropertyActions, LanguageFactory) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.pluginMaterialPickerProperty.pageHeaderConfig,
            dataListConfig: state.pluginMaterialPickerProperty.dataListConfig,
            groups: state.pluginMaterialPickerProperty.groups
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        setPageNumber: MaterialPickerPropertyActions.setPageNumber,
        setSearchString: MaterialPickerPropertyActions.setSearchString,
        fetchGroupsByPage: MaterialPickerPropertyActions.fetchGroupsByPage,
        removeGroup: MaterialPickerPropertyActions.removeGroup
    })($scope);

    function init() {
        fetchGroupsByPage();
    }

    function fetchGroupsByPage() {
        $scope.fetchGroupsByPage(
            $scope.pageHeaderConfig.pagination.pageNumber,
            $scope.pageHeaderConfig.pagination.recordsPerPage,
            $scope.pageHeaderConfig.search.searchString
        );
    }

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: GroupDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                groupId: id
            },
            controller: GroupDetailController
        }).then(fetchGroupsByPage, fetchGroupsByPage);
    }

    $scope.pageHeaderActions = {
        pageChanged: {
            fnc: function (page) {
                $scope.setPageNumber(page);
                fetchGroupsByPage();
            }
        },
        search: {
            fnc: function (searchString) {
                $scope.setSearchString(searchString);
                fetchGroupsByPage();
            }
        },
        add: {
            fnc: showDetailsDialog
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
                $scope.removeGroup(id).then(fetchGroupsByPage);
            }
        }
    };

    init();

    $scope.$on('$destroy', subscribedActions);
    $scope.translate = LanguageFactory.translate;
};

GroupController.$inject = GroupControllerInject;

export default ['MaterialPickerGroupController', GroupController];
