import WidthHeightInputTemplate from './width-height-input.component.html';
import PriceMatrixRule from "apto-catalog/model/rules/price-matrix-rule";

const WidthHeightInputControllerInject = ['$rootScope', '$location', '$ngRedux', 'LanguageFactory', 'ElementValuesService', 'ConfigurationService', 'UserInputParser', 'PersistedPropertiesFactory', 'MessageBusFactory', 'SnippetFactory'];
class WidthHeightInputController {
    constructor($rootScope, $location, $ngRedux, LanguageFactory, ElementValuesService, ConfigurationService, UserInputParser, PersistedPropertiesFactory, MessageBusFactory, SnippetFactory) {
        this.rootScope = $rootScope;
        this.location = $location;
        this.ngRedux = $ngRedux;
        this.languageFactory = LanguageFactory;
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        this.elementValuesService = ElementValuesService;
        this.configurationService = ConfigurationService;
        this.elementIsSelected = this.configurationService.elementIsSelected;
        this.elementIsDisabled = this.configurationService.elementIsDisabled;
        this.convertFloat = UserInputParser.convertFloat;
        this.getPersistedProperty = PersistedPropertiesFactory.getPersistedProperty;
        this.messageBusFactory = MessageBusFactory;
        this.snippetFactory = SnippetFactory;

        this.reduxProps = {
            width: 0,
            height: 0
        };
        this.input = {
            width: null,
            height: null
        };
        this.reduxActions = {};
        this.inputInSync = {
            width: true,
            height: true
        };

        this.elementValueSelect = {
            width: null,
            height: null
        };

        this.boundedEvents = [
            this.rootScope.$on('APTO_CONFIGURATION_SERVICE_SHOP_SESSION_INITIALIZED', this.onConfigurationServiceShopSessionInitialized.bind(this))
        ];
        this.priceMatrixRule = null;
        this.priceMatrixRuleMapping = {
            row: 'height',
            column: 'width'
        };
        this.shopSessionInitialized = this.configurationService.isShopSessionInitialized();
    };

    $onInit() {
        this.element = this.elementInput;
        this.section = this.sectionInput;

        this.staticValues = this.element.definition.staticValues;
        this.boundedEvents.push(
            this.reduxConnect()
        );
        this.input = angular.copy(this.reduxProps);

        this.initElementValueSelect();
        //this.initElementDefaultValues();
        this.initPriceMatrixRule();
    }

    onConfigurationServiceShopSessionInitialized() {
        // set shopSession initialize state
        this.shopSessionInitialized = this.configurationService.isShopSessionInitialized();

        // init PriceMatrixRule
        this.initPriceMatrixRule();
    }

    initElementValueSelect() {
        switch (this.staticValues.renderingWidth) {
            case 'select': {
                this.elementValueSelect.width = [];
                for (let i = 0; i < this.element.definition.properties.width.length; i++) {
                    const value = this.element.definition.properties.width[i];
                    for (let j = value.minimum; j <= value.maximum; j += value.step) {
                        this.elementValueSelect.width.push(j);
                    }
                }
                this.input.width = this.elementValueSelect.width[0].id;
                break;
            }
        }

        switch (this.staticValues.renderingHeight) {
            case 'select': {
                this.elementValueSelect.height = [];
                for (let i = 0; i < this.element.definition.properties.height.length; i++) {
                    const value = this.element.definition.properties.height[i];
                    for (let j = value.minimum; j <= value.maximum; j += value.step) {
                        this.elementValueSelect.height.push(j);
                    }
                }
                this.input.height = this.elementValueSelect.height[0].id;
                break;
            }
        }
    }

    initElementDefaultValues() {
        let valuesHasChanged = false;

        if (null === this.reduxProps.width && this.staticValues.defaultWidth) {
            this.input.width = this.staticValues.defaultWidth;
            valuesHasChanged = true;
        }

        if (null === this.reduxProps.height && this.staticValues.defaultHeight) {
            this.input.height = this.staticValues.defaultHeight;
            valuesHasChanged = true;
        }

        if (true === valuesHasChanged) {
            this.setValues();
        }
    }

