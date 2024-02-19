const ControllerInject = ['$scope', '$mdDialog', '$ngRedux'];
const Controller = function($scope, $mdDialog, $ngRedux) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.pluginImportExportImport.pageHeaderConfig,
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {})($scope);
    $scope.$on('$destroy', subscribedActions);
};

Controller.$inject = ControllerInject;

export default ['PluginImportExportImportController', Controller];