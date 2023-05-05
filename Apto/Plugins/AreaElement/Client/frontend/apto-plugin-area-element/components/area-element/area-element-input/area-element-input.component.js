import Template from './area-element-input.component.html';
import PriceMatrixRule from "apto-catalog/model/rules/price-matrix-rule";

const ControllerInject = ['$rootScope', '$ngRedux', 'LanguageFactory', 'ElementValuesService', 'ConfigurationService', 'UserInputParser', 'PersistedPropertiesFactory', 'MessageBusFactory', 'SnippetFactory'];
class Controller {
    constructor($rootScope, $ngRedux, LanguageFactory, ElementValuesService, ConfigurationService, UserInputParser, PersistedPropertiesFactory, MessageBusFactory, SnippetFactory) {
        this.rootScope = $rootScope;
        this.ngRedux = $ngRedux;
        this.languageFactory = LanguageFactory;
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        this.elementValuesService = ElementValuesService;
        this.configurationService = ConfigurationService;
        this.elementIsSelected = this.configurationService.elementIsSelected;
        this.elementIsDisabled = this.configurationService.elementIsDisabled;
        this.convertInt = UserInputParser.convertInt;
        this.convertFloat = UserInputParser.convertFloat;
        this.getPersistedProperty = PersistedPropertiesFactory.getPersistedProperty;
        this.messageBusFactory = MessageBusFactory;
        this.snippetFactory = SnippetFactory;

        // redux
        this.reduxProps = {};
        this.reduxActions = {};

        // user input
        this.input = {};
        this.inputInSync = {};

        // selectbox items
        this.elementValueSelect = {};

        this.boundedEvents = [
            this.rootScope.$on('APTO_CONFIGURATION_SERVICE_SHOP_SESSION_INITIALIZED', this.onConfigurationServiceShopSessionInitialized.bind(this))
        ];
        this.priceMatrixRule = null;
        this.priceMatrixRuleMapping = {
            row: 'height',
            column: 'width'
        };
        this.shopSessionInitialized = this.configurationService.isShopSessionInitialized();

        this.sumOfFieldValue = 0;
    };

    $onInit() {
        this.staticValues = this.element.definition.staticValues;
        this.boundedEvents.push(
            this.reduxConnect()
        );

        this.initFields();
        this.initPriceMatrixRule();

        this.input = angular.copy(this.reduxProps);
    }

    onConfigurationServiceShopSessionInitialized() {
        // set shopSession initialize state
        this.shopSessionInitialized = this.configurationService.isInitialized();

        // init PriceMatrixRule
        this.initPriceMatrixRule();
    }

    initFields() {
        for (let i = 0; i < this.staticValues.fields.length; i++) {
            const
                field = this.staticValues.fields[i],
                fieldName = 'field_' + i,
                fieldProperty = this.element.definition.properties[fieldName];

            this.input[fieldName] = null;

            if (field.rendering === 'select') {
                this.initElementValueSelect(fieldName, fieldProperty);
            }
        }
    }

    initElementValueSelect(fieldName, fieldProperty) {
        this.elementValueSelect[fieldName] = [];
        for (let i = 0; i < fieldProperty.length; i++) {
            const value = fieldProperty[i];
            for (let j = value.minimum; j <= value.maximum; j += value.step) {
                this.elementValueSelect[fieldName].push(j);
            }
        }
    }

    initPriceMatrixRule() {
        if (false === this.shopSessionInitialized) {
            return;
        }

        if (this.staticValues.priceMatrix.id) {
            this.messageBusFactory.query('FindPriceMatrixLookupTable', [
                this.staticValues.priceMatrix.id,
                this.reduxProps.shopSession.shopCurrency,
                this.reduxProps.shopSession.customerGroup.id
            ]).then((response) => {
                this.priceMatrixRule = new PriceMatrixRule(response.data.result, this.priceMatrixRuleMapping);
            });
        } else {
            this.priceMatrixRule = false;
        }
    }

    reduxConnect() {
        return this.ngRedux.connect(
            this.mapStateProps(
                Controller.getStateProperty,
                this.section.id,
                this.element.id,
                this.getPersistedProperty
            ), {

            }
        )((selectedState, actions) => {
            this.reduxProps = selectedState;
            this.reduxActions = actions;

            // sync input values with redux values
            for (let i = 0; i < this.staticValues.fields.length; i++) {
                const fieldName = 'field_' + i;

                if (this.reduxProps[fieldName] !== this.input[fieldName]) {
                    this.input[fieldName] = this.reduxProps[fieldName];
                }
            }

            // set sync lookup object
            this.setInputInSync();
        });
    }

