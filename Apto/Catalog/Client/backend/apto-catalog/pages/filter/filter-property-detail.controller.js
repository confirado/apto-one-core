import CategoryTab from './category-tab.html';
import FilterPropertyTab from './filter-property-tab.html';

const FilterPropertyDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', '$templateCache', 'FilterPropertyActions', 'targetEvent', 'showDetailsDialog', 'searchString'];
const FilterPropertyDetailController = function($scope, $mdDialog, $ngRedux, $templateCache, FilterPropertyActions, targetEvent, showDetailsDialog, searchString) {

    $templateCache.put('catalog/pages/filter/category-tab.html', CategoryTab);
    $templateCache.put('catalog/pages/filter/filter-property-tab.html', FilterPropertyTab);


    $scope.mapStateToThis = function(state) {
        return {
            filterPropertyDetail: state.filterProperty.filterPropertyDetail,
            filterCategories: state.filterCategory.filterCategories
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchFilterProperties: FilterPropertyActions.fetchFilterProperties,
        fetchFilterPropertyDetail: FilterPropertyActions.fetchFilterPropertyDetail,
        saveFilterProperty: FilterPropertyActions.saveFilterProperty,
        filterPropertyDetailReset: FilterPropertyActions.filterPropertyDetailReset,
        filterPropertyDetailAssignCategories: FilterPropertyActions.filterPropertyDetailAssignCategories
    })($scope);

    $scope.save = function (filterPropertyForm, close) {
        if(filterPropertyForm.$valid) {
            $scope.saveFilterProperty($scope.filterPropertyDetail).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if(typeof $scope.filterPropertyDetail.id === "undefined") {
                    $scope.filterPropertyDetailReset();
                    $scope.fetchFilterProperties(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    $scope.onToggleCategory = function (categories) {
        $scope.filterPropertyDetailAssignCategories(categories);
    }

    $scope.close = function () {
        $scope.filterPropertyDetailReset();
        $scope.fetchFilterProperties(
            searchString
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

FilterPropertyDetailController.$inject = FilterPropertyDetailControllerInject;

export default FilterPropertyDetailController;