import SidebarTemplate from './sidebar.component.html';

const SidebarControllerInject = ['LanguageFactory', 'ConfigurationService'];
const SidebarController = function (LanguageFactory, ConfigurationService) {
    const self = this;

    self.$onChanges = function (changes) {
        if (changes.productDetail) {
            self.product = self.productDetail;
        }
        if (changes.selectedSection) {
            self.section = self.selectedSection;
        }
    };

    function getSelectBackground(sectionId) {
        let elementBackground = ConfigurationService.getSelectedElementBackground(sectionId);
        if(elementBackground !== false){
            return {'background-image':'url(' + elementBackground + ')'};
        }
    }

    self.$onInit = function () {
        self.product = self.productDetail;
        self.section = self.selectedSection;
    };
    self.translate = LanguageFactory.translate;
    self.sectionIsSelected = ConfigurationService.sectionIsSelected;
    self.getSelectBackground = getSelectBackground;
};

const Sidebar = {
    template: SidebarTemplate,
    controller: SidebarController,
    bindings: {
        productDetail: "<productDetail",
        selectedSection: "<activeSection"
    }
};

SidebarController.$inject = SidebarControllerInject;

export default ['aptoSidebar', Sidebar];