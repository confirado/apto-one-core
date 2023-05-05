import SidebarMenuTemplate from './sidebar-menu.component.html';
import LoginTemplate from '../../partials/login/login.controller.html';

const SidebarMenuControllerInject = ['$ngRedux', 'IndexActions', 'IndexAuthActions', 'ngDialog', 'SnippetFactory'];
const SidebarMenuController = function($ngRedux, IndexActions, IndexAuthActions, ngDialog, SnippetFactory) {
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
            width: '320px'
        });
    }

    function logout($event) {
        $event.preventDefault();
        self.shopLogout();
    }

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoSidebarMenu.' + path, trustAsHtml);
    }

    function snippetGlobal(path, trustAsHtml) {
        return SnippetFactory.get(path, trustAsHtml);
    }

    self.snippet = snippet;
    self.snippetGlobal = snippetGlobal;
    self.openBasket = openBasket;
    self.openLogin = openLogin;
    self.logout = logout;

    self.$onDestroy = function () {
        reduxSubscribe();
    };
};

SidebarMenuController.$inject = SidebarMenuControllerInject;

const SidebarMenu = {
    template: SidebarMenuTemplate,
    controller: SidebarMenuController
};

export default ['aptoSidebarMenu', SidebarMenu];