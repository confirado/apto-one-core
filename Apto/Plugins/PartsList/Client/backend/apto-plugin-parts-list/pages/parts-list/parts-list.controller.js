import DetailTemplate from './detail/detail.controller.html';
import DetailController from './detail/detail.controller';

const ControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'AptoPartsListPartActions', 'IndexActions'];
const Controller = function($scope, $mdDialog, $ngRedux, AptoPartsListPartActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.aptoPartsListPart.pageHeaderConfig,
            dataListConfig: state.aptoPartsListPart.dataListConfig,
            list: state.aptoPartsListPart.list
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        setListTemplate: AptoPartsListPartActions.setListTemplate,
        fetchList: AptoPartsListPartActions.fetchList,
        fetchDetails: AptoPartsListPartActions.fetchDetails,
        removeDetails: AptoPartsListPartActions.removeDetails,
        availableCustomerGroupsFetch: AptoPartsListPartActions.availableCustomerGroupsFetch,
        categoriesFetch: AptoPartsListPartActions.categoriesFetch,
    })($scope);

    $scope.pageHeaderActions = {
        pageChanged: {
            fnc: function (page) {
                $scope.fetchList(
                    page,
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
                $scope.fetchList(
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
                $scope.removeDetails(id).then(function () {
                    $scope.fetchList(
                        $scope.pageHeaderConfig.pagination.pageNumber,
                        $scope.pageHeaderConfig.pagination.recordsPerPage,
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.fetchList(
        $scope.pageHeaderConfig.pagination.pageNumber,
        $scope.pageHeaderConfig.pagination.recordsPerPage,
        $scope.pageHeaderConfig.search.searchString
    );

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);
        $scope.availableCustomerGroupsFetch();
        $scope.categoriesFetch();
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            clickOutsideToClose: true,
            fullscreen: true,
            multiple: true,
            locals: {
                id: id,
                onClose: function(reopen) {
                    $scope.fetchList(
                        $scope.pageHeaderConfig.pagination.pageNumber,
                        $scope.pageHeaderConfig.pagination.recordsPerPage,
                        $scope.pageHeaderConfig.search.searchString
                    );
                    if (reopen) {
                        showDetailsDialog($event, id);
                    }
                }
            },
            template: DetailTemplate,
            controller: DetailController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

Controller.$inject = ControllerInject;

export default ['AptoPartsListPartController', Controller];
