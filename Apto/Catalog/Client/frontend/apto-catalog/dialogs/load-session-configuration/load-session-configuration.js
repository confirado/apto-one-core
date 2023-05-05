const AptoLoadConfigurationDialogControllerInject = ['$scope', 'ngDialog', 'SnippetFactory'];
const AptoLoadConfigurationDialogController = function($scope, ngDialog, SnippetFactory) {
    function loadSessionConfiguration() {
        closeDialog(true);
    }
    
    function discardSessionConfiguration() {
        closeDialog(false);
    }

    function closeDialog(value) {
        ngDialog.close(null, value);
    }

    function snippet(path) {
        return SnippetFactory.get('AptoLoadConfigurationDialog.' + path);
    }

    $scope.loadSessionConfiguration = loadSessionConfiguration;
    $scope.discardSessionConfiguration = discardSessionConfiguration;
    $scope.snippet = snippet;
};

AptoLoadConfigurationDialogController.$inject = AptoLoadConfigurationDialogControllerInject;

export default AptoLoadConfigurationDialogController;