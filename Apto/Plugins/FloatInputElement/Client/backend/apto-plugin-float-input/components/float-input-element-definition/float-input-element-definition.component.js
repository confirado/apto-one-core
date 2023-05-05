import FloatInputElementDefinitionTemplate from './float-input-element-definition.component.html';

const FloatInputElementDefinitionControllerInject = ['$ngRedux', 'ElementActions', 'FloatInputDefinitionActions', 'PricePerUnitDefinitionActions'];
class FloatInputElementDefinitionController {
    constructor($ngRedux, ElementActions, FloatInputDefinitionActions, PricePerUnitDefinitionActions) {
        this.value = {
            minimum: 0,
            maximum: 1,
            step: 1
        };

        this.availableElements = [];
        this.availableSelectableValues = [];
        this.availableComputableValues = [];
        this.availableSelectableValueTypes = ['Selectable', 'Computable'];
        this.compareValueTypes = ['Minimum', 'Maximum'];

        this.input = {
            elementValueRef: {
                sectionId: null,
                elementId: null,
                selectableValue: null,
                selectableValueType: null,
                compareValueType: null,
                compareValueFormula: null
            }
        };

        this.values = {
            prefix: '',
            suffix: '',
            defaultValue: '',
            useDefaultValue: false,
            showDefaultValue: false,
            value: [],
            conversionFactor: '1',
            livePricePrefix: '',
            livePriceSuffix: '',
            elementValueRefs: []
        };

        this.mapStateToThis = function(state) {
            return {
                availableSections: state.pricePerUnitDefinition.sections,
                sectionIdentifiers: state.pricePerUnitDefinition.sectionIdentifiers,
                elementIdentifiers: state.pricePerUnitDefinition.elementIdentifiers,
                availableCustomerGroups: state.floatInputDefinition.availableCustomerGroups,
                floatInputPrices: state.floatInputDefinition.floatInputPrices,
                detailDefinition: state.element.detail.definition,
                definitionValues: state.element.definition.values
            }
        };

        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues,
            fetchSections: PricePerUnitDefinitionActions.fetchSections,
            fetchAvailableCustomerGroups: FloatInputDefinitionActions.fetchAvailableCustomerGroups,
            fetchFloatInputPrices: FloatInputDefinitionActions.fetchFloatInputPrices,
            addFloatInputPrice: FloatInputDefinitionActions.addFloatInputPrice,
            removeFloatInputPrice: FloatInputDefinitionActions.removeFloatInputPrice
        })(this);
    };

    $onInit() {
        if (this.detailDefinition.class === 'Apto\\Plugins\\FloatInputElement\\Domain\\Core\\Model\\Product\\Element\\FloatInputElementDefinition') {
            const valueCollection = this.detailDefinition.json.value.json.collection;

            for (let i = 0; i < valueCollection.length; i++) {
                this.pushValue(valueCollection[i].json);
            }

            this.values.prefix = this.detailDefinition.json.prefix;
            this.values.suffix = this.detailDefinition.json.suffix;
            this.values.defaultValue = this.detailDefinition.json.defaultValue;
            this.values.useDefaultValue = this.detailDefinition.json.useDefaultValue;
            this.values.showDefaultValue = this.detailDefinition.json.showDefaultValue;

            if (this.detailDefinition.json.conversionFactor) {
                this.values.conversionFactor = this.detailDefinition.json.conversionFactor;
            }
            if (this.detailDefinition.json.livePricePrefix) {
                this.values.livePricePrefix = this.detailDefinition.json.livePricePrefix;
            }
            if (this.detailDefinition.json.livePriceSuffix) {
                this.values.livePriceSuffix = this.detailDefinition.json.livePriceSuffix;
            }

            if (this.detailDefinition.json.elementValueRefs) {
                for (let i = 0; i < this.detailDefinition.json.elementValueRefs.length; i++) {
                    let elementValueRef = angular.copy(this.detailDefinition.json.elementValueRefs[i]);

                    if (!elementValueRef.selectableValueType) {
                        elementValueRef.selectableValueType = 'Selectable'
                    }
                    this.pushElementValueRef(elementValueRef);
                }
            }

            this.updateDefinitionValues();
        }

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    if (this.values.value.length < 1 && !this.values.useDefaultValue) {
                        return false;
                    }
                    return true;
                }
            }
        });

        this.initNewPrice();
        this.fetchSections(this.productId).then(() => {
            this.updateAvailableElements();
            this.updateAvailableSelectableValues();
            this.updateAvailableComputableValues();
        });
        this.fetchAvailableCustomerGroups();
        this.fetchFloatInputPrices(this.element.id);
    }

    $onDestroy() {
        this.unSubscribeActions();
    };

    pushValue(value) {
        this.values.value.push(value);
    }

    addValue() {
        this.pushValue({
            minimum: this.value.minimum,
            maximum: this.value.maximum,
            step: this.value.step
        });
        this.updateDefinitionValues();
    };

    removeValue(index) {
        this.values.value.splice(index, 1);
        this.updateDefinitionValues();
    };

    initNewPrice() {
        this.newPrice = {
            amount: '',
            currencyCode: 'EUR',
            customerGroupId: ''
        };
    }

    addPrice() {
        this.addFloatInputPrice(
            this.productId,
            this.sectionId,
            this.element.id,
            this.newPrice.amount,
            this.newPrice.currencyCode,
            this.newPrice.customerGroupId
        ).then(() => {
            this.initNewPrice();
            this.fetchPrices();
        });
    }

    removePrice(priceId) {
        this.removeFloatInputPrice(
            this.element.id,
            priceId
        ).then(() => {
            this.fetchPrices();
        });
    }

    fetchPrices() {
        this.fetchFloatInputPrices(this.element.id);
    }

    updateAvailableElements(resetValuesAfter) {
        if (!this.input.elementValueRef.sectionId) {
            return;
        }

        let section = this.findById(this.availableSections, this.input.elementValueRef.sectionId);

        this.availableElements = [];
        for (let i = 0; i < section.elements.length; i++) {
            const element = section.elements[i];

            if (element.definition.properties) {
                this.availableElements.push(angular.copy(element));
            }
        }

        if (resetValuesAfter) {
            this.input.elementValueRef.elementId = null;
            this.input.elementValueRef.selectableValue = null;
            this.input.elementValueRef.selectableValueType = null;
        } else {
            this.updateDefinitionValues();
        }
    }

    updateAvailableSelectableAndComputableValues() {
        this.updateAvailableSelectableValues(true);
        this.updateAvailableComputableValues(true);
    }

    updateAvailableSelectableValues(resetValuesAfter) {
        if (!this.input.elementValueRef.elementId) {
            return;
        }

        let element = this.findById(this.availableElements, this.input.elementValueRef.elementId);
        this.availableSelectableValues = Object.keys(element.definition.properties);

        if (resetValuesAfter) {
            this.input.elementValueRef.selectableValue = null;
        } else {
            this.updateDefinitionValues();
        }
    }

    updateAvailableComputableValues(resetValuesAfter) {
        if (!this.input.elementValueRef.elementId) {
            return;
        }

        let element = this.findById(this.availableElements, this.input.elementValueRef.elementId);
        this.availableComputableValues = Object.values(element.definition.computableValues);

        if (resetValuesAfter) {
            this.input.elementValueRef.selectableValue = null;
        } else {
            this.updateDefinitionValues();
        }
    }

    addElementValueRef() {
        this.pushElementValueRef({
            sectionId: this.input.elementValueRef.sectionId,
            elementId: this.input.elementValueRef.elementId,
            selectableValue: this.input.elementValueRef.selectableValue,
            selectableValueType: this.input.elementValueRef.selectableValueType,
            compareValueType: this.input.elementValueRef.compareValueType,
            compareValueFormula: this.input.elementValueRef.compareValueFormula
        });
        this.updateDefinitionValues();
    }

    pushElementValueRef(value) {
        if (this.elementValueRefAlreadyExists(value)) {
            return;
        }
        this.values.elementValueRefs.push(value);
    }

    removeElementValueRef(index) {
        this.values.elementValueRefs.splice(index, 1);
        this.updateDefinitionValues();
    };

    elementValueRefAlreadyExists(value) {
        for (let i = 0; i < this.values.elementValueRefs.length; i++) {
            const elementValueRef = this.values.elementValueRefs[i];

            if (
                elementValueRef.sectionId === value.sectionId &&
                elementValueRef.elementId === value.elementId &&
                elementValueRef.selectableValue === value.selectableValue &&
                elementValueRef.selectableValueType === value.selectableValueType &&
                elementValueRef.compareValueType === value.compareValueType
            ) {
                return true;
            }
        }
        return false;
    }

    getSectionIdentifier(sectionId) {
        if (this.sectionIdentifiers[sectionId]) {
            return this.sectionIdentifiers[sectionId];
        }
        return sectionId;
    }

    getElementIdentifier(elementId) {
        if (this.elementIdentifiers[elementId]) {
            return this.elementIdentifiers[elementId];
        }
        return elementId;
    }

    findById(values, id) {
        for (let i in values) {
            if (values.hasOwnProperty(i) && values[i].id === id) {
                return values[i];
            }
        }
        return null;
    }

    updateDefinitionValues() {
        this.setDefinitionValues(this.values);
    }
}

FloatInputElementDefinitionController.$inject = FloatInputElementDefinitionControllerInject;

const FloatInputElementDefinitionComponent = {
    bindings: {
        definitionValidation: '&',
        productId: '<',
        sectionId: '<',
        element: '<'
    },
    template: FloatInputElementDefinitionTemplate,
    controller: FloatInputElementDefinitionController
};

export default ['floatInputElementDefinition', FloatInputElementDefinitionComponent];