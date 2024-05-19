import CategoryTab from './tabs/category-tab.html';
import CustomPropertiesTab from './tabs/custom-properties-tab.html';

const CategoryDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'CategoryActions', 'CurrentUserFactory', 'targetEvent', 'showDetailsDialog', 'searchString', 'parentId', 'categoryId', '$templateCache'];
const CategoryDetailController = function($scope, $mdDialog, $ngRedux, CategoryActions, CurrentUserFactory, targetEvent, showDetailsDialog, searchString, parentId, categoryId, $templateCache) {
    $templateCache.put('catalog/pages/category/tabs/category-tab.html', CategoryTab);
    $templateCache.put('catalog/pages/category/tabs/custom-properties-tab.html', CustomPropertiesTab);

    $scope.mapStateToThis = function(state) {
        return {
            categoryDetail: state.category.categoryDetail,
            customProperties: state.category.customProperties
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        categoryTreeFetch: CategoryActions.categoryTreeFetch,
        categoryDetailFetch: CategoryActions.categoryDetailFetch,
        categoryDetailSave: CategoryActions.categoryDetailSave,
        categoryDetailReset: CategoryActions.categoryDetailReset,
        addCategoryCustomProperty: CategoryActions.addCategoryCustomProperty,
        removeCategoryCustomProperty: CategoryActions.removeCategoryCustomProperty,
        fetchCustomProperties: CategoryActions.fetchCustomProperties,
        setDetailValue: CategoryActions.setDetailValue
    })($scope);

    if(null !== parentId) {
        $scope.categoryDetail.parent = parentId;
    }

    function init() {
        if (typeof categoryId !== 'undefined') {
            $scope.categoryDetailFetch(categoryId);
            $scope.fetchCustomProperties(categoryId);
        }
    }

    function addCustomProperty(key, value, translatable) {
        $scope.addCategoryCustomProperty(categoryId, key, value, translatable).then(() => {
            $scope.fetchCustomProperties(categoryId);
        });
    }

    function removeCustomProperty(id) {
        $scope.removeCategoryCustomProperty(categoryId, id).then(() => {
            $scope.fetchCustomProperties(categoryId);
        });
    }

    $scope.currentUserFactory = CurrentUserFactory;

    $scope.save = function (categoryForm, close) {
        if(categoryForm.$valid) {
            $scope.categoryDetailSave($scope.categoryDetail).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if(typeof $scope.categoryDetail.id === "undefined") {
                    $scope.categoryDetailReset();
                    $scope.categoryTreeFetch(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    function onSelectPreviewImage(path) {
        $scope.setDetailValue('previewImage', path);
    }

    $scope.close = function () {
        $scope.categoryDetailReset();
        $scope.categoryTreeFetch(
            searchString
        );
        $mdDialog.cancel();
    };

    init();

    $scope.addCustomProperty = addCustomProperty;
    $scope.removeCustomProperty = removeCustomProperty;
    $scope.onSelectPreviewImage = onSelectPreviewImage;

    $scope.$on('$destroy', subscribedActions);
};

CategoryDetailController.$inject = CategoryDetailControllerInject;

export default CategoryDetailController;
