import SummaryTemplate from './summary.component.html';
import confirmSelectSectionDialog from '../../../apto-catalog/dialogs/confirm-select-section/confirm-select-section.dialog';

const SummaryControllerInject = ['$location', '$ngRedux', 'ngDialog', 'LanguageFactory', 'ConfigurationService', 'IndexActions', 'ProductActions', 'SnippetFactory', 'ConfigurationActions'];
const SummaryController = function ($location, $ngRedux, ngDialog, LanguageFactory, ConfigurationService, IndexActions, ProductActions, SnippetFactory, ConfigurationActions) {
    const self = this;
    self.showAptoSlider = false;

    function mapStateToThis(state) {
        return {
            aptoProduct: state.configuration.present.raw.product,
            configurationId: state.configuration.present.configurationId,
            configurationType: state.configuration.present.configurationType,
            currentRenderImage: state.renderImage.currentRenderImage,
            quantity: state.index.quantity,
            humanReadableState: state.humanReadableState.humanReadableState
        }
    }

    const reduxUnSubscribe = $ngRedux.connect(mapStateToThis, {
        setQuantity: IndexActions.setQuantity,
        setSelectedSection: ProductActions.selectSection,
        setProductView: ConfigurationActions.setProductView
    })((selectedState, actions) => {
        // state
        self.aptoProduct = selectedState.aptoProduct;
        self.configurationId = selectedState.configurationId;
        self.configurationType = selectedState.configurationType;
        self.currentRenderImage = selectedState.currentRenderImage;
        self.quantity = selectedState.quantity;
        self.humanReadableState = selectedState.humanReadableState;

        // actions
        self.setQuantity = actions.setQuantity;
        self.setSelectedSection = actions.setSelectedSection;
        self.setProductView = actions.setProductView;
        if (self.currentRenderImage) {
            self.showAptoSlider = true;
        }

    });

    // custom component functions
    function onSelectSection(sectionId) {
        if (!ConfigurationService.sectionIsSelected(sectionId) && !ConfigurationService.sectionIsComplete(sectionId)) {
            return;
        }

        // clear sections on step by step
        if (self.aptoProduct.useStepByStep) {
            onSelectSectionStepByStep(sectionId);
        } else {
            onSelectSectionOnePage(sectionId);
        }
    }

    function onSelectSectionOnePage(sectionId) {
        // select section
        self.setSelectedSection(sectionId);

        // set product view
        self.setProductView('configuration');
    }

    function onSelectSectionStepByStep(sectionId) {
        confirmSelectSectionDialog(ngDialog, SnippetFactory).then((data) => {
            if (!data.value) {
                return;
            }

            ConfigurationService.clearNextSections(sectionId).then(() => {
                // select section
                self.setSelectedSection(sectionId);

                // set product view
                self.setProductView('configuration');
            });
        });
    }

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoSummary.' + path, trustAsHtml);
    }

    function snippetGlobal(path, trustAsHtml) {
        return SnippetFactory.get(path, trustAsHtml);
    }

    function goBack() {
        let sectionIndex = self.stateSummary.sections.length - 1;

        if (sectionIndex > 0) {
            onSelectSection(self.stateSummary.sections[sectionIndex].id);
        }
    }

    function addToBasket(quantity) {
        ConfigurationService.addToBasket(quantity, null, {
            elementPreviewImages: getElementPreviewImages()
        });
    }

    function getElementPreviewImages() {
        const compressedState = ConfigurationService.getCompressedState();
        let elementPreviewImages = {};

        for(let i = 0; i < self.aptoProduct.sections.length; i++) {
            const section = self.aptoProduct.sections[i];

            if (!compressedState[section.id]) {
                continue;
            }

            for (let j = 0; j < section.elements.length; j++) {
                const element = section.elements[j];

                if (!compressedState[section.id][element.id] || !element.previewImage) {
                    continue;
                }

                if (!elementPreviewImages[section.id]) {
                    elementPreviewImages[section.id] = {};
                }

                elementPreviewImages[section.id][element.id] = APTO_API.media + element.previewImage.mediaFile.path + '/' + element.previewImage.mediaFile.filename + '.' + element.previewImage.mediaFile.extension;
            }
        }

        return elementPreviewImages;
    }

    self.onSelectSection = onSelectSection;
    self.translate = LanguageFactory.translate;
    self.getActiveSectionNames = ConfigurationService.getActiveSectionNames;
    self.getActiveSectionElementNames = ConfigurationService.getActiveSectionElementNames;
    self.addToBasket = addToBasket;
    self.stateSummary = ConfigurationService.getStateSummary();
    self.getStatePrice = ConfigurationService.getFormattedStatePrice;
    self.getProductPrice = ConfigurationService.getFormattedProductPrice;
    self.getSectionPrice = ConfigurationService.getFormattedSectionPrice;
    self.getElementPrice = ConfigurationService.getFormattedElementPrice;
    self.getProductDiscountName = ConfigurationService.getProductDiscountName;
    self.getSectionDiscountName = ConfigurationService.getSectionDiscountName;
    self.getElementDiscountName = ConfigurationService.getElementDiscountName;
    self.getShowGross = ConfigurationService.getShowGross;
    self.hasStatePseudoPrice = ConfigurationService.hasStatePseudoPrice;
    self.hasProductPseudoPrice = ConfigurationService.hasProductPseudoPrice;
    self.hasSectionPseudoPrice = ConfigurationService.hasSectionPseudoPrice;
    self.hasElementPseudoPrice = ConfigurationService.hasElementPseudoPrice;
    self.snippet = snippet;
    self.snippetGlobal = snippetGlobal;
    self.goBack = goBack;

    //angular component lifecycle
    self.$onInit = function () {
        if (!ConfigurationService.configurationIsValid()) {
            self.setProductView('configuration');
        }
    };

    self.$onDestroy = function () {
        reduxUnSubscribe();
    };
};

const Summary = {
    template: SummaryTemplate,
    controller: SummaryController,
};

SummaryController.$inject = SummaryControllerInject;

export default ['aptoSummary', Summary];