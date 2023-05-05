import FilterCategoryDetailTemplate from './filter-category-detail.controller.html';
import FilterCategoryDetailController from './filter-category-detail.controller';

const FilterCategoryControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'FilterCategoryActions', 'IndexActions'];
const FilterCategoryController = function($scope, $mdDialog, $ngRedux, FilterCategoryActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.filterCategory.pageHeaderConfig,
            dataListConfig: state.filterCategory.dataListConfig,
            filterCategories: state.filterCategory.filterCategories
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: FilterCategoryActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        fetchFilterCategories: FilterCategoryActions.fetchFilterCategories,
        fetchFilterCategoryDetail: FilterCategoryActions.fetchFilterCategoryDetail,
        removeFilterCategory: FilterCategoryActions.removeFilterCategory
    })($scope);

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: $scope.fetchFilterCategories
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
                $scope.removeFilterCategory(id).then(function () {
                    $scope.fetchFilterCategories(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.fetchFilterCategories(
        $scope.pageHeaderConfig.search.searchString
    );

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);
        if(typeof id !== "undefined") {
            $scope.fetchFilterCategoryDetail(id);
        }
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: FilterCategoryDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString
            },
            controller: FilterCategoryDetailController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

FilterCategoryController.$inject = FilterCategoryControllerInject;

export default ['FilterCategoryController', FilterCategoryController];