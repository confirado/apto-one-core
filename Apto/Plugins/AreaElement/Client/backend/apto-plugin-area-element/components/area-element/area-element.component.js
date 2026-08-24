import Template from './area-element.component.html';

const ControllerInject = ['$ngRedux', 'LanguageFactory', 'ElementActions', 'AreaElementActions'];
class Controller {
    constructor($ngRedux, LanguageFactory, ElementActions, AreaElementActions) {
        // service functions
        this.translate = LanguageFactory.translate;

        // definition values
        this.renderDialogInOnePageDesktop = true;
        this.priceMatrix = {
            id: null,
            row: null,
            column: null
        };
        this.livePricePrefix = [];
        this.livePriceSuffix = [];
        this.sumOfFieldValues = [];
        this.priceMultiplication = {
            active: false,
            baseValueFormula: null,
            factor: 1
        };
        this.fields = [{
            prefix: [],
            suffix: [],
            rendering: 'input',
            default: null,
            values: []
        }];

        // local properties
        this.selectedPriceMatrix = null;
        this.priceMatrixSearchTerm = '';
        this.numberOfFields = 1;
        this.fieldTemplate = {
            prefix: [],
            suffix: [],
            rendering: 'input',
            default: null,
            values: []
        };

        // value entry model for "Felder"-Tab, unterstuetzt Computed Values
        this.fieldValue = this.getEmptyFieldValue();

        // value entry model for "Summe aller Felder"-Tab, bewusst OHNE Computed Values,
        // eigenstaendiges Objekt, damit keine Computed-Value-Auswahl aus dem Felder-Tab hier
        // versehentlich mit uebernommen wird
        this.sumOfFieldValue = {
            minimum: 0,
            maximum: 1,
            step: 1
        };

        this.fieldRenderings = [{
            id: 'input',
            label: 'Eingabefeld'
        }, {
            id: 'select',
            label: 'Auswahlfeld'
        }];
        this.redux = {};

        // redux properties
        this.mapStateToThis = function(state) {
            return {
                priceMatrices: state.areaElement.priceMatrices,
                detailDefinition: state.element.detail.definition,
                computedValues: state.product.computedValues
            }
        };

        // redux actions
        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues,
            fetchPriceMatrices: AreaElementActions.fetchPriceMatrices
        })(this.redux);
    };

    $onInit() {
        if (this.redux.detailDefinition.class === 'Apto\\Plugins\\AreaElement\\Domain\\Core\\Model\\Product\\Element\\AreaElementDefinition') {
            // init price matrix
            if (this.redux.detailDefinition.json.priceMatrix) {
                this.priceMatrix = this.redux.detailDefinition.json.priceMatrix;
            }

            // init dialog setting
            this.renderDialogInOnePageDesktop = this.redux.detailDefinition.json.renderDialogInOnePageDesktop;

            // init fields
            let fields = this.redux.detailDefinition.json.fields;
            this.fields = [];
            for (let i = 0; i < fields.length; i++) {
                const
                    field = fields[i],
                    fieldCollection = field.values.json.collection;

                // add field
                this.fields.push({
                    prefix: field.prefix,
                    suffix: field.suffix,
                    rendering: field.rendering,
                    default: field.default,
                    values: []
                });

                // add field values
                // fieldCollection[j].json enthaelt bereits das diskriminierte Format
                // { minimum: {type:'fixed'|'computed', ...}, maximum: {...}, step: N }
                for (let j = 0; j < fieldCollection.length; j++) {
                    this.pushFieldValue(i, fieldCollection[j].json);
                }
            }

            // init number of fields
            this.numberOfFields = this.fields.length;

            if (this.redux.detailDefinition.json.livePricePrefix) {
                this.livePricePrefix = this.redux.detailDefinition.json.livePricePrefix;
            }
            if (this.redux.detailDefinition.json.livePriceSuffix) {
                this.livePriceSuffix = this.redux.detailDefinition.json.livePriceSuffix;
            }

            // init sumOfFieldValues (bleibt bewusst rein numerisch, keine Computed Values)
            if (this.redux.detailDefinition.json.sumOfFieldValues) {
                const sumOfFieldValuesCollection = this.redux.detailDefinition.json.sumOfFieldValues.json.collection;
                for (let i = 0; i < sumOfFieldValuesCollection.length; i++) {
                    const fieldValue = sumOfFieldValuesCollection[i].json;
                    this.sumOfFieldValues.push({
                        minimum: fieldValue.minimum,
                        maximum: fieldValue.maximum,
                        step: fieldValue.step
                    });
                }
            }

            // init priceMultiplication
            if (this.redux.detailDefinition.json.priceMultiplication) {
                this.priceMultiplication = this.redux.detailDefinition.json.priceMultiplication;
            }
        }

        // init price matrix
        this.redux.fetchPriceMatrices().then(() => {
            if (this.redux.detailDefinition.class === 'Apto\\Plugins\\AreaElement\\Domain\\Core\\Model\\Product\\Element\\AreaElementDefinition') {
                for (let iPriceMatrix = 0; iPriceMatrix < this.redux.priceMatrices.length; iPriceMatrix++) {
                    if (this.priceMatrix.id === this.redux.priceMatrices[iPriceMatrix].id) {
                        this.selectedPriceMatrix = angular.copy(this.redux.priceMatrices[iPriceMatrix]);
                    }
                }
            }
        });

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    if (!this.assertValidValues()) {
                        return false;
                    }

                    this.setDefinitionValues();
                    return true;
                }
            }
        });
    }

    /**
     * @return {{minimum: number, minimumComputedValue: null, maximum: number, maximumComputedValue: null, step: number}}
     */
    getEmptyFieldValue() {
        return {
            minimum: 0,
            minimumComputedValue: null,
            maximum: 1,
            maximumComputedValue: null,
            step: 1
        };
    }

    /**
     * @param {number} value
     * @param {string|null} computedValueName
     * @return {{type: string, name: string}|{type: string, value: number}}
     */
    encodeBound(value, computedValueName) {
        if (computedValueName) {
            return {
                type: 'computed',
                name: computedValueName
            };
        }

        return {
            type: 'fixed',
            value: Number(value)
        };
    }

    /**
     * Fuer die Anzeige in der Werte-Tabelle. Versteht sowohl das neue diskriminierte
     * Format als auch (zur Sicherheit) rohe Altdaten-Zahlen.
     * @param bound
     * @return {string|number}
     */
    displayBound(bound) {
        if (bound && typeof bound === 'object') {
            if (bound.type === 'computed') {
                return 'Σ ' + bound.name;
            }
            return bound.value;
        }

        return bound;
    }

    /**
     * Wird per ng-change auf dem Zahlen-Input aufgerufen: manuelle Eingabe hat Vorrang,
     * eine evtl. vorher getroffene Dropdown-Auswahl fuer diese Grenze wird verworfen.
     * @param {string} bound 'minimum' | 'maximum'
     */
    onChangeManualBound(bound) {
        this.fieldValue[bound + 'ComputedValue'] = null;
    }

    addSumOfFieldValues() {
        this.sumOfFieldValues.push({
            minimum: this.sumOfFieldValue.minimum,
            maximum: this.sumOfFieldValue.maximum,
            step: this.sumOfFieldValue.step
        });
    }

    removeSumOfFieldValues(fieldIndex) {
        this.sumOfFieldValues.splice(fieldIndex, 1);
    }

    addFieldValue(index) {
        this.pushFieldValue(index, {
            minimum: this.encodeBound(this.fieldValue.minimum, this.fieldValue.minimumComputedValue),
            maximum: this.encodeBound(this.fieldValue.maximum, this.fieldValue.maximumComputedValue),
            step: this.fieldValue.step
        });

        this.fieldValue = this.getEmptyFieldValue();
    };

    pushFieldValue(index, value) {
        this.fields[index].values.push(value);
    }

    removeFieldValue(fieldIndex, valueIndex) {
        this.fields[fieldIndex].values.splice(valueIndex, 1);
    };

    onChangeNumberOfFields() {
        const difference = this.numberOfFields - this.fields.length;

        // remove fields
        if (difference < 0) {
            this.fields.splice(this.numberOfFields, 0 - difference);
        }

        // add fields
        if (difference > 0) {
            for (let i = 0; i < difference; i++) {
                this.fields.push(angular.copy(this.fieldTemplate));
            }
        }
    }

    onChangePriceMatrix(priceMatrix) {
        this.priceMatrix.id = null;

        if (typeof priceMatrix !== "undefined") {
            this.priceMatrix.id = priceMatrix.id;
        }
    }

    setDefinitionValues() {
        this.redux.setDefinitionValues({
            renderDialogInOnePageDesktop: this.renderDialogInOnePageDesktop,
            priceMatrix: this.priceMatrix,
            fields: this.fields,
            livePricePrefix: this.livePricePrefix,
            livePriceSuffix: this.livePriceSuffix,
            sumOfFieldValues: this.sumOfFieldValues,
            priceMultiplication: angular.copy(this.priceMultiplication)
        });
    }

    assertValidValues() {
        if (this.fields.length < 1) {
            return false;
        }

        for (let i = 0; i < this.fields.length; i++) {
            if (this.fields[i].values.length < 1) {
                return false;
            }
        }
        return true;
    }

    $onDestroy() {
        this.unSubscribeActions();
    };
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        definitionValidation: '&'
    },
    template: Template,
    controller: Controller
};

export default ['aptoAreaElement', Component];
