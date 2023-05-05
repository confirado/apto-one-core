import WidthHeightElementTemplate from './width-height-element.component.html';

const WidthHeightElementControllerInject = ['$ngRedux', 'LanguageFactory', 'ElementActions', 'WidthHeightElementActions'];
class WidthHeightElementController {
    constructor($ngRedux, LanguageFactory, ElementActions, WidthHeightElementActions) {
        this.width = {
            minimum: 0,
            maximum: 1,
            step: 1
        };

        this.height = {
            minimum: 0,
            maximum: 1,
            step: 1
        };

        this.renderings = [{
            id: 'none',
            label: 'keine Ausgabe'
        }, {
            id: 'input',
            label: 'Eingabefeld'
        }, {
            id: 'select',
            label: 'Auswahlfeld'
        }];

        this.priceMatrix = null;
        this.priceMatrixSearchTerm = '';

        this.values = {
            width: [],
            height: [],
            priceMatrixId: '',
            prefixWidth: [],
            prefixHeight: [],
            suffixWidth: [],
            suffixHeight: [],
            livePricePrefix: [],
            livePriceSuffix: [],
            renderingWidth: '',
            renderingHeight: '',
            defaultWidth: null,
            defaultHeight: null,
            renderDialogInOnePageDesktop: true
        };

        this.mapStateToThis = function(state) {
            return {
                priceMatrices: state.widthHeightElement.priceMatrices,
                detailDefinition: state.element.detail.definition,
                definitionValues: state.element.definition.values
            }
        };

        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues,
            fetchPriceMatrices: WidthHeightElementActions.fetchPriceMatrices
        })(this);

        this.translate = LanguageFactory.translate;
    };

    $onInit() {
        if (this.detailDefinition.class === 'Apto\\Plugins\\WidthHeightElement\\Domain\\Core\\Model\\Product\\Element\\WidthHeightElementDefinition') {
            const widthCollection = this.detailDefinition.json.width.json.collection;
            const heightCollection = this.detailDefinition.json.height.json.collection;

            for (let iWidth = 0; iWidth < widthCollection.length; iWidth++) {
                this.pushWidthValue(widthCollection[iWidth].json);
            }

            for (let iHeight = 0; iHeight < heightCollection.length; iHeight++) {
                this.pushHeightValue(heightCollection[iHeight].json);
            }

            this.values.priceMatrixId = this.detailDefinition.json.priceMatrixId;

            this.values.prefixWidth = this.detailDefinition.json.prefixWidth;
            this.values.prefixHeight = this.detailDefinition.json.prefixHeight;
            this.values.suffixWidth = this.detailDefinition.json.suffixWidth;
            this.values.suffixHeight = this.detailDefinition.json.suffixHeight;

            if (this.detailDefinition.json.livePricePrefix) {
                this.values.livePricePrefix = this.detailDefinition.json.livePricePrefix;
            }
            if (this.detailDefinition.json.livePriceSuffix) {
                this.values.livePriceSuffix = this.detailDefinition.json.livePriceSuffix;
            }

            this.values.renderingWidth = this.detailDefinition.json.renderingWidth;
            this.values.renderingHeight = this.detailDefinition.json.renderingHeight;

            this.values.defaultWidth = this.detailDefinition.json.defaultWidth;
            this.values.defaultHeight = this.detailDefinition.json.defaultHeight;

            this.values.renderDialogInOnePageDesktop = this.detailDefinition.json.renderDialogInOnePageDesktop;

            if (typeof this.values.renderingWidth === "undefined") {
                this.values.renderingWidth = 'input';
            }

            if (typeof this.values.renderingHeight === "undefined") {
                this.values.renderingHeight = 'input';
            }

            if (typeof this.values.defaultWidth === "undefined") {
                this.values.defaultWidth = null;
            }

            if (typeof this.values.defaultHeight === "undefined") {
                this.values.defaultHeight = null;
            }

            if (typeof this.values.renderDialogInOnePageDesktop === "undefined") {
                this.values.renderDialogInOnePageDesktop = true;
            }

            this.setDefinitionValues(this.values);
        }

        this.fetchPriceMatrices().then(() => {
            if (this.detailDefinition.class === 'Apto\\Plugins\\WidthHeightElement\\Domain\\Core\\Model\\Product\\Element\\WidthHeightElementDefinition') {
                for (let iPriceMatrix = 0; iPriceMatrix < this.priceMatrices.length; iPriceMatrix++) {
                    if (this.detailDefinition.json.priceMatrixId === this.priceMatrices[iPriceMatrix].id) {
                        this.priceMatrix = angular.copy(this.priceMatrices[iPriceMatrix]);
                    }
                }
            }
        });

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    this.setDefinitionValues(this.values);
                    if (
                        this.values.renderingWidth !== 'none' && this.values.width.length < 1 ||
                        this.values.renderingHeight !== 'none' && this.values.height.length < 1
                    ) {
                        return false;
                    }
                    return true;
                }
            }
        });
    }

    pushWidthValue(value) {
        this.values.width.push(value);
    }

    pushHeightValue(value) {
        this.values.height.push(value);
    }

    addWidthValue() {
        this.pushWidthValue({
            minimum: this.width.minimum,
            maximum: this.width.maximum,
            step: this.width.step
        });
        this.setDefinitionValues(this.values);
    };

    addHeightValue() {
        this.pushHeightValue({
            minimum: this.height.minimum,
            maximum: this.height.maximum,
            step: this.height.step
        });
        this.setDefinitionValues(this.values);
    };

    removeWidthValue(index) {
        this.values.width.splice(index, 1);
        this.setDefinitionValues(this.values);
    };

    removeHeightValue(index) {
        this.values.height.splice(index, 1);
        this.setDefinitionValues(this.values);
    };

    onChangePriceMatrix(priceMatrix) {
        this.values.priceMatrixId = '';

        if (typeof priceMatrix !== "undefined") {
            this.values.priceMatrixId = priceMatrix.id;
        }

        this.setDefinitionValues(this.values);
    }

    $onDestroy() {
        this.unSubscribeActions();
    };
}

WidthHeightElementController.$inject = WidthHeightElementControllerInject;

const WidthHeightElementComponent = {
    bindings: {
        definitionValidation: '&'
    },
    template: WidthHeightElementTemplate,
    controller: WidthHeightElementController
};

export default ['aptoWidthHeightElement', WidthHeightElementComponent];