import DetailTemplate from './canvas-detail.controller.html';
import DetailController from './canvas-detail.controller';

const ControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'IndexActions', 'ImageUploadCanvasActions'];
const Controller = function($scope, $mdDialog, $ngRedux, IndexActions, ImageUploadCanvasActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.pluginImageUploadCanvas.pageHeaderConfig,
            dataListConfig: state.pluginImageUploadCanvas.dataListConfig,
            list: state.pluginImageUploadCanvas.list
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        fetchCanvasList: ImageUploadCanvasActions.fetchCanvasList,
        removeCanvas: ImageUploadCanvasActions.removeCanvas,
        setPageNumber: ImageUploadCanvasActions.setPageNumber,
        setSearchString: ImageUploadCanvasActions.setSearchString
    })($scope);

    function init() {
        fetchCanvasList();
    }

    function fetchCanvasList() {
        $scope.fetchCanvasList(
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
            template: DetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                canvasId: id
            },
            controller: DetailController
        }).then(fetchCanvasList, fetchCanvasList);
    }

    $scope.pageHeaderActions = {
        pageChanged: {
            fnc: function (page) {
                $scope.setPageNumber(page);
                fetchCanvasList();
            }
        },
        search: {
            fnc: function (searchString) {
                $scope.setSearchString(searchString);
                fetchCanvasList();
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
                $scope.removeCanvas(id).then(fetchCanvasList);
            }
        }
    };

    init();

    $scope.$on('$destroy', subscribedActions);
};

Controller.$inject = ControllerInject;

export default ['ImageUploadCanvasController', Controller];
