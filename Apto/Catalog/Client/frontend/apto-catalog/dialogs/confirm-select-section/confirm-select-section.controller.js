const ControllerInject = ['$scope', 'ngDialog', 'SnippetFactory', 'LanguageFactory'];
const Controller = function ($scope, ngDialog, SnippetFactory, LanguageFactory) {
    function closeDialog(value) {
        ngDialog.close(null, value);
    }

    function snippet(path) {
        return SnippetFactory.get('aptoStepByStep.confirmSelectSectionDialog.' + path);
    }

    $scope.closeDialog = closeDialog;
    $scope.translate = LanguageFactory.translate;
    $scope.snippet = snippet;
};

Controller.$inject = ControllerInject;

export default ['AptoDialogConfirmSelectSectionController', Controller];