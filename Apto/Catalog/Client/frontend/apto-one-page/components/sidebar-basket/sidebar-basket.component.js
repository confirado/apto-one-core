import SidebarBasketTemplate from './sidebar-basket.component.html';

const SidebarBasketControllerInject = ['$rootScope', '$location', '$ngRedux', 'ConfigurationService', 'SnippetFactory', 'ConfigurationActions'];
const SidebarBasketController = function ($rootScope, $location, $ngRedux, ConfigurationService, SnippetFactory, ConfigurationActions) {
    const self = this;
    const reduxUnsubscribe = $ngRedux.connect(mapStateToThis,{
        setProductView: ConfigurationActions.setProductView
    })(self);

    function mapStateToThis(state) {
        return {
            quantity: state.index.quantity,
            useStepByStep: state.product.productDetail.useStepByStep,
            productDetail: state.product.productDetail,
            locale: state.index.activeLanguage.isocode,
            currency: state.index.shopSession.displayCurrency.currency,
            customerGroupId: state.index.shopSession.customerGroup.id
        }
    }

    function goToSummary() {
        self.setProductView('summary');
    }

    function snippet(path, trustAsHtml) {
        $rootScope.$emit('APTO_SIDEBAR_BASKET_SNIPPET_REFRESHED');
        return SnippetFactory.get('aptoSidebarBasket.' + path, trustAsHtml);
    }

    self.snippet = snippet;
    self.goToSummary = goToSummary;
    self.configurationIsValid = ConfigurationService.configurationIsValid;
    self.addToBasket = function (quantity) {
        ConfigurationService.addToBasket(quantity);
    }

    self.$onDestroy = function () {
        reduxUnsubscribe();
    }
};

const SidebarBasket = {
    template: SidebarBasketTemplate,
    controller: SidebarBasketController
};

SidebarBasketController.$inject = SidebarBasketControllerInject;

export default ['aptoSidebarBasket', SidebarBasket];