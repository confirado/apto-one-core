import PriceMatrixDetailTemplate from './price-matrix-detail.controller.html';
import PriceMatrixDetailController from './price-matrix-detail.controller';

const PriceMatrixControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'PriceMatrixActions', 'IndexActions'];
const PriceMatrixController = function($scope, $mdDialog, $ngRedux, PriceMatrixActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.priceMatrix.pageHeaderConfig,
            dataListConfig: state.priceMatrix.dataListConfig,
            priceMatrices: state.priceMatrix.priceMatrices
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: PriceMatrixActions.setListTemplate,
        setPageNumber: PriceMatrixActions.setPageNumber,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        fetchPriceMatrices: PriceMatrixActions.fetchPriceMatrices,
        removePriceMatrix: PriceMatrixActions.removePriceMatrix
    })($scope);

    $scope.pageHeaderActions = {
        pageChanged: {
            fnc: function (page) {
                $scope.setPageNumber(page);
                $scope.fetchPriceMatrices(
                    $scope.pageHeaderConfig.pagination.pageNumber,
                    $scope.pageHeaderConfig.pagination.recordsPerPage,
                    $scope.pageHeaderConfig.search.searchString
                );
            }
        },
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: function (searchString) {
                $scope.fetchPriceMatrices(
                    $scope.pageHeaderConfig.pagination.pageNumber,
                    $scope.pageHeaderConfig.pagination.recordsPerPage,
                    searchString
                );
            }
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
                $scope.removePriceMatrix(id).then(fetchPriceMatrices);
            }
        }
    };

    function init() {
        fetchPriceMatrices();
    }

    function fetchPriceMatrices() {
        $scope.fetchPriceMatrices(
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
            template: PriceMatrixDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                priceMatrixId: id
            },
            controller: PriceMatrixDetailController
        }).then(fetchPriceMatrices, fetchPriceMatrices);
    }

    init();

    $scope.$on('$destroy', subscribedActions);
};

PriceMatrixController.$inject = PriceMatrixControllerInject;

export default ['PriceMatrixController', PriceMatrixController];