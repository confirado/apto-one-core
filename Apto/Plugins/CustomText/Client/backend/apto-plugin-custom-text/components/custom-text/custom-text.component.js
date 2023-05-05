import CustomTextTemplate from './custom-text.component.html';

const CustomTextControllerInject = ['$ngRedux', 'ElementActions'];
class CustomTextController {
    constructor($ngRedux, ElementActions) {
        this.text= {
            minLength: 0,
            maxLength: 1,
        };

        this.values = {
            text: [],
            rendering: 'input',
            placeholder: [],
            renderDialogInOnePageDesktop: true
        };

        this.renderings = [{
            id: 'input',
            label: 'Einzeiliger Text'
        }, {
            id: 'textarea',
            label: 'Mehrzeiliger Text'
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
    };

    $onInit() {
        if (this.detailDefinition.class == 'Apto\\Plugins\\CustomText\\Domain\\Core\\Model\\Product\\Element\\CustomTextDefinition') {
            const textCollection = this.detailDefinition.json.text.json.collection;

            for (let iText = 0; iText < textCollection.length; iText++) {
                this.pushTextValue(textCollection[iText].json);
            }

            this.values.rendering = this.detailDefinition.json.rendering;
            this.values.renderDialogInOnePageDesktop = this.detailDefinition.json.renderDialogInOnePageDesktop;

            if (typeof this.values.rendering === "undefined") {
                this.values.rendering = 'input';
            }

            if (this.detailDefinition.json.placeholder) {
                this.values.placeholder = this.detailDefinition.json.placeholder;
            }

            if (typeof this.values.renderDialogInOnePageDesktop === "undefined") {
                this.values.renderDialogInOnePageDesktop = true;
            }

            this.setDefinitionValues(this.values);
        }

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    if(this.values.text.length < 1) {
                        return false;
                    }
                    this.setDefinitionValues(this.values);
                    return true;
                }
            }
        });
    }

    pushTextValue(value) {
        this.values.text.push(value);
    }

    addTextValue() {
        this.pushTextValue({
            minLength: this.text.minLength,
            maxLength: this.text.maxLength
        });
        this.setDefinitionValues(this.values);
    };

    removeTextValue(index) {
        this.values.text.splice(index, 1);
        this.setDefinitionValues(this.values);
    };

    $onDestroy() {
        this.unSubscribeActions();
    };
}

CustomTextController.$inject = CustomTextControllerInject;

const CustomTextComponent = {
    bindings: {
        definitionValidation: '&'
    },
    template: CustomTextTemplate,
    controller: CustomTextController
};

export default ['customText', CustomTextComponent];
