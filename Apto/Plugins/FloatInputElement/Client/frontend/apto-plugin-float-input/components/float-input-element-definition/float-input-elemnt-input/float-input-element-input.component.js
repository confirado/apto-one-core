import { parser } from 'mathjs';
import { BigNumber } from 'bignumber.js';
import Template from './float-input-element-input.component.html';

const ControllerInject = ['$ngRedux', 'ConfigurationService', 'LanguageFactory', 'ElementValuesService', 'UserInputParser', 'PersistedPropertiesFactory', 'SnippetFactory'];
class Controller {
    static getStateValue(state, sectionId, elementId, staticValues, getPersistedProperty) {
        let value = getPersistedProperty(
            sectionId,
            elementId,
            'value',
            state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['value']
        );
        if (staticValues.useDefaultValue) {
            if (value === null) {
                value = staticValues.defaultValue;
            }
            if (!staticValues.showDefaultValue && value === staticValues.defaultValue) {
                value = null;
            }
        }
        return value;
    }

    constructor($ngRedux, ConfigurationService, LanguageFactory, ElementValuesService, UserInputParser, PersistedPropertiesFactory, SnippetFactory) {
        this.ngRedux = $ngRedux;
        this.configurationService = ConfigurationService;
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        this.elementValuesService = ElementValuesService;
        this.snippetFactory = SnippetFactory;
        this.elementIsSelected = this.configurationService.elementIsSelected;
        this.elementIsDisabled = this.configurationService.elementIsDisabled;
        this.convertFloat = UserInputParser.convertFloat;
        this.getPersistedProperty = PersistedPropertiesFactory.getPersistedProperty;

        this.reduxProps = {
            value: 0
        };

        this.reduxActions = {};
        this.mathjsParser = parser();
        this.validValueReference = {
            minimum: null,
            maximum: null
        };
    };

    mapStateProps(getStateValue, sectionId, elementId, staticValues, getPersistedProperty) {
        return (state) => {
            let mapping = {
                value: getStateValue(state, sectionId, elementId, staticValues, getPersistedProperty),
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
                Controller.getStateValue,
                this.section.id,
                this.element.id,
                this.staticValues,
                this.getPersistedProperty
            ), {
            }
        )((selectedState, actions) => {
            this.reduxProps = selectedState;
            this.reduxActions = actions;
        });
    }

    setValues() {
        let value = this.input.value ? ('' + this.input.value).trim() : this.input.value;

        // set default value, if option enabled
        if (this.staticValues.useDefaultValue && (value === '' || value === null)) {
            value = this.staticValues.defaultValue;
        } else if (!this.staticValues.useDefaultValue || value !== this.staticValues.defaultValue) {
            value = this.convertFloat(value);
        }

        // handle valid value reference
        const errorMessage = {
            messages: [this.element.errorMessage]
        }
        if (null !== this.validValueReference.minimum && value < this.validValueReference.minimum) {
            this.configurationService.handleException(errorMessage);
            return;
        }

        if (null !== this.validValueReference.maximum && value > this.validValueReference.maximum) {
            this.configurationService.handleException(errorMessage);
            return;
        }

        // set value
        this.configurationService.setElementProperties(
            this.section.id,
            this.element.id,
            {
                value: value
            },
            true,
            this.reduxProps.useStepByStep
        );
    }

    removeValues() {
        this.configurationService.removeElement(this.section.id, this.element.id);
        this.input = angular.copy(this.reduxProps);
    }

    $onInit() {
        this.element = this.elementInput;
        this.staticValues = this.element.definition.staticValues;

        if (typeof this.sectionInput !== 'undefined') {
            this.section = this.sectionInput;
        } else {
            this.section = this.sectionCtrlInput;
        }

        this.reduxDisconnect = this.reduxConnect();
        this.input = angular.copy(this.reduxProps);

        this.initValidValueReference();
    }

    initValidValueReference() {
        for (let i = 0; i < this.staticValues.elementValueRefs.length; i++) {
            const elementValueRef = this.staticValues.elementValueRefs[i];
            const configurationValue = this.configurationService.getElementPropertyValue(
                elementValueRef.sectionId, elementValueRef.elementId, elementValueRef.selectableValue
            );

            // continue if value is not set in configuration
            if (null === configurationValue) {
                continue;
            }

            switch (elementValueRef.compareValueType) {
                case 'Minimum': {
                    // continue if minimum is already set because the first found value counts
                    if (null !== this.validValueReference.minimum) {
                        continue;
                    }

                    this.validValueReference.minimum = this.getValidValueReference(elementValueRef, configurationValue);
                    break;
                }
                case 'Maximum': {
                    // continue if maximum is already set because the first found value counts
                    if (null !== this.validValueReference.maximum) {
                        continue;
                    }

                    this.validValueReference.maximum = this.getValidValueReference(elementValueRef, configurationValue);
                    break;
                }
            }

            if (null !== this.validValueReference.minimum && null !== this.validValueReference.maximum) {
                break;
            }
        }
    }

    getValidValueReference(elementValueRef, configurationValue) {
        let value;

        if (elementValueRef.compareValueFormula) {
            this.mathjsParser.set(elementValueRef.selectableValue, configurationValue);
            value = this.mathjsParser.evaluate(elementValueRef.compareValueFormula);
            this.mathjsParser.clear();
        } else {
            value = configurationValue;
        }

        return this.roundValueByStep(
            value,
            this.getLowestStep()
        );
    }

    getHumanReadableValidValue() {
        const suffix = this.translate(this.staticValues.suffix);
        let humanReadableString = this.elementValuesService.getHumanReadableString(
            this.element.definition.properties.value,
            '',
            suffix
        )
        const humanReadableStringArray = humanReadableString.split(' - ');

        if (humanReadableStringArray.length !== 2) {
            return humanReadableString;
        }

        if (null !== this.validValueReference.minimum) {
            humanReadableStringArray[0] = this.validValueReference.minimum + ' ' + suffix;
        }

        if (null !== this.validValueReference.maximum) {
            humanReadableStringArray[1] = this.validValueReference.maximum + ' ' + suffix;
        }

        return humanReadableStringArray[0] + ' - ' + humanReadableStringArray[1];
    }

    getLowestStep() {
        const validValues = this.element.definition.properties.value;
        let step = null;

        for (let i = 0; i < validValues.length; i++) {
            if (null === step || step > validValues[i].step) {
                step = validValues[i].step;
            }
        }
        return step;
    }

    roundValueByStep(valueInput, stepInput) {
        const
            value = new BigNumber(valueInput.toString()),
            step = new BigNumber(stepInput.toString())
        ;

        return parseFloat(value.toFixed(step.decimalPlaces()));
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

export default ['floatInputElementInput', Component];