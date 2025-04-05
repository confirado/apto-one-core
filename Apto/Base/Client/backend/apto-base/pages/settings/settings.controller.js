const SettingsControllerInject = ['$scope', '$ngRedux'];
const SettingsController = function($scope, $ngRedux) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.settings.pageHeaderConfig,
        }
    };
    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
    })($scope);

    $scope.$on('$destroy', subscribedActions);
};


SettingsController.$inject = SettingsControllerInject;

export default ['SettingsController', SettingsController];
