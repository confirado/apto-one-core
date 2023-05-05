import CategoryDetailTemplate from './category-detail.controller.html';
import CategoryDetailController from './category-detail.controller';

const CategoryControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'CategoryActions', 'IndexActions'];
const CategoryController = function($scope, $mdDialog, $ngRedux, CategoryActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.category.pageHeaderConfig,
            dataListConfig: state.category.dataListConfig,
            categoryTree: state.category.categoryTree
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: CategoryActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        categoryTreeFetch: CategoryActions.categoryTreeFetch,
        categoryDetailFetch: CategoryActions.categoryDetailFetch,
        categoryRemove: CategoryActions.categoryRemove
    })($scope);

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: $scope.categoryTreeFetch
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
                $scope.categoryRemove(id).then(function () {
                    $scope.categoryTreeFetch(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.categoryTreeActions = {
        edit: {
            fnc: showDetailsDialog
        },
        addBelow: {
            fnc: addBelow
        },
        remove: {
            fnc: function ($event, id) {
                $event.stopPropagation();
                $event.preventDefault();

                $scope.categoryRemove(id).then(function () {
                    $scope.categoryTreeFetch(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    $scope.categoryTreeFetch(
        $scope.pageHeaderConfig.search.searchString
    );

    function showDetailsDialog($event, id) {
        $event.stopPropagation();
        $event.preventDefault();

        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: CategoryDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString,
                parentId: null,
                categoryId: id
            },
            controller: CategoryDetailController
        });
    }

    function addBelow($event, parentId) {
        $event.stopPropagation();
        $event.preventDefault();

        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: CategoryDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString,
                parentId: parentId,
                categoryId: undefined
            },
            controller: CategoryDetailController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

CategoryController.$inject = CategoryControllerInject;

export default ['CategoryController', CategoryController];
