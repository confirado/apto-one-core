import ContainerController from '../apto-container.controller';
import CustomPropertiesTemplate from './custom-properties.component.html';

const CustomPropertiesControllerInject = ['$ngRedux', 'LanguageFactory', 'CustomPropertyActions', 'MessageBusFactory'];
class CustomPropertiesController extends ContainerController {
    constructor ($ngRedux, LanguageFactory, CustomPropertyActions, MessageBusFactory) {
        super($ngRedux);

        this.languageFactory = LanguageFactory;
        this.customPropertyActions = CustomPropertyActions;
        this.messageBusFactory = MessageBusFactory;

        this.conditionSets = [];
        this.properties = [];
        this.property = {
            id: null,
            key: null,
            value: null,
            translatable: false,
            productConditionId: null
        };
        this.keys = {
            text: '',
            item: null
        };
    }

    connectProps() {
        return (state) => {
            // state mapping object
            return {
                usedCustomPropertyKeys: state.customProperty.usedCustomPropertyKeys
            }
        }
    }

    connectActions() {
        // actions mapping object
        return {
            fetchUsedCustomPropertyKeys: this.customPropertyActions.fetchUsedCustomPropertyKeys
        }
    }

    $onInit() {
        super.$onInit();
        this.actions.fetchUsedCustomPropertyKeys();

        if (this.productId) {
            this.messageBusFactory.query('FindConditionSets', [this.productId]).then((result) => {
                this.conditionSets = result.data.result.conditionSets;
            });
        }
    }

    keysSearchTextChanges(searchText) {
        this.property.key = searchText;
    }

    keysItemChanges(item) {
        if (!item) {
            return;
        }
        this.property.key = item;
    }

    addProperty () {
        this.onAddProperty({
            key: this.property.key,
            value: this.property.value,
            translatable: this.property.translatable,
            productConditionId: this.property.productConditionId
        });
    }

    editProperty (property) {
        this.keys.item = property.key;
        this.property = {
            id: property.id,
            key: property.key,
            value: property.value,
            translatable: property.translatable,
            productConditionId: property.productConditionId
        };
    }

    removeProperty (id) {
        this.onRemoveProperty({
            id: id
        });
    }

    onChangeTranslatable() {
        if (this.property.translatable) {
            try {
                // try json decode
                this.property.value = JSON.parse(this.property.value);
                if (typeof this.property.value !== 'object') {
                    this.property.value = this.getTranslatedValue(this.property.value);
                }
            } catch (e){
                // if json decode fail, create new translated value
                this.property.value = this.getTranslatedValue(this.property.value);
            }
        } else {
            this.property.value = JSON.stringify(this.property.value);
        }
    }

    getTranslatedValue(value) {
        let translatedValue = {};
        translatedValue[this.languageFactory.getIsoCode()] = value;
        return translatedValue;
    }

    getConditionName = function (id) {
        const condition = this.conditionSets.find((c) => c.id === id);

        return condition ? condition.identifier : null;
    }

    $onChanges = function (changes) {
        if (changes.properties) {
            this.properties = changes.properties.currentValue;
        }
    };
}

CustomPropertiesController.$inject = CustomPropertiesControllerInject;

const CustomPropertiesComponent = {
    bindings: {
        properties: '<',
        productId: '<',
        onAddProperty: '&',
        onRemoveProperty: '&'
    },
    template: CustomPropertiesTemplate,
    controller: CustomPropertiesController
};

export default ['aptoCustomProperties', CustomPropertiesComponent];
