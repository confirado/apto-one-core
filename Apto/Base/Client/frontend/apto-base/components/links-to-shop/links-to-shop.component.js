import LinksToShopTemplate from './links-to-shop.component.html';
import LoginTemplate from '../../partials/login/login.controller.html';

const LinksToShopControllerInject = ['$ngRedux', 'IndexActions', 'IndexAuthActions', 'ngDialog', 'SnippetFactory'];
const LinksToShopController = function ($ngRedux, IndexActions, IndexAuthActions, ngDialog, SnippetFactory) {
    const self = this;

    function mapStateToThis (state) {
        return {
            shopSession: state.index.shopSession,
            shopConnectorConfigured: state.index.shopConnectorConfigured
        };
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {
        openSidebarRight: IndexActions.openSidebarRight,
        shopLogout: IndexAuthActions.shopLogout
    })(self);

    function openBasket($event) {
        $event.preventDefault();
        self.openSidebarRight();
    }

    function openLogin($event) {
        $event.preventDefault();
        ngDialog.open({
            template: LoginTemplate,
            plain: true,
            controller: 'AptoLoginController',
            className: 'ngdialog-theme-default',
            width: '360px'
        });
    }

    function logout($event) {
        $event.preventDefault();
        self.shopLogout();
    }

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoLinksToShop.' + path, trustAsHtml);
    }

    self.openBasket = openBasket;
    self.openLogin = openLogin;
    self.logout = logout;
    self.snippet = snippet;

    self.$onDestroy = function () {
        reduxSubscribe();
    };
};

const LinksToShop = {
    template: LinksToShopTemplate,
    controller: LinksToShopController
};

LinksToShopController.$inject = LinksToShopControllerInject;

export default ['aptoLinksToShop', LinksToShop];