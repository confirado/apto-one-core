import FooterPaymentProviderTemplate from './footer-payment-provider.component.html';

const FooterPaymentProviderControllerInject = ['$ngRedux', 'IndexActions', 'SnippetFactory'];
const FooterPaymentProviderController = function($ngRedux, IndexActions, SnippetFactory) {
    const self = this;

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoFooterPaymentProvider.' + path, trustAsHtml);
    }

    self.snippet = snippet;
    self.mediaUrl = APTO_API.media;
};

const FooterPaymentProvider = {
    template: FooterPaymentProviderTemplate,
    controller: FooterPaymentProviderController
};

FooterPaymentProviderController.$inject = FooterPaymentProviderControllerInject;

export default ['aptoFooterPaymentProvider', FooterPaymentProvider];