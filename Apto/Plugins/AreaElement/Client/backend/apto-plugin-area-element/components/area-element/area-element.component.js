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
        this.fieldValue = {
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
                detailDefinition: state.element.detail.definition
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

            // init sumOfFieldValues
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

    addSumOfFieldValues() {
        this.sumOfFieldValues.push({
            minimum: this.fieldValue.minimum,
            maximum: this.fieldValue.maximum,
            step: this.fieldValue.step
        });
    }

    removeSumOfFieldValues(fieldIndex) {
        this.sumOfFieldValues.splice(fieldIndex, 1);
    }

    addFieldValue(index) {
        this.pushFieldValue(index, {
            minimum: this.fieldValue.minimum,
            maximum: this.fieldValue.maximum,
            step: this.fieldValue.step
        });
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