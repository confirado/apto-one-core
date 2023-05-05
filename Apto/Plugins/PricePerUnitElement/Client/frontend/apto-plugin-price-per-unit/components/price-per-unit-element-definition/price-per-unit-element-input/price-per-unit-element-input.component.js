import Template from './price-per-unit-element-input.component.html';

const ControllerInject = ['$ngRedux', 'ConfigurationService', 'LanguageFactory', 'ElementValuesService', 'PersistedPropertiesFactory', 'SnippetFactory'];
class Controller {
    static getStateActive(state, sectionId, elementId) {
        return state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['active'] === true;
    }

    static getStateText(state, sectionId, elementId, getPersistedProperty) {
        let elementState = state.configuration.present.configurationState[sectionId]['elements'][elementId]['state'];
        return getPersistedProperty(
            sectionId,
            elementId,
            'text',
            elementState.values.text ? elementState.values.text : null
        );
    }

    constructor($ngRedux, ConfigurationService, LanguageFactory, ElementValuesService, PersistedPropertiesFactory, SnippetFactory) {
        this.ngRedux = $ngRedux;
        this.configurationService = ConfigurationService;
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        this.elementValuesService = ElementValuesService;
        this.snippetFactory = SnippetFactory;
        this.elementIsDisabled = this.configurationService.elementIsDisabled;
        this.elementIsSelected = this.configurationService.elementIsSelected;
        this.getPersistedProperty = PersistedPropertiesFactory.getPersistedProperty;

        this.reduxProps = {
            active: false,
            text: null
        };

        this.reduxActions = {};
    };

    mapStateProps(getStateActive, getStateText, sectionId, elementId, getPersistedProperty) {
        return (state) => {
            let mapping = {
                active: getStateActive(state, sectionId, elementId),
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
                Controller.getStateActive,
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
        this.input.active = true;

        let values = {
            active: this.input.active
        };
        if (this.staticValues.textBoxEnabled) {
            values.text = this.input.text;
        }

        this.configurationService.setElementProperties(
            this.section.id,
            this.element.id,
            values,
            true,
            this.reduxProps.useStepByStep
        );
    }

    removeValues() {
        this.configurationService.removeElement(this.section.id, this.element.id);
    }

    $onInit() {
        this.element = this.elementInput;

        if (typeof this.sectionInput !== "undefined") {
            this.section = this.sectionInput;
        } else {
            this.section = this.sectionCtrlInput;
        }

        this.staticValues = this.element.definition.staticValues;
        this.reduxDisconnect = this.reduxConnect();
        this.input = angular.copy(this.reduxProps);
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

export default ['pricePerUnitElementInput', Component];