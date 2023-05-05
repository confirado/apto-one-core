import SidebarSummaryTemplate from './sidebar-summary.component.html';

import OfferConfigurationDialogTemplate from '../../../apto-catalog/dialogs/offer-configuration/offer-configuration.controller.html';
import OfferConfigurationDialogController from "../../../apto-catalog/dialogs/offer-configuration/offer-configuration.controller";

const SidebarSummaryControllerInject = ['$location', '$ngRedux', 'LanguageFactory', 'ConfigurationService', 'IndexActions', 'SnippetFactory', 'ngDialog', 'ConfigurationActions'];
const SidebarSummaryController = function($location, $ngRedux, LanguageFactory, ConfigurationService, IndexActions, SnippetFactory, ngDialog, ConfigurationActions) {
    const self = this;

    function mapStateToThis(state) {
        return {
            shopSession: state.index.shopSession,
            quantity: state.index.quantity,
            currentRenderImage: state.renderImage.currentRenderImage
        }
    }

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoStepByStep.sidebarSummary.' + path, trustAsHtml);
    }

    function snippetGlobal(path,trustAsHtml) {
        return SnippetFactory.get(path, trustAsHtml);
    }

    function isOfferEnabled() {
        if (snippetGlobal('AptoOfferConfigurationDialog.enabled') === 'true') {
            return true;
        }
        return false;
    }

    const reduxUnSubscribe = $ngRedux.connect(mapStateToThis, {
        setQuantity: IndexActions.setQuantity,
        setProductView: ConfigurationActions.setProductView
    })(self);

    self.$onChanges = function (changes) {
        if(changes.productDetail){
            self.product = self.productDetail;
        }
    };

    self.$onInit = function () {
        self.product = self.productDetail;
    };

    self.$onDestroy = function () {
        reduxUnSubscribe();
    };

    self.goToSummary = function () {
        if(self.configurationIsValid() === true) {
            self.setProductView('summary');
        } else {
            self.openErrorDialog();
        }
    };

    function openErrorDialog () {
        self.ngDialog.open({
            template:   '<div>' +
                        '<h3>' + this.snippet('basketNotificationTitle') +'</h3>' +
                        '<p>' + this.snippet('basketNotification') +'</p>' +
                        '</div>',
            plain: true,
            className: 'ngdialog-theme-default apto-ngdialog-scroll inside-directive-plain margin-null sbs-error-message',
            name: 'inside-directive-plain',
            height: '180px',
            background: 'none'
        });
    }

    function openOfferConfigurationDialog($event) {
        $event.preventDefault();
        self.ngDialog.open({
            template: OfferConfigurationDialogTemplate,
            plain: true,
            controller: OfferConfigurationDialogController,
            className: 'ngdialog-theme-default',
            width: '360px'
        });
    }

    self.translate = LanguageFactory.translate;
    self.sectionIsSelected = ConfigurationService.sectionIsSelected;
    self.getStatePrice = ConfigurationService.getFormattedStatePrice;
    self.getProductDiscountName = ConfigurationService.getProductDiscountName;
    self.configurationIsValid = ConfigurationService.configurationIsValid;
    self.hasStatePseudoPrice = ConfigurationService.hasStatePseudoPrice;
    self.hasProductPseudoPrice = ConfigurationService.hasProductPseudoPrice;
    self.getShowGross = ConfigurationService.getShowGross;
    self.snippet = snippet;
    self.snippetGlobal = snippetGlobal;
    self.ngDialog = ngDialog;
    self.openErrorDialog = openErrorDialog;
    self.openOfferConfigurationDialog = openOfferConfigurationDialog;
    self.isOfferEnabled = isOfferEnabled;
};

const SidebarSummary = {
    template: SidebarSummaryTemplate,
    controller: SidebarSummaryController,
    bindings: {
        productDetail: "<productDetail",
        selectedSection: "<activeSection"
    }
};

SidebarSummaryController.$inject = SidebarSummaryControllerInject;

export default ['aptoSidebarSummary', SidebarSummary];
