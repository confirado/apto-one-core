import SidebarSectionTemplate from './sidebar-section.component.html';

const SidebarSectionControllerInject = ['$window', '$ngRedux', 'OnePageElementActions', 'ProductActions',  '$timeout', 'ConfigurationService', 'LanguageFactory'];
const SidebarSectionController = function ($window, $ngRedux, OnePageElementActions, ProductActions,  $timeout, ConfigurationService, LanguageFactory) {
    const self = this;

    const reduxSubscribe = $ngRedux.connect(null, {
        setSidebarOpen: OnePageElementActions.setSidebarOpen,
        setSelectedSection: ProductActions.selectSection
    })(self);

    angular.element($window).bind('resize', onResize);

    function onSelectSection(sectionId) {
        if (!self.sectionIsSelected(sectionId) && !self.sectionIsComplete(sectionId)) {
           return;
        }

        self.setSidebarOpen(true);

        ConfigurationService.clearNextSections(sectionId).then(() => {
            self.setSelectedSection(sectionId);

            try {
                if ($window.matchMedia("(max-width: 1023px)").matches) {
                    angular.element('.select-section').removeClass('active');
                    angular.element('nav.sidebar-section').removeClass('active');
                    $timeout(function () {
                        angular.element('.materials').css('width', $('.materials > .material').length * 2.625 + 'rem' );
                    });
                }
            }
            catch(err) {
                // DO nothing
            }
        });
    }

    function getSelectBackground(sectionId) {
        let elementBackground = ConfigurationService.getSelectedElementBackground(sectionId);
        if(elementBackground !== false){
            return {'background-image':'url(' + elementBackground + ')'};
        }
    }

    function onInit() {
        selectDefaultSection();
    }

    function onDestroy() {
        reduxSubscribe();
    }

    function onResize() {
        selectDefaultSection();
    }

    function selectDefaultSection() {
        try {
            if (!$window.matchMedia("(max-width: 767px)").matches) {
                return;
            }

            if (typeof self.selectedSection.id === "undefined") {
                onSelectSection(self.sections[0]);
            } else {
                onSelectSection(self.selectedSection);
            }
        }
        catch(err) {
            // DO nothing
        }
    }

    self.$onInit = function () {
        self.$onDestroy = onDestroy;
        self.onSelectSection = onSelectSection;
        self.getSelectBackground = getSelectBackground;
        self.sectionIsDisabled = ConfigurationService.sectionIsDisabled;
        self.sectionIsSelected = ConfigurationService.sectionIsSelected;
        self.sectionIsComplete = ConfigurationService.sectionIsComplete;
        self.translate = LanguageFactory.translate;
    };
};

const SidebarSection = {
    template: SidebarSectionTemplate,
    controller: SidebarSectionController,
    bindings: {
        sections: '<',
        selectedSection: '<'
    }
};

SidebarSectionController.$inject = SidebarSectionControllerInject;

export default ['aptoSbsSidebarSection', SidebarSection];