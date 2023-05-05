import DetailsController from "../customer-group/customer-group-details.controller";
import DetailsTemplate from "../customer-group/customer-group-details.controller.html";

const CustomerGroupControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'CustomerGroupActions', 'IndexActions'];
const CustomerGroupController = function($scope, $mdDialog, $ngRedux, CustomerGroupActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.customerGroup.pageHeaderConfig,
            dataListConfig: state.customerGroup.dataListConfig,
            customerGroups: state.customerGroup.customerGroups
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: CustomerGroupActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        customerGroupsFetch: CustomerGroupActions.customerGroupsFetch,
        customerGroupsSynchronize: CustomerGroupActions.customerGroupsSynchronize,
        fetchDetails: CustomerGroupActions.fetchDetails,
        removeCustomerGroup: CustomerGroupActions.removeCustomerGroup
    })($scope);

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: $scope.customerGroupsFetch
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
                $scope.removeCustomerGroup(id).then(function () {
                    $scope.customerGroupsFetch(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    function synchronize() {
        $scope.customerGroupsSynchronize().then(
            () => {
                $scope.customerGroupsFetch(
                    $scope.pageHeaderConfig.search.searchString
                );
            }
        );
    }

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);
        if(typeof id !== "undefined") {
            $scope.fetchDetails(id);
        }
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: DetailsTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString
            },
            controller: DetailsController
        });
    }

    $scope.customerGroupsFetch(
        $scope.pageHeaderConfig.search.searchString
    );

    $scope.synchronize = synchronize;

    $scope.$on('$destroy', subscribedActions);
};

CustomerGroupController.$inject = CustomerGroupControllerInject;

export default ['CustomerGroupController', CustomerGroupController];