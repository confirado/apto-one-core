import FooterNavTemplate from './footer-nav.component.html';

const FooterNavControllerInject = ['$ngRedux', 'SnippetFactory'];
const FooterNavController = function($ngRedux, SnippetFactory) {
    const self = this;
    function mapStateToThis (state) {
        return {
            shopUrls: state.index.shopSession.url
        };
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {})(self);

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoFooterNav.' + path, trustAsHtml);
    }

    self.snippet = snippet;
    self.$onDestroy = function () {
        reduxSubscribe();
    };
};

const FooterNav = {
    template: FooterNavTemplate,
    controller: FooterNavController
};

FooterNavController.$inject = FooterNavControllerInject;

export default ['aptoFooterNav', FooterNav];