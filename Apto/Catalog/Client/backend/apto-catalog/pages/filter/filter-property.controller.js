import FilterPropertyDetailTemplate from './filter-property-detail.controller.html';
import FilterPropertyDetailController from './filter-property-detail.controller';

const FilterPropertyControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'FilterPropertyActions', 'IndexActions', 'FilterCategoryActions'];
const FilterPropertyController = function($scope, $mdDialog, $ngRedux, FilterPropertyActions, IndexActions, FilterCategoryActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.filterProperty.pageHeaderConfig,
            dataListConfig: state.filterProperty.dataListConfig,
            filterProperties: state.filterProperty.filterProperties
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: FilterPropertyActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        fetchFilterProperties: FilterPropertyActions.fetchFilterProperties,
        fetchFilterPropertyDetail: FilterPropertyActions.fetchFilterPropertyDetail,
        removeFilterProperty: FilterPropertyActions.removeFilterProperty,
        fetchFilterCategories: FilterCategoryActions.fetchFilterCategories
    })($scope);

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: $scope.fetchFilterProperties
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
                $scope.removeFilterProperty(id).then(function () {
                    $scope.fetchFilterProperties(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.fetchFilterProperties(
        $scope.pageHeaderConfig.search.searchString
    );

    function showDetailsDialog($event, id) {
        $scope.fetchFilterCategories('');
        const parentEl = angular.element(document.body);
        if(typeof id !== "undefined") {
            $scope.fetchFilterPropertyDetail(id);
        }
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: FilterPropertyDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString
            },
            controller: FilterPropertyDetailController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

FilterPropertyController.$inject = FilterPropertyControllerInject;

export default ['FilterPropertyController', FilterPropertyController];