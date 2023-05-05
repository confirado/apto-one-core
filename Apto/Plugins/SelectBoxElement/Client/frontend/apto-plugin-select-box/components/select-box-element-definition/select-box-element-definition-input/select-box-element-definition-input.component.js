import Template from './select-box-element-definition-input.component.html';

const ControllerInject = ['$location', '$ngRedux', 'ConfigurationService', 'LanguageFactory', 'ElementValuesService', 'MessageBusFactory', 'SnippetFactory'];
class Controller {
    static getStateBoxes(state, sectionId, elementId) {
        let boxes = state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['boxes'];
        if (boxes) {
            return boxes;
        }
        return [];
    }

    constructor($location, $ngRedux, ConfigurationService, LanguageFactory, ElementValuesService, MessageBusFactory, SnippetFactory) {
        this.location = $location;
        this.ngRedux = $ngRedux;
        this.configurationService = ConfigurationService;
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        this.elementValuesService = ElementValuesService;
        this.messageBusFactory = MessageBusFactory;
        this.snippetFactory = SnippetFactory;
        this.elementIsDisabled = this.configurationService.elementIsDisabled;
        this.elementIsSelected = this.configurationService.elementIsSelected;
        this.itemId = null;
        this.items = [];
        this.itemIds = [];
        this.boxes = [];
        this.itemToAdd = null;
        this.multiToAdd = 1;
        this.done = false;
        this.updateNeeded = false;

        this.reduxProps = {
            id: '',
            title: '',
            multi: 1
        };

        this.reduxActions = {};
    };

    mapStateProps(getStateBoxes, sectionId, elementId) {
        return (state) => {
            let mapping = {
                boxes: getStateBoxes(state, sectionId, elementId),
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
                Controller.getStateBoxes,
                this.section.id,
                this.element.id
            ), {
            }
        )((selectedState, actions) => {
            this.reduxProps = selectedState;
            this.reduxActions = actions;
        });
    }

    setValues(continueWithNext) {
        if (!this.element.definition.staticValues.enableMultiSelect) {
            this.configurationService.setElementProperties(
                this.section.id,
                this.element.id,
                {
                    aptoElementDefinitionId: this.element.definition.staticValues.aptoElementDefinitionId,
                    boxes: this.boxes,
                    selectedItem: this.boxes[0].id
                },
                true,
                continueWithNext
            );
        }
        else {
            let selectedItems = "";
            for (let i = 0; i < this.boxes.length; i++) {
                if (selectedItems === "") {
                    selectedItems = this.boxes[i].id;
                }
                else {
                    selectedItems += ',' + this.boxes[i].id;
                }
            }
            this.configurationService.setElementProperties(
                this.section.id,
                this.element.id,
                {
                    aptoElementDefinitionId: this.element.definition.staticValues.aptoElementDefinitionId,
                    boxes: this.boxes,
                    selectedItem: selectedItems
                },
                true,
                continueWithNext
            );
        }

        this.updateNeeded = false;
    }

    removeValues() {
        this.configurationService.removeElement(this.section.id, this.element.id);
        this.input = angular.copy(this.reduxProps);
    }

    onChangedItem() {
        this.setValues();
    }

    onChangedSingleMulti() {
        if (this.itemToAdd) {
            this.onChangedItemToAdd();
        }
    }

    onChangedItemToAdd() {
        if (this.element.definition.staticValues.enableMultiSelect) {
            for (let i = 0; i < this.boxes.length; i++) {
                if (this.boxes[i].id === this.itemToAdd) {
                    this.boxes[i].multi = parseInt(this.boxes[i].multi) + parseInt(this.multiToAdd);
                    this.itemToAdd = null;
                    this.multiToAdd = 1;
                    this.setValues();
                    this.done = true;
                    return;
                }
            }
            this.boxes.push({
                'id': this.itemToAdd,
                'multi': this.multiToAdd ? ('' + this.multiToAdd).trim() : this.multiToAdd,
                'name': this.getItemById(this.itemToAdd).name
            })
            this.done = true;
            this.itemToAdd = null;
            this.multiToAdd = 1;
            this.setValues();
        }
        else {
            this.done = true;
            this.boxes = [
                {
                    'id': this.itemToAdd,
                    'multi': this.multiToAdd ? ('' + this.multiToAdd).trim() : this.multiToAdd,
                    'name': this.getItemById(this.itemToAdd).name
                }
            ]
        }
    }

    removeSelect(index) {
        this.boxes.splice(index, 1);
        if (this.boxes.length === 0) {
            this.done = false;
        }
        this.setValues();
    }

    onChangedMulti() {
        this.updateNeeded = true;
    }

    getItemById(id) {
        for (let i = 0; i < this.items.length; i++) {
            if (this.items[i].id === id) {
                return this.items[i];
            }
        }
    }

    getLivePriceSuffix(id) {
        if (!this.reduxProps.livePricePrices || !this.reduxProps.livePricePrices[this.element.id][id]) {
            return '';
        }

        return ' (' + this.snippetFactory.get('plugins.livePrice.label') + ' ' + this.reduxProps.livePricePrices[this.element.id][id]['formatted'] + ')';
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get(path, trustAsHtml);
    }

    $onInit() {
        this.element = this.elementInput;

        if (typeof this.sectionInput !== "undefined") {
            this.section = this.sectionInput;
        } else {
            this.section = this.sectionCtrlInput;
        }

        this.reduxDisconnect = this.reduxConnect();

        this.messageBusFactory.query('FindSelectBoxItems', [
            this.element.id
        ]).then(
            (result) => {
                // populate items and select current item if set
                this.items = result.data.result.data;

                this.boxes = this.reduxProps.boxes;
                this.preSelectedItem = null;
                for(let i = 0; i < this.items.length; i++) {
                    if (this.items[i].isDefault) {
                        this.preSelectedItem = this.items[i];
                    }
                }
                if (this.boxes.length < 1 && !this.element.definition.staticValues.enableMultiSelect && this.preSelectedItem) {
                    this.itemToAdd = this.preSelectedItem.id;
                    this.boxes.push({
                            'id': this.itemToAdd,
                            'multi': this.multiToAdd ? ('' + this.multiToAdd).trim() : this.multiToAdd,
                            'name': this.getItemById(this.itemToAdd).name
                    })
                    this.done = true;
                }
            }
        );
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

export default ['selectBoxElementDefinitionInput', Component];