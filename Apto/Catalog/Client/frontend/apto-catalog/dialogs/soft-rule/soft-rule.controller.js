const AptoSoftRuleDialogControllerInject = ['$scope', 'ngDialog', 'SnippetFactory', 'LanguageFactory'];
const AptoSoftRuleDialogController = function($scope, ngDialog, SnippetFactory, LanguageFactory) {

    function closeDialog(value) {
        ngDialog.close(null, value);
    }

    function snippet(path) {
        return SnippetFactory.get('AptoSoftRuleDialog.' + path);
    }

    $scope.closeDialog = closeDialog;
    $scope.translate = LanguageFactory.translate;
    $scope.snippet = snippet;
};

AptoSoftRuleDialogController.$inject = AptoSoftRuleDialogControllerInject;

export default AptoSoftRuleDialogController;