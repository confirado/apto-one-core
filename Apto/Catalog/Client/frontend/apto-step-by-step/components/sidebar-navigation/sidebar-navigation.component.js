import SidebarNavigationTemplate from './sidebar-navigation.component.html';
import confirmSelectSectionDialog from '../../../apto-catalog/dialogs/confirm-select-section/confirm-select-section.dialog';

const SidebarNavigationControllerInject = ['$location', '$rootScope', '$ngRedux', 'ngDialog', 'LanguageFactory', 'ConfigurationService', 'ConfigurationActions', 'ProductActions', 'SnippetFactory'];
const SidebarNavigationController = function ($location, $rootScope, $ngRedux, ngDialog, LanguageFactory, ConfigurationService, ConfigurationActions, ProductActions, SnippetFactory) {
    const self = this;

    // redux state and action map
    function mapStateToThis(state) {
        return {
            currentRenderImage: state.renderImage.currentRenderImage,
            configurationState: state.configuration.present.configurationState,
            aptoProduct: state.configuration.present.raw.product
        }
    }

    const reduxSubscribe = $ngRedux.connect(null, {
        setSelectedSection: ProductActions.selectSection,
        setProductView: ConfigurationActions.setProductView
    })(self);

    const reduxUnSubscribe = $ngRedux.connect(mapStateToThis, {})((selectedState, actions) => {
        if (selectedState.currentRenderImage || selectedState.aptoProduct.previewImage) {
            self.showAptoSlider = true;
        }

        self.firstNotCompleteSection = ConfigurationService.getFirstNotCompleteSection();
    });

    // custom component functions
    function onSelectSection(sectionId) {
        if (!self.sectionIsSelected(sectionId) && !self.sectionIsComplete(sectionId)) {
            return;
        }

        confirmSelectSectionDialog(ngDialog, SnippetFactory).then((data) => {
            if (!data.value) {
                return;
            }

            ConfigurationService.clearNextSections(sectionId).then(() => {
                self.setSelectedSection(sectionId);
                $rootScope.$emit('CONTINUE_WITH_NEW_SECTION');
            });
        });
    }

    // render
    function getHumanReadableProperties(sectionId, elementId) {
        return  self.getElementById(sectionId, elementId).humanReadableState;
    }

    self.onSelectSection = onSelectSection;
    self.getHumanReadableProperties = getHumanReadableProperties;

    //angular component lifecycle
    self.$onInit = function () {
        self.product = self.productDetail;
        self.section = self.selectedSection;

        self.showAptoSlider = false;
        self.translate = LanguageFactory.translate;
        self.firstNotCompleteSection = ConfigurationService.getFirstNotCompleteSection();
        self.getActiveSectionElements = ConfigurationService.getActiveSectionElements;
        self.getElementById = ConfigurationService.getElementById;
        self.sectionIsSelected = ConfigurationService.sectionIsSelected;
        self.sectionIsComplete = ConfigurationService.sectionIsComplete;
        self.sectionIsDisabled = ConfigurationService.sectionIsDisabled;
        self.sectionIsHidden = ConfigurationService.sectionIsHidden;

        if (null === self.firstNotCompleteSection) {
            self.setProductView('summary');
        } else {
            self.setSelectedSection(self.firstNotCompleteSection);
        }
    };

    self.$onChanges = function (changes) {
        if (changes.productDetail) {
            self.product = self.productDetail;
        }
        if (changes.selectedSection) {
            self.section = self.selectedSection;
            self.firstNotCompleteSection = ConfigurationService.getFirstNotCompleteSection();
        }
    };

    self.$onDestroy = function () {
        reduxUnSubscribe();
        reduxSubscribe();
    };
};

const SidebarNavigation = {
    template: SidebarNavigationTemplate,
    controller: SidebarNavigationController,
    bindings: {
        productDetail: "<",
        selectedSection: "<"
    }
};

SidebarNavigationController.$inject = SidebarNavigationControllerInject;

export default ['aptoSidebarNavigation', SidebarNavigation];
