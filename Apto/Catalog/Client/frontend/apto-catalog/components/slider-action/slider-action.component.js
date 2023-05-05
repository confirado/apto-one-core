import SliderActionTemplate from './slider-action.component.html';

import GuestConfigurationDialogTemplate from '../../../apto-catalog/dialogs/guest-configuration/guest-configuration.controller.html';
import GuestConfigurationDialogController from "../../../apto-catalog/dialogs/guest-configuration/guest-configuration.controller";

const SliderActionControllerInject = ['$window', '$ngRedux', 'ngDialog', 'ConfigurationService', 'SnippetFactory', 'LanguageFactory'];
const SliderActionController = function ($window, $ngRedux, ngDialog, ConfigurationService, SnippetFactory, LanguageFactory) {
    const self = this;
    const reduxUnSubscribe = $ngRedux.connect(mapStateToThis, {})(self);
    self.socials = [];

    function mapStateToThis(state) {
        return {
            perspectives: state.renderImage.perspectives,
            aptoProduct: state.configuration.present.raw.product
        }
    }

    function initSocials() {
        let currentURL = encodeURIComponent($window.location.href);
        let socials = SnippetFactory.getNode('aptoSliderAction.shareLinks');
        const productName = LanguageFactory.translate(self.aptoProduct.name);

        for (let social in socials) {
            if (!socials.hasOwnProperty(social)) {
                continue;
            }

            self.socials.push({
                link: LanguageFactory.translate(socials[social].link).replace('{_currentUrl_}', currentURL).replace('{_productName_}', productName),
                iconClass: LanguageFactory.translate(socials[social].iconClass),
                target: LanguageFactory.translate(socials[social].target)
            });
        }
    }

    function openGuestConfigurationDialog($event) {
        $event.preventDefault();
        ngDialog.open({
            template: GuestConfigurationDialogTemplate,
            plain: true,
            controller: GuestConfigurationDialogController,
            className: 'ngdialog-theme-default',
            width: '360px'
        });
    }

    function snippet(path) {
        return SnippetFactory.get('aptoSliderAction.' + path);
    }

    initSocials();

    self.snippet = snippet;
    self.openGuestConfigurationDialog = openGuestConfigurationDialog;
    self.nextPerspective = ConfigurationService.nextPerspective;
    self.previousPerspective = ConfigurationService.previousPerspective;

    self.$onDestroy = function () {
        reduxUnSubscribe();
    };
};

const SliderAction = {
    template: SliderActionTemplate,
    controller: SliderActionController
};

SliderActionController.$inject = SliderActionControllerInject;

export default ['aptoSliderAction', SliderAction];
