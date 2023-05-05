import PriceGroupDetailTemplate from './price-group-detail.controller.html';
import PriceGroupDetailController from './price-group-detail.controller';

const PriceGroupControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'IndexActions', 'MaterialPickerPriceGroupActions', 'LanguageFactory'];
const PriceGroupController = function($scope, $mdDialog, $ngRedux, IndexActions, MaterialPickerPriceGroupActions, LanguageFactory) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.pluginMaterialPickerPriceGroup.pageHeaderConfig,
            dataListConfig: state.pluginMaterialPickerPriceGroup.dataListConfig,
            priceGroups: state.pluginMaterialPickerPriceGroup.priceGroups
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        setPageNumber: MaterialPickerPriceGroupActions.setPageNumber,
        setSearchString: MaterialPickerPriceGroupActions.setSearchString,
        fetchPriceGroupsByPage: MaterialPickerPriceGroupActions.fetchPriceGroupsByPage,
        removePriceGroup: MaterialPickerPriceGroupActions.removePriceGroup
    })($scope);

    function init() {
        fetchPriceGroupsByPage();
    }

    function fetchPriceGroupsByPage() {
        $scope.fetchPriceGroupsByPage(
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
            template: PriceGroupDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                priceGroupId: id
            },
            controller: PriceGroupDetailController
        }).then(fetchPriceGroupsByPage, fetchPriceGroupsByPage);
    }

    $scope.pageHeaderActions = {
        pageChanged: {
            fnc: function (page) {
                $scope.setPageNumber(page);
                fetchPriceGroupsByPage();
            }
        },
        search: {
            fnc: function (searchString) {
                $scope.setSearchString(searchString);
                fetchPriceGroupsByPage();
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
                $scope.removePriceGroup(id).then(fetchPriceGroupsByPage);
            }
        }
    };

    init();

    $scope.$on('$destroy', subscribedActions);
    //$scope.translate = LanguageFactory.translate;
};

PriceGroupController.$inject = PriceGroupControllerInject;

export default ['MaterialPickerPriceGroupController', PriceGroupController];
