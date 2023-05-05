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

    function getSelectNumber(sectionId) {
        let sectionNumber = ConfigurationService.getSelectedElementBackground(sectionId);
        if(sectionNumber !== false){
            return sectionNumber;
        }
    }


    self.$onInit = function () {
        self.product = self.productDetail;
        self.section = self.selectedSection;
    };
    self.translate = LanguageFactory.translate;
    self.sectionIsSelected = ConfigurationService.sectionIsSelected;
    self.getSelectNumber = getSelectNumber;
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

export default ['aptoSbsSidebar', Sidebar];