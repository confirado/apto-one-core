const FilterCategoryDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'FilterCategoryActions', 'targetEvent', 'showDetailsDialog', 'searchString'];
const FilterCategoryDetailController = function($scope, $mdDialog, $ngRedux, FilterCategoryActions, targetEvent, showDetailsDialog, searchString) {

    $scope.mapStateToThis = function(state) {
        return {
            filterCategoryDetail: state.filterCategory.filterCategoryDetail
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchFilterCategories: FilterCategoryActions.fetchFilterCategories,
        fetchFilterCategoryDetail: FilterCategoryActions.fetchFilterCategoryDetail,
        saveFilterCategory: FilterCategoryActions.saveFilterCategory,
        filterCategoryDetailReset: FilterCategoryActions.filterCategoryDetailReset
    })($scope);

    $scope.save = function (filterCategoryForm, close) {
        if(filterCategoryForm.$valid) {
            $scope.saveFilterCategory($scope.filterCategoryDetail).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if(typeof $scope.filterCategoryDetail.id === "undefined") {
                    $scope.filterCategoryDetailReset();
                    $scope.fetchFilterCategories(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    $scope.close = function () {
        $scope.filterCategoryDetailReset();
        $scope.fetchFilterCategories(
            searchString
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

FilterCategoryDetailController.$inject = FilterCategoryDetailControllerInject;

export default FilterCategoryDetailController;