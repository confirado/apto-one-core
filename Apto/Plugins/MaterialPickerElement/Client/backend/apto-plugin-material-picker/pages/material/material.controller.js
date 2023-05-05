import MaterialDetailTemplate from './material-detail.controller.html';
import MaterialDetailController from './material-detail.controller';

const MaterialControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'IndexActions', 'MaterialPickerMaterialActions', 'LanguageFactory'];
const MaterialController = function($scope, $mdDialog, $ngRedux, IndexActions, MaterialPickerMaterialActions, LanguageFactory) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.pluginMaterialPickerMaterial.pageHeaderConfig,
            dataListConfig: state.pluginMaterialPickerMaterial.dataListConfig,
            materials: state.pluginMaterialPickerMaterial.materials
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        setPageNumber: MaterialPickerMaterialActions.setPageNumber,
        setSearchString: MaterialPickerMaterialActions.setSearchString,
        fetchMaterialsByPage: MaterialPickerMaterialActions.fetchMaterialsByPage,
        removeMaterial: MaterialPickerMaterialActions.removeMaterial,
        availableCustomerGroupsFetch: MaterialPickerMaterialActions.availableCustomerGroupsFetch
    })($scope);

    function init() {
        fetchMaterialsByPage();
    }

    function fetchMaterialsByPage() {
        $scope.fetchMaterialsByPage(
            $scope.pageHeaderConfig.pagination.pageNumber,
            $scope.pageHeaderConfig.pagination.recordsPerPage,
            $scope.pageHeaderConfig.search.searchString
        );
    }

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);
        $scope.availableCustomerGroupsFetch();

        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: MaterialDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                materialId: id
            },
            controller: MaterialDetailController
        }).then(fetchMaterialsByPage, fetchMaterialsByPage);
    }

    $scope.pageHeaderActions = {
        pageChanged: {
            fnc: function (page) {
                $scope.setPageNumber(page);
                fetchMaterialsByPage();
            }
        },
        search: {
            fnc: function (searchString) {
                $scope.setSearchString(searchString);
                fetchMaterialsByPage();
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
                $scope.removeMaterial(id).then(fetchMaterialsByPage);
            }
        }
    };

    init();

    $scope.$on('$destroy', subscribedActions);
    $scope.translate = LanguageFactory.translate;
};

MaterialController.$inject = MaterialControllerInject;

export default ['MaterialPickerMaterialController', MaterialController];
