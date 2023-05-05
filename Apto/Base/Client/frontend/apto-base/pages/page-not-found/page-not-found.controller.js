const AptoBasePageNotFoundControllerInject = ['$scope', 'SnippetFactory'];
const AptoBasePageNotFoundController = function ($scope, SnippetFactory) {
    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoPageNotFound.' + path, trustAsHtml);
    }
    $scope.snippet = snippet;
};

AptoBasePageNotFoundController.$inject = AptoBasePageNotFoundControllerInject;

export default ['AptoBasePageNotFoundController', AptoBasePageNotFoundController];