import PartsListElementDefinitionTemplate from './parts-list-element-definition.component.html';
import Presentational from 'apto-base/components/apto-presentational.controller.js';

const PartsListElementDefinitionControllerInject = ['$ngRedux', 'ElementActions', 'MessageBusFactory', 'LanguageFactory'];
class PartsListElementDefinitionController extends Presentational {
    constructor($ngRedux, ElementActions, MessageBusFactory, LanguageFactory) {
        super(LanguageFactory);
        this.values = {
            category: null,
            multiple: false
        };
        this.categories = [];

        this.mapStateToThis = function(state) {
            return {
                detailDefinition: state.element.detail.definition,
                definitionValues: state.element.definition.values
            }
        };

        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues
        })(this);

        MessageBusFactory.query('FindCategories', ['']).then((result) => {
            this.categories = result.data.result.data;
        });
    }

    $onInit = function () {
        if (this.detailDefinition.class == 'Apto\\Plugins\\PartsListElement\\Domain\\Core\\Model\\Product\\Element\\PartsListElementDefinition') {
            if (this.detailDefinition.json.category) {
                this.values.category = this.detailDefinition.json.category;
            }

            if (this.detailDefinition.json.hasOwnProperty('multiple')) {
                this.values.multiple = this.detailDefinition.json.multiple;
            }

            this.setDefinitionValues(this.values);
        }

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    if (this.values.hasOwnProperty('category') && (this.values.category !== null && typeof this.values.category !== 'string')) {
                        return false;
                    }

                    if (this.values.hasOwnProperty('multiple') && typeof this.values.multiple !== 'boolean') {
                        return false;
                    }

                    this.setDefinitionValues(this.values);
                    return true;
                }
            }
        });
    }

    $onChanges = function (changes) {
    }

    $onDestroy() {
        this.unSubscribeActions();
    };
}

PartsListElementDefinitionController.$inject = PartsListElementDefinitionControllerInject;

const PartsListElementDefinitionComponent = {
    bindings: {
        definitionValidation: '&'
    },
    template: PartsListElementDefinitionTemplate,
    controller: PartsListElementDefinitionController
};

export default ['partsListElementDefinition', PartsListElementDefinitionComponent];
