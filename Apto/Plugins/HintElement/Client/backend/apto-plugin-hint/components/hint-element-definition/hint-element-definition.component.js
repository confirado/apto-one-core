import HintElementDefinitionTemplate from './hint-element-definition.component.html';

const HintElementDefinitionControllerInject = ['$ngRedux', 'ElementActions'];
class HintElementDefinitionController {
    constructor($ngRedux, ElementActions) {
        this.values = {
            link: '',
            buttonText: [],
            openLinkInNewTab: '_blank', // _blank | _self
            active: false,
        };

        this.renderings = [{
            id: '_blank',
            label: 'Ja'
        }, {
            id: '_self',
            label: 'Nein'
        }];

        this.mapStateToThis = function(state) {
            return {
                detailDefinition: state.element.detail.definition,
                definitionValues: state.element.definition.values
            }
        };

        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues
        })(this);
    }

    $onInit = function () {
        if (this.detailDefinition.class == 'Apto\\Plugins\\HintElement\\Domain\\Core\\Model\\Product\\Element\\HintElementDefinition') {
            if (this.detailDefinition.json.link) {
                this.values.link = this.detailDefinition.json.link;
            }

            if (this.detailDefinition.json.buttonText) {
                this.values.buttonText = this.detailDefinition.json.buttonText;
            }

            if (this.detailDefinition.json.openLinkInNewTab) {
                this.values.openLinkInNewTab = this.detailDefinition.json.openLinkInNewTab;
            }

            if (this.detailDefinition.json.hasOwnProperty('active')) {
                this.values.active = this.detailDefinition.json.active;
            }

            this.setDefinitionValues(this.values);
        }

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    if (this.values.hasOwnProperty('link') && typeof this.values.link !== 'string') {
                        return false;
                    }
                    if (this.values.hasOwnProperty('buttonText') && typeof this.values.buttonText !== 'object') {
                        return false;
                    }
                    if (this.values.hasOwnProperty('openLinkInNewTab') && this.values.openLinkInNewTab !== '_blank' && this.values.buttonText === '_self') {
                        return false;
                    }
                    if (this.values.hasOwnProperty('active') && typeof this.values.active !== 'boolean') {
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

HintElementDefinitionController.$inject = HintElementDefinitionControllerInject;

const HintElementDefinitionComponent = {
    bindings: {
        definitionValidation: '&'
    },
    template: HintElementDefinitionTemplate,
    controller: HintElementDefinitionController
};

export default ['hintElementDefinition', HintElementDefinitionComponent];
