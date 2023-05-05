import SidebarInfoTemplate from './sidebar-info.component.html';

const SidebarInfoControllerInject = ['ConfigurationService', '$ngRedux', 'LanguageFactory', 'IndexActions', 'SnippetFactory'];
const SidebarInfoController = function (ConfigurationService, $ngRedux, LanguageFactory, IndexActions, SnippetFactory) {
    const self = this;
    const reduxUnsubscribe = $ngRedux.connect(mapStateToThis,{
        setQuantity: IndexActions.setQuantity
    })(self);

    function mapStateToThis(state) {
        return {
            quantity: state.index.quantity,
            shopUrls: state.index.shopSession.url
        }
    }

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoSidebarInfo.' + path, trustAsHtml);
    }

    function snippetGlobal(path,trustAsHtml) {
        return SnippetFactory.get(path, trustAsHtml);
    }

    self.snippet = snippet;
    self.snippetGlobal = snippetGlobal;
    self.getStatePrice = ConfigurationService.getFormattedStatePrice;
    self.hasStatePseudoPrice = ConfigurationService.hasStatePseudoPrice;
    self.hasProductPseudoPrice = ConfigurationService.hasProductPseudoPrice;
    self.getShowGross = ConfigurationService.getShowGross;
    self.translate = LanguageFactory.translate;

    self.$onDestroy = function () {
        reduxUnsubscribe();
    }
};

const SidebarInfo = {
    template: SidebarInfoTemplate,
    controller: SidebarInfoController,
    bindings: {
        productDetail: "<product"
    }
};

SidebarInfoController.$inject = SidebarInfoControllerInject;

export default ['aptoSidebarInfo', SidebarInfo];