    mapStateProps(getStateProperty, sectionId, elementId, getPersistedProperty) {
        return (state) => {
            let mapping = {
                useStepByStep: state.product.productDetail.useStepByStep,
                shopSession: state.index.shopSession
            };

            for (let i = 0; i < this.staticValues.fields.length; i++) {
                const fieldName = 'field_' + i;
                mapping[fieldName] = getStateProperty(state, sectionId, elementId, fieldName, getPersistedProperty);
            }

            if (state.livePrice) {
                mapping.livePricePrices = state.livePrice.prices;
            }

            return mapping;
        }
    }

    setValues() {
        let values = {};
        let valuesMatrix = {};

        for (let i = 0; i < this.staticValues.fields.length; i++) {
            const fieldName = 'field_' + i;
            values[fieldName] = this.convertFloat(this.input[fieldName]);
        }

        if (this.staticValues.sumOfFieldValueActive) {
            values['sumOfFieldValue'] = this.sumOfFieldValue;
        }

        /* @todo refactor when priceMatrix is supported
        if (this.staticValues.renderingWidth !== 'none') {
            values.width = this.convertInt(this.input.width);
            valuesMatrix.width = this.convertInt(this.input.width);
        } else {
            valuesMatrix.width = 0;
        }

        if (this.staticValues.renderingHeight !== 'none') {
            values.height = this.convertInt(this.input.height);
            valuesMatrix.height = this.convertInt(this.input.height);
        } else {
            valuesMatrix.height = 0;
        }

        if(this.priceMatrixRule && !this.priceMatrixRule.fulfilled(valuesMatrix)) {
            this.configurationService.handleException({
                messages: [
                    this.getElementErrorMessage()
                ]
            });
            return false;
        }*/

        this.configurationService.setElementProperties(this.section.id, this.element.id, values, true, this.reduxProps.useStepByStep);
    }

    getElementErrorMessage() {
        return this.languageFactory.merge(this.element.errorMessage, {
            de_DE: 'Der eingegebene Wert ist unzul�ssig.',
            en_EN: 'The entered value is not acceptable.'
        });
    }

    removeValues() {
        this.configurationService.removeElement(this.section.id, this.element.id);
    }

    $onDestroy() {
        for (let i = 0; i < this.boundedEvents.length; i++) {
            this.boundedEvents[i]();
        }
    }

    onChangeField() {
        this.setInputInSync();
    }

    setInputInSync() {
        this.sumOfFieldValue = 0;
        for (let i = 0; i < this.staticValues.fields.length; i++) {
            const fieldName = 'field_' + i;

            this.sumOfFieldValue += this.convertFloat(this.input[fieldName]);

            // reset value to null if empty string
            if (this.input[fieldName] === '') {
                this.input[fieldName] = null;
            }

            // check value
            if (this.input[fieldName] == this.reduxProps[fieldName]) {
                this.inputInSync[fieldName] = true;
            } else {
                this.inputInSync[fieldName] = false;
            }
        }
    }

    isInputInSync() {
        for (let field in this.inputInSync) {
            if (!this.inputInSync.hasOwnProperty(field)) {
                continue;
            }

            if (!this.inputInSync[field]) {
                return false;
            }
        }
        return true;
    }

    getMaxSumOfFieldValue() {
        let maxSumOfFieldValue = 0;
        for (let i = 0; i < this.element.definition.properties.sumOfFieldValue.length; i++) {
            let sumOfFieldValue = this.element.definition.properties.sumOfFieldValue[i].maximum;
            if (maxSumOfFieldValue < sumOfFieldValue) {
                maxSumOfFieldValue = sumOfFieldValue;
            }
        }
        return maxSumOfFieldValue;
    }

    static getStateProperty(state, sectionId, elementId, property, getPersistedProperty) {
        return getPersistedProperty(
            sectionId,
            elementId,
            property,
            state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values'][property]
        );
    }

    saveSizeOnEnter (event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            this.setValues();
        }
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get(path, trustAsHtml);
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        element: '<',
        section: '<'
    },
    template: Template,
    controller: Controller
};

export default ['aptoAreaElementInput', Component];