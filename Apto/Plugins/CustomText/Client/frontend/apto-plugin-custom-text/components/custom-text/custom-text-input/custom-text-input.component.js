import Template from './custom-text-input.component.html';

const ControllerInject = ['$location', '$ngRedux', 'ConfigurationService', 'LanguageFactory', 'ElementValuesService', 'PersistedPropertiesFactory', 'SnippetFactory'];
class Controller {
    static getStateText(state, sectionId, elementId, getPersistedProperty) {
        return getPersistedProperty(
            sectionId,
            elementId,
            'text',
            state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['text']
        );
    }

    constructor ($location, $ngRedux, ConfigurationService, LanguageFactory, ElementValuesService, PersistedPropertiesFactory, SnippetFactory) {
        this.location = $location;
        this.ngRedux = $ngRedux;
        this.configurationService = ConfigurationService;
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        this.elementValuesService = ElementValuesService;
        this.snippetFactory = SnippetFactory;
        this.elementIsSelected = this.configurationService.elementIsSelected;
        this.elementIsDisabled = this.configurationService.elementIsDisabled;
        this.getPersistedProperty = PersistedPropertiesFactory.getPersistedProperty;

        this.reduxProps = {
            'text': ''
        };

        this.reduxActions = {};
    }

    mapStateProps(getStateText, sectionId, elementId, getPersistedProperty) {
        return (state) => {
            let mapping = {
                text: getStateText(state, sectionId, elementId, getPersistedProperty),
                useStepByStep: state.product.productDetail.useStepByStep
            };

            if (state.livePrice) {
                mapping.livePricePrices = state.livePrice.prices
            }

            return mapping;
        }
    }

    reduxConnect() {
        return this.ngRedux.connect(
            this.mapStateProps(
                Controller.getStateText,
                this.section.id,
                this.element.id,
                this.getPersistedProperty
            ), {
            }
        )((selectedState, actions) => {
            this.reduxProps = selectedState;
            this.reduxActions = actions;
        });
    }

    setValues() {
        if (!this.input.text) {
            this.input.text = '';
        }

        this.configurationService.setElementProperties(
            this.section.id,
            this.element.id,
            {
                aptoElementDefinitionId: this.staticValues.aptoElementDefinitionId,
                text: this.input.text
            },
            true,
            this.reduxProps.useStepByStep
        );
    }

    removeValues() {
        this.configurationService.removeElement(this.section.id, this.element.id);
        this.input = angular.copy(this.reduxProps);
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get(path, trustAsHtml);
    }

    $onInit() {
        this.element = this.elementInput;

        if (typeof this.sectionInput !== 'undefined') {
            this.section = this.sectionInput;
        } else {
            this.section = this.sectionCtrlInput;
        }

        this.reduxDisconnect = this.reduxConnect();
        this.input = angular.copy(this.reduxProps);
        this.staticValues = this.element.definition.staticValues;
    }

    $onDestroy() {
        this.reduxDisconnect();
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        elementInput: '<element',
        sectionInput: '<section',
        sectionCtrlInput: '<sectionCtrl'
    },
    template: Template,
    controller: Controller
};

export default ['aptoCustomTextInput', Component];