import HeaderTemplate from './header.component.html';

const HeaderControllerInject = ['SnippetFactory'];
const HeaderController = function(SnippetFactory) {
    const self = this;
    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoHeader.' + path, trustAsHtml);
    }
    self.snippet = snippet;
};

HeaderController.$inject = HeaderControllerInject;

const Header = {
    template: HeaderTemplate,
    controller: HeaderController
};

export default ['aptoHeader', Header];