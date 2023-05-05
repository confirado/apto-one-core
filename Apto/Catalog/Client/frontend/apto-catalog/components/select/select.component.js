import ContainerController from 'apto-base/components/apto-container.controller';
import Template from './select.component.html';

const ControllerInject = ['$ngRedux', 'LanguageFactory', 'ConfigurationService'];
class Controller extends ContainerController {
    constructor($ngRedux, LanguageFactory, ConfigurationService) {
        // parent constructor
        super($ngRedux);

        // services
        this.languageFactory = LanguageFactory;
        this.configurationService = ConfigurationService;

        // set default values
        this.minQuantity = 1;
        this.maxQuantity = 100;
        this.stepQuantity = 1;
    }

    connectProps() {
        return (state) => {
            // state mapping object
            return {
                minPurchase: state.product.productDetail.minPurchase,
                maxPurchase: state.product.productDetail.maxPurchase,
                contentSnippetAptoSelect: state.index.contentSnippets.aptoSelect
            }
        }
    }

    onStateChange(state) {
        this.state = state;
        this.updateOptions();
        this.updateSelected();
    }

    changeSelected() {
        this.onChangeSelected({
            quantity: this.selected
        });
        this.configurationService.fetchCurrentStatePrice();
        this.configurationService.fetchComputedProductValues();
    }

    updateSelected() {
        if (null !== this.selectedInput) {
            this.selected = this.selectedInput;
            return;
        }

        if (typeof this.options !== "undefined") {
            this.selected = this.options[0];
            return;
        }

        this.selected = {
            value: this.minQuantity,
            name: this.minQuantity + ' ' + this.getSuffix()
        }
    }

    updateOptions() {
        this.options = [];
        for (let i = this.minQuantity; i <= this.maxQuantity; i += this.stepQuantity) {
            this.options.push({
                value: i,
                name: i + ' ' + this.getSuffix()
            });
        }
    }

    getSuffix() {
        if (this.selectedSuffix) {
            return this.selectedSuffix;
        }

        if (this.state.contentSnippetAptoSelect) {
            return this.languageFactory.translate(this.state.contentSnippetAptoSelect.suffix);
        }

        return '';
    }

    $onInit() {
        // call parent init
        super.$onInit();

        // set min quantity
        this.minQuantity = this.state.minPurchase ? this.state.minPurchase : 1;

        // set max quantity
        this.maxQuantity = this.state.maxPurchase ? this.state.maxPurchase : 100;

        // update options
        this.updateOptions();

        // update selected
        this.updateSelected();

        // broadcast
        this.onChangeSelected({
            quantity: this.selected
        });
    };

    $onChanges(changes) {
        if (changes.selectedInput) {
            this.updateSelected();
        }

        if (changes.selectedSuffix) {
            this.updateOptions();
            this.updateSelected();
        }
    }
}

Controller.$inject = ControllerInject;

const Select = {
    template: Template,
    controller: Controller,
    bindings: {
        selectedInput: '<selected',
        selectedSuffix: '@suffix',
        onChangeSelected: '&'
    }
};

export default ['aptoSelect', Select];