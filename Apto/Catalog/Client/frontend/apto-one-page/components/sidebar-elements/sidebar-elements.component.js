import SidebarElementsTemplate from './sidebar-elements.component.html';

const SidebarElementsControllerInject = ['$ngRedux', 'OnePageElementActions', 'ProductActions', 'LanguageFactory', 'SnippetFactory'];
const SidebarElementsController = function ($ngRedux, OnePageElementActions, ProductActions, LanguageFactory, SnippetFactory) {
    const self = this;

    self.mapStateToThis = function (state) {
        return {
            sidebarOpen: state.onePageElement.sidebarOpen,
            section: state.product.selectedSection
        }
    };

    self.resetSelect = function () {
        self.selectedColorGroup = null;
    };

    self.closeSidebar = function () {
        self.setSidebarOpen(false);
    };

    self.snippet = function (path, trustAsHtml) {
        return SnippetFactory.get('aptoOnePage.sidebarElements' + path, trustAsHtml);
    };

    const reduxSubscribe = $ngRedux.connect(self.mapStateToThis, {
        setSidebarOpen: OnePageElementActions.setSidebarOpen,
        setSelectedSection: ProductActions.selectSection
    })(self);

    self.$onDestroy = function () {
        reduxSubscribe();
    };
    self.translate = LanguageFactory.translate;
};

const SidebarElements = {
    template: SidebarElementsTemplate,
    controller: SidebarElementsController,
};

SidebarElementsController.$inject = SidebarElementsControllerInject;

export default ['aptoSidebarElements', SidebarElements];