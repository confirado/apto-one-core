import PricePerUnitElementDefinitionTemplate from './price-per-unit-element-definition.component.html';

const PricePerUnitElementDefinitionControllerInject = ['$ngRedux', 'ElementActions', 'PricePerUnitDefinitionActions'];
class PricePerUnitElementDefinitionController {
    constructor($ngRedux, ElementActions, PricePerUnitDefinitionActions) {

        this.availableElements = [];
        this.availableSelectableValues = [];
        this.availableComputableValues = [];
        this.availableSelectableValueTypes = ['Selectable', 'Computable'];
        this.text = {
            minLength: 0,
            maxLength: 1,
        };

        this.input = {
            elementValueRef: {
                sectionId: null,
                elementId: null,
                selectableValue: null,
                selectableValueType: null
            }
        };

        this.values = {
            sectionId: null,
            elementId: null,
            selectableValue: null,
            selectableValueType: null,
            conversionFactor: '1.0',
            minOne: false,
            textBoxEnabled: false,
            textBoxPrefix: [],
            textBoxSuffix: [],
            livePricePrefix: [],
            livePriceSuffix: [],
            text: [],
            elementValueRefs: []
        };

        this.mapStateToThis = function(state) {
            return {
                availableSections: state.pricePerUnitDefinition.sections,
                sectionIdentifiers: state.pricePerUnitDefinition.sectionIdentifiers,
                elementIdentifiers: state.pricePerUnitDefinition.elementIdentifiers,
                availableCustomerGroups: state.pricePerUnitDefinition.availableCustomerGroups,
                pricePerUnitPrices: state.pricePerUnitDefinition.pricePerUnitPrices,
                detailDefinition: state.element.detail.definition,
                definitionValues: state.element.definition.values
            }
        };

        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues,
            fetchSections: PricePerUnitDefinitionActions.fetchSections,
            fetchAvailableCustomerGroups: PricePerUnitDefinitionActions.fetchAvailableCustomerGroups,
            fetchPricePerUnitPrices: PricePerUnitDefinitionActions.fetchPricePerUnitPrices,
            addPricePerUnitPrice: PricePerUnitDefinitionActions.addPricePerUnitPrice,
            removePricePerUnitPrice: PricePerUnitDefinitionActions.removePricePerUnitPrice
        })(this);
    };

    $onInit() {
        this.isPricePerUnitElementDefinition = this.detailDefinition.class === 'Apto\\Plugins\\PricePerUnitElement\\Domain\\Core\\Model\\Product\\Element\\PricePerUnitElementDefinition';
        if (this.isPricePerUnitElementDefinition) {
            const textCollection = this.detailDefinition.json.text.json.collection;

            for (let iText = 0; iText < textCollection.length; iText++) {
                this.pushTextValue(textCollection[iText].json);
            }

            this.values.sectionId = this.detailDefinition.json.sectionId;
            this.values.elementId = this.detailDefinition.json.elementId;
            this.values.selectableValue = this.detailDefinition.json.selectableValue;
            this.values.selectableValueType = this.detailDefinition.json.selectableValueType;

            if(!this.detailDefinition.json.selectableValueType && this.values.selectableValue) {
                this.values.selectableValueType = 'Selectable';
            }

            this.values.conversionFactor = this.detailDefinition.json.conversionFactor;
            this.values.minOne = this.detailDefinition.json.minOne;
            this.values.textBoxEnabled = this.detailDefinition.json.textBoxEnabled;
            this.values.textBoxPrefix = this.detailDefinition.json.textBoxPrefix;
            this.values.textBoxSuffix = this.detailDefinition.json.textBoxSuffix;

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

            if (this.values.sectionId && this.values.elementId && this.values.selectableValue && this.values.selectableValueType) {
                this.pushElementValueRef({
                    sectionId: this.values.sectionId,
                    elementId: this.values.elementId,
                    selectableValue: this.values.selectableValue,
                    selectableValueType: this.values.selectableValueType
                });
            }

            this.updateDefinitionValues();
        }

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    this.updateDefinitionValues();
                    if (!this.isPricePerUnitElementDefinition) {
                        return true;
                    }

                    if (
                        // no section/element/selectableValue chosen
                        (this.values.elementValueRefs.length < 1) ||

                        // text enabled, but no text lengths added
                        (this.values.text.length < 1 && this.values.textBoxEnabled)
                    ) {
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
        this.fetchPricePerUnitPrices(this.element.id);
    }

    $onDestroy() {
        this.unSubscribeActions();
    };

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

    initNewPrice() {
        this.newPrice = {
            amount: '',
            currencyCode: 'EUR',
            customerGroupId: ''
        };
    }

    addPrice() {
        this.addPricePerUnitPrice(
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
        this.removePricePerUnitPrice(
            this.element.id,
            priceId
        ).then(() => {
            this.fetchPrices();
        });
    }

    fetchPrices() {
        this.fetchPricePerUnitPrices(this.element.id);
    }

    pushTextValue(value) {
        this.values.text.push(value);
    }

    addTextValue() {
        this.pushTextValue({
            minLength: this.text.minLength,
            maxLength: this.text.maxLength
        });
        this.updateDefinitionValues();
    };

    removeTextValue(index) {
        this.values.text.splice(index, 1);
        this.updateDefinitionValues();
    };

    pushElementValueRef(value) {
        if (this.elementValueRefAlreadyExists(value)) {
            return;
        }
        this.values.elementValueRefs.push(value);
    }

    addElementValueRef() {
        this.pushElementValueRef({
            sectionId: this.input.elementValueRef.sectionId,
            elementId: this.input.elementValueRef.elementId,
            selectableValue: this.input.elementValueRef.selectableValue,
            selectableValueType: this.input.elementValueRef.selectableValueType
        });
        this.updateDefinitionValues();
    }

    removeElementValueRef(index, elementValueRef) {
        this.values.elementValueRefs.splice(index, 1);
        if (
            elementValueRef.sectionId === this.values.sectionId &&
            elementValueRef.elementId === this.values.elementId &&
            elementValueRef.selectableValue === this.values.selectableValue &&
            elementValueRef.selectableValueType === this.values.selectableValueType
        ) {
            this.values.sectionId = null;
            this.values.elementId = null;
            this.values.selectableValue = null;
            this.values.selectableValueType = null;
        }
        this.updateDefinitionValues();
    }

    elementValueRefAlreadyExists(value) {
        for (let i = 0; i < this.values.elementValueRefs.length; i++) {
            const elementValueRef = this.values.elementValueRefs[i];

            if (
                elementValueRef.sectionId === value.sectionId &&
                elementValueRef.elementId === value.elementId &&
                elementValueRef.selectableValue === value.selectableValue &&
                elementValueRef.selectableValueType === value.selectableValueType
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

PricePerUnitElementDefinitionController.$inject = PricePerUnitElementDefinitionControllerInject;

const PricePerUnitElementDefinitionComponent = {
    bindings: {
        definitionValidation: '&',
        productId: '<',
        sectionId: '<',
        element: '<'
    },
    template: PricePerUnitElementDefinitionTemplate,
    controller: PricePerUnitElementDefinitionController
};

export default ['pricePerUnitElementDefinition', PricePerUnitElementDefinitionComponent];