    initPriceMatrixRule() {
        if (
            false === this.shopSessionInitialized ||
            !this.staticValues.priceMatrix.id ||
            !this.reduxProps.shopConnectorConfigured
        ) {
            return;
        }

        this.messageBusFactory.query('FindPriceMatrixLookupTable', [
            this.staticValues.priceMatrix.id,
            this.reduxProps.shopSession.shopCurrency,
            this.reduxProps.shopSession.customerGroup.id
        ]).then((response) => {
            this.priceMatrixRule = new PriceMatrixRule(response.data.result, this.priceMatrixRuleMapping);
        });
    }

    reduxConnect() {
        return this.ngRedux.connect(
            this.mapStateProps(
                WidthHeightInputController.getStateWidth,
                WidthHeightInputController.getStateHeight,
                this.section.id,
                this.element.id,
                this.getPersistedProperty
            ), {

            }
        )((selectedState, actions) => {
            this.reduxProps = selectedState;
            this.reduxActions = actions;

            // sync input values with redux values
            if (this.reduxProps.width !== this.input.width) {
                this.input.width = this.reduxProps.width;
            }

            if (this.reduxProps.height !== this.input.height) {
                this.input.height = this.reduxProps.height;
            }

            // set sync lookup object
            this.setInputInSync();
        });
    }

    mapStateProps(getStateWidth, getStateHeight, sectionId, elementId, getPersistedProperty) {
        return (state) => {
            let mapping = {
                width: getStateWidth(state, sectionId, elementId, getPersistedProperty),
                height: getStateHeight(state, sectionId, elementId, getPersistedProperty),
                useStepByStep: state.product.productDetail.useStepByStep,
                shopSession: state.index.shopSession,
                shopConnectorConfigured: state.index.shopConnectorConfigured
            };

            if (state.livePrice) {
                mapping.livePricePrices = state.livePrice.prices
            }

            return mapping;
        }
    }

    setValues() {
        let values = {};
        let valuesMatrix = {};

        values['aptoElementDefinitionId'] = this.staticValues.aptoElementDefinitionId;

        if (this.staticValues.renderingWidth !== 'none') {
            values.width = this.convertFloat(this.input.width);
            valuesMatrix.width = this.convertFloat(this.input.width);
        } else {
            valuesMatrix.width = 0;
        }

        if (this.staticValues.renderingHeight !== 'none') {
            values.height = this.convertFloat(this.input.height);
            valuesMatrix.height = this.convertFloat(this.input.height);
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
        }

        this.configurationService.setElementProperties(this.section.id, this.element.id, values, true, this.reduxProps.useStepByStep);
    }

    getElementErrorMessage() {
        return this.languageFactory.merge(this.element.errorMessage, {
            de_DE: 'Der eingegebene Wert ist unzulässig.',
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

    onChangeHeight() {
        this.setInputInSync();
    }

    onChangeWidth() {
        this.setInputInSync();
    }

    setInputInSync() {
        // reset width to null if empty string
        if (this.input.width === '') {
            this.input.width = null;
        }

        // check width values
        if (this.input.width == this.reduxProps.width) {
            this.inputInSync.width = true;
        } else {
            this.inputInSync.width = false;
        }

        // reset height to null if empty string
        if (this.input.height === '') {
            this.input.height = null;
        }

        // check height values
        if (this.input.height == this.reduxProps.height) {
            this.inputInSync.height = true;
        } else {
            this.inputInSync.height = false;
        }
    }

    static getStateWidth(state, sectionId, elementId, getPersistedProperty) {
        return getPersistedProperty(
            sectionId,
            elementId,
            'width',
            state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['width']
        );
    }

    static getStateHeight(state, sectionId, elementId, getPersistedProperty) {
        return getPersistedProperty(
            sectionId,
            elementId,
            'height',
            state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['height']
        );
    }
}

WidthHeightInputController.$inject = WidthHeightInputControllerInject;

const WidthHeightInputComponent = {
    bindings: {
        elementInput: '<element',
        sectionInput: '<section'
    },
    template: WidthHeightInputTemplate,
    controller: WidthHeightInputController
};

export default ['aptoWidthHeightInput', WidthHeightInputComponent];
