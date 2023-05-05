import DefaultElementDefinitionTemplate from './default-element-definition.component.html';

const DefaultElementDefinitionControllerInject = ['$ngRedux', 'ConfigurationService', 'LanguageFactory', 'SnippetFactory'];
const DefaultElementDefinitionController = function ($ngRedux, ConfigurationService, LanguageFactory, SnippetFactory) {
    const self = this;

    function setActiveValue() {
        self.configurationService.setSingleValue(self.section.id, self.element.id, null, null, true, true);
    }

    function removeActiveValue() {
        self.configurationService.removeElement(self.section.id, self.element.id);
    }

    function mapStateToThis(state) {
        let mapping = {
            useStepByStep: state.product.productDetail.useStepByStep
        };

        if (state.livePrice) {
            mapping.livePricePrices = state.livePrice.prices
        }

        return mapping
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {
    })(self);

    self.$onInit = function () {
        self.element = self.elementInput;
        if (typeof this.sectionInput !== "undefined") {
            self.section = self.sectionInput;
        } else {
            self.section = self.sectionCtrlInput;
        }
        self.configurationService = ConfigurationService;
        self.elementIsDisabled = self.configurationService.elementIsDisabled;
        self.elementIsSelected = self.configurationService.elementIsSelected;
        self.translate = LanguageFactory.translate;
        self.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
    };

    self.$onDestroy = function () {
        reduxSubscribe();
    };

    self.setActiveValue = setActiveValue;
    self.removeActiveValue = removeActiveValue;
    self.snippet = SnippetFactory.get;
};

const DefaultElementDefinition = {
    template: DefaultElementDefinitionTemplate,
    controller: DefaultElementDefinitionController,
    bindings: {
        elementInput: '<element',
        sectionInput: '<section',
        sectionCtrlInput: '<sectionCtrl'
    }
};

DefaultElementDefinitionController.$inject = DefaultElementDefinitionControllerInject;

export default ['aptoDefaultElementDefinition', DefaultElementDefinition];