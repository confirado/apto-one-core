import PoolDetailTemplate from './pool-detail.controller.html';
import PoolDetailController from './pool-detail.controller';

const PoolControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'IndexActions', 'MaterialPickerPoolActions'];
const PoolController = function($scope, $mdDialog, $ngRedux, IndexActions, MaterialPickerPoolActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.pluginMaterialPickerPool.pageHeaderConfig,
            dataListConfig: state.pluginMaterialPickerPool.dataListConfig,
            pools: state.pluginMaterialPickerPool.pools
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        setPageNumber: MaterialPickerPoolActions.setPageNumber,
        setSearchString: MaterialPickerPoolActions.setSearchString,
        fetchPoolsByPage: MaterialPickerPoolActions.fetchPoolsByPage,
        copyPool: MaterialPickerPoolActions.copyPool,
        removePool: MaterialPickerPoolActions.removePool
    })($scope);

    function init() {
        fetchPoolsByPage();
    }

    function fetchPoolsByPage() {
        $scope.fetchPoolsByPage(
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
            template: PoolDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                poolId: id
            },
            controller: PoolDetailController
        }).then(fetchPoolsByPage, fetchPoolsByPage);
    }

    $scope.pageHeaderActions = {
        pageChanged: {
            fnc: function (page) {
                $scope.setPageNumber(page);
                fetchPoolsByPage();
            }
        },
        search: {
            fnc: function (searchString) {
                $scope.setSearchString(searchString);
                fetchPoolsByPage();
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
                $scope.removePool(id).then(fetchPoolsByPage);
            }
        },
        copy: {
            fnc: function ($event, id) {
                $scope.copyPool(id).then(fetchPoolsByPage);
            }
        }
    };

    init();

    $scope.$on('$destroy', subscribedActions);
};

PoolController.$inject = PoolControllerInject;

export default ['MaterialPickerPoolController', PoolController];