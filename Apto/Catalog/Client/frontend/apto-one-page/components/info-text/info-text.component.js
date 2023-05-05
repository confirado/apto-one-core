import InfoTextTemplate from './info-text.component.html';

const InfoTextControllerInject = ['LanguageFactory'];
const InfoTextController = function ( LanguageFactory) {
    const self = this;
    self.translate = LanguageFactory.translate;
    self.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
};

const InfoText = {
    template: InfoTextTemplate,
    controller: InfoTextController,
    bindings: {
        text: "<description"
    }
};

InfoTextController.$inject = InfoTextControllerInject;

export default ['aptoInfoText', InfoText];