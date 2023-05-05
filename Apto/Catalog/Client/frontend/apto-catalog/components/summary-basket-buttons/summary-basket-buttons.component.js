import ContainerController from 'apto-base/components/apto-container.controller';
import Template from './summary-basket-buttons.component.html';
import confirmSelectSectionDialog from '../../../apto-catalog/dialogs/confirm-select-section/confirm-select-section.dialog';

import OfferConfigurationDialogTemplate from '../../../apto-catalog/dialogs/offer-configuration/offer-configuration.controller.html';
import OfferConfigurationDialogController from "../../../apto-catalog/dialogs/offer-configuration/offer-configuration.controller";

const ControllerInject = ['$ngRedux', 'ngDialog', 'LanguageFactory', 'ConfigurationService', 'SnippetFactory', 'ProductActions', 'ConfigurationActions'];
class Controller extends ContainerController {
    constructor($ngRedux, ngDialog, LanguageFactory, ConfigurationService, SnippetFactory, ProductActions, ConfigurationActions) {
        // parent constructor
        super($ngRedux);

        // services
        this.ngDialog = ngDialog;
        this.languageFactory = LanguageFactory;
        this.configurationService = ConfigurationService;
        this.snippetFactory = SnippetFactory;
        this.productActions = ProductActions;
        this.configurationActions = ConfigurationActions;
    }

    connectProps() {
        return (state) => {
            // state mapping object
            return {
                aptoProduct: state.configuration.present.raw.product
            }
        }
    }

    connectActions() {
        // actions mapping object
        return {
            setSelectedSection: this.productActions.selectSection,
            setProductView: this.configurationActions.setProductView
        }
    }

    onStateChange(state) {
        this.state = state;
    }

    $onInit() {
        // call parent init
        super.$onInit();
    }

    onSelectSection(sectionId) {
        if (!this.configurationService.sectionIsSelected(sectionId) && !this.configurationService.sectionIsComplete(sectionId)) {
            return;
        }

        // clear sections on step by step
        if (this.state.aptoProduct.useStepByStep) {
            this.onSelectSectionStepByStep(sectionId);
        } else {
            this.onSelectSectionOnePage(sectionId);
        }
    }

    onSelectSectionOnePage(sectionId) {
        // select section
        this.actions.setSelectedSection(sectionId);

        // set product view
        this.actions.setProductView('configuration');
    }

    onSelectSectionStepByStep(sectionId) {
        confirmSelectSectionDialog(this.ngDialog, this.snippetFactory).then((data) => {
            if (!data.value) {
                return;
            }

            this.configurationService.clearNextSections(sectionId).then(() => {
                // select section
                this.actions.setSelectedSection(sectionId);

                // set product view
                this.actions.setProductView('configuration');
            });
        });
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get('aptoSummary.' + path, trustAsHtml);
    }

    goBack() {
        const stateSummary = this.configurationService.getStateSummary();
        let sectionIndex = stateSummary.sections.length - 1;

        if (sectionIndex > 0) {
            this.onSelectSection(stateSummary.sections[sectionIndex].id);
        }
    }

    addToBasket(quantity) {
        this.configurationService.addToBasket(quantity, null, {
            elementPreviewImages: this.getElementPreviewImages()
        });
    }

    getElementPreviewImages() {
        const compressedState = this.configurationService.getCompressedState();
        let elementPreviewImages = {};

        for(let i = 0; i < this.state.aptoProduct.sections.length; i++) {
            const section = this.state.aptoProduct.sections[i];

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

    openOfferConfigurationDialog($event) {
        $event.preventDefault();
        this.ngDialog.open({
            template: OfferConfigurationDialogTemplate,
            plain: true,
            controller: OfferConfigurationDialogController,
            className: 'ngdialog-theme-default',
            width: '360px'
        });
    }

    isOfferEnabled() {
        if (this.snippetFactory.get('AptoOfferConfigurationDialog.enabled') === 'true') {
            return true;
        }
        return false;
    }

}

Controller.$inject = ControllerInject;

const Component = {
    template: Template,
    controller: Controller,
    bindings: {

    }
};

export default ['aptoSummaryBasketButtons', Component];
