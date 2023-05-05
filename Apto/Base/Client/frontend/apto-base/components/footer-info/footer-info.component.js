import FooterInfoTemplate from './footer-info.component.html';

const FooterInfoControllerInject = ['$scope', 'SnippetFactory'];
const FooterInfoController = function($scope, SnippetFactory) {
    const self = this;
    $scope.date = new Date();

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoFooterInfo.' + path, trustAsHtml);
    }

    self.snippet = snippet;
};

const FooterInfo = {
    template: FooterInfoTemplate,
    controller: FooterInfoController
};

FooterInfoController.$inject = FooterInfoControllerInject;

export default ['aptoFooterInfo', FooterInfo];