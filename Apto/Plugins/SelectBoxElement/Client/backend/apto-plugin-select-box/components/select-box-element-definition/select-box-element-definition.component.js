import SelectBoxElementDefinitionTemplate from './select-box-element-definition.component.html';
import SelectBoxItemDialogTemplate from './select-box-item.dialog.html';
import SelectBoxItemDialogController from './select-box-item.dialog';
import { Promise } from 'es6-promise';
import { BigNumber } from 'bignumber.js';

const SelectBoxElementDefinitionControllerInject = ['$ngRedux', '$mdDialog', 'LanguageFactory', 'ElementActions', 'SelectBoxDefinitionActions'];
class SelectBoxElementDefinitionController {
    constructor($ngRedux, $mdDialog, LanguageFactory, ElementActions, SelectBoxDefinitionActions) {
        this.newItem = {
            name: []
        };

        this.range = {
            min: null,
            max: null,
            step: null,
            prefix: {},
            suffix: {},
            positiveSign: false,
            decimalPlaces: 2
        };

        this.values = {
            defaultItem: null,
            enableMultiplier: false,
            enableMultiSelect: false,
            multiplierPrefix: {},
            multiplierSuffix: {}
        };

        this.mapStateToThis = function(state) {
            return {
                selectBoxItems: state.selectBoxDefinition.selectBoxItems,
                detailDefinition: state.element.detail.definition,
                definitionValues: state.element.definition.values,
                languages: state.index.languages
            }
        };

        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues,
            fetchSelectBoxItems: SelectBoxDefinitionActions.fetchSelectBoxItems,
            saveSelectBoxItemDetail: SelectBoxDefinitionActions.saveSelectBoxItemDetail,
            addSelectBoxItems: SelectBoxDefinitionActions.addSelectBoxItems,
            removeSelectBoxItem: SelectBoxDefinitionActions.removeSelectBoxItem,
            removeSelectBoxItems: SelectBoxDefinitionActions.removeSelectBoxItems,
            setSelectBoxItemIsDefaultAction: SelectBoxDefinitionActions.setSelectBoxItemIsDefault
        })(this);

        this.languageFactory = LanguageFactory;
        this.translate = LanguageFactory.translate;
        this.$mdDialog = $mdDialog;
    };

    $onInit() {
        if (this.detailDefinition.class === 'Apto\\Plugins\\SelectBoxElement\\Domain\\Core\\Model\\Product\\Element\\SelectBoxElementDefinition') {
            this.values.defaultItem = this.detailDefinition.json.defaultItem;
            this.values.enableMultiplier = this.detailDefinition.json.enableMultiplier;
            this.values.enableMultiSelect = this.detailDefinition.json.enableMultiSelect;
            this.values.multiplierPrefix = this.detailDefinition.json.multiplierPrefix;
            this.values.multiplierSuffix = this.detailDefinition.json.multiplierSuffix;
            this.setDefinitionValues(this.values);
        }

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    //@todo maybe check, if at least one option does exist?
                    return true;
                }
            }
        });

        this.fetchItems();
    }

    $onDestroy() {
        this.unSubscribeActions();
    };

    addItem() {
        this.addItemByName(this.newItem.name).then(
            this.fetchItems.bind(this),
            this.fetchItems.bind(this)
        );
        this.newItem.name = [];
    }

    addItemByName(name) {
        return this.saveSelectBoxItemDetail({
            productId: this.productId,
            sectionId: this.sectionId,
            elementId: this.element.id,
            name: name
        });
    }

    removeItem(id) {
        this.removeSelectBoxItem(id).then(
            this.fetchItems.bind(this),
            this.fetchItems.bind(this)
        );
    }

    openItemDetailDialog($event, selectBoxItemId) {
        const parentEl = angular.element(document.body);
        this.$mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            clickOutsideToClose: true,
            fullscreen: true,
            multiple: true,
            locals: {
                productId: this.productId,
                sectionId: this.sectionId,
                element: this.element,
                selectBoxItemId: selectBoxItemId
            },
            template: SelectBoxItemDialogTemplate,
            controller: SelectBoxItemDialogController
        }).then(
            this.fetchItems.bind(this),
            this.fetchItems.bind(this)
        );
    }

    fetchItems() {
        this.fetchSelectBoxItems(this.element.id).then(() => {
            for (let i = 0; i < this.selectBoxItems.length; i++) {
                const item = this.selectBoxItems[i];

                if (true === item.isDefault) {
                    this.values.defaultItem = {
                        id: item.id,
                        elementId: item.elementId,
                        name: item.name
                    };
                    this.setDefinitionValues(this.values);
                    return;
                }
            }

            // if no default item is found set default item to null
            this.values.defaultItem = null;
            this.setDefinitionValues(this.values);
        });
    }

    setSelectBoxItemIsDefault(item) {
        this.setSelectBoxItemIsDefaultAction(item.elementId, item.id, item.isDefault).then((response) => {
            this.fetchItems();
        });
    }

    hasTranslation(value) {
        // no object given, can't be a translated value
        if (typeof value !== 'object') {
            return false;
        }

        // check if at least one translation is not empty
        for (let i in value) {
            if (value.hasOwnProperty(i)) {
                if (value[i].length > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    addRange() {
        const ranges  = this.getRangeItems();

        this.addSelectBoxItems(
            this.productId,
            this.sectionId,
            this.element.id,
            ranges
        ).then(
            this.fetchItems.bind(this),
            this.fetchItems.bind(this)
        );
    }

    getRangeItems() {
        let items = [];

        for (let i = this.range.min; i <= this.range.max; i += this.range.step) {
            items.push(this.getRangeTranslatedValue(i));
        }

        return items;
    }

    getRangeTranslatedValue(value) {
        // define variables
        let translatedValue = {};
        let numberValue = new BigNumber('' + value);

        // set format config
        BigNumber.config({
            FORMAT: {
                groupSeparator: '.',
                groupSize: 3,

                decimalSeparator: ',',
                secondaryGroupSize: 2,

                fractionGroupSeparator: '',
                fractionGroupSize: 0
            }
        });

        // round value
        numberValue = numberValue.decimalPlaces(
            this.range.decimalPlaces,
            BigNumber.ROUND_HALF_UP
        );

        // round and format value to eg: 12345.13 -> 12.345,13
        let strValue = numberValue.toFormat(this.range.decimalPlaces);

        // set value for all languages
        for (let i = 0; i < this.languages.length; i++) {
            const language = this.languages[i];

            if (numberValue.isPositive() && this.range.positiveSign) {
                translatedValue[language.isocode] = '+' + strValue;
            }
            else {
                translatedValue[language.isocode] = '' + strValue;
            }
        }

        // add prefix for each languages defined in prefix
        for (let locale in this.range.prefix) {
            if (!this.range.prefix.hasOwnProperty(locale) || !this.range.prefix[locale]) {
                continue;
            }

            translatedValue[locale] = this.range.prefix[locale] + ' ' + translatedValue[locale];
        }

        // add suffix for each languages defined in suffix
        for (let locale in this.range.suffix) {
            if (!this.range.suffix.hasOwnProperty(locale) || !this.range.suffix[locale]) {
                continue;
            }

            translatedValue[locale] = translatedValue[locale] + ' ' + this.range.suffix[locale];
        }

        // return translatedValue
        return translatedValue;
    }

    removeItems() {
        this.removeSelectBoxItems(this.getItemIds()).then(
            this.fetchItems.bind(this),
            this.fetchItems.bind(this)
        );
    }

    getItemIds() {
        let ids = [];

        for (let i = 0; i < this.selectBoxItems.length; i++) {
            const selectBoxItem = this.selectBoxItems[i];
            ids.push(selectBoxItem.id);
        }

        return ids;
    }
}

SelectBoxElementDefinitionController.$inject = SelectBoxElementDefinitionControllerInject;

const SelectBoxElementDefinitionComponent = {
    bindings: {
        definitionValidation: '&',
        productId: '<',
        sectionId: '<',
        element: '<'
    },
    template: SelectBoxElementDefinitionTemplate,
    controller: SelectBoxElementDefinitionController
};

export default ['selectBoxElementDefinition', SelectBoxElementDefinitionComponent];