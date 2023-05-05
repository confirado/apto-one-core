const CustomerControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'CustomerActions', 'IndexActions'];
const CustomerController = function($scope, $mdDialog, $ngRedux, CustomerActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.customer.pageHeaderConfig,
            dataListConfig: state.customer.dataListConfig,
            customers: state.customer.customers
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: CustomerActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        customersFetch: CustomerActions.customersFetch
    })($scope);

    $scope.pageHeaderActions = {
        search: {
            fnc: $scope.customersFetch
        },
        listStyle: {
            fnc: $scope.setListTemplate
        },
        toggleSideBarRight: {
            fnc: $scope.toggleSidebarRight
        }
    };

    $scope.dataListActions = {
    };

    $scope.customersFetch(
        $scope.pageHeaderConfig.search.searchString
    );

    $scope.$on('$destroy', subscribedActions);
};

CustomerController.$inject = CustomerControllerInject;

export default ['CustomerController', CustomerController];