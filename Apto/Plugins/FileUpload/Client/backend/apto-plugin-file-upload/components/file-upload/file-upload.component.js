import Template from './file-upload.component.html';

const ControllerInject = ['$ngRedux', 'ElementActions'];
class Controller {
    constructor($ngRedux, ElementActions) {
        this.newAllowedFileType = '';

        this.values = {
            file: {
                maxFileSize: 4,
                allowedFileTypes: ['jpg']
            },
            needsValue: false,
            value: [],
            valuePrefix: [],
            valueSuffix: []
        };

        this.input = {
            value: {
                minimum: 0,
                maximum: 1,
                step: 1
            }
        };

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
        if (this.detailDefinition.class == 'Apto\\Plugins\\FileUpload\\Domain\\Core\\Model\\Product\\Element\\FileUploadDefinition') {

            this.values.file = this.detailDefinition.json.file;
            this.values.file.maxFileSize = parseInt(this.values.file.maxFileSize);

            if (this.detailDefinition.json.needsValue) {
                this.values.needsValue = this.detailDefinition.json.needsValue;
            }

            if (this.detailDefinition.json.value) {
                const valueCollection = this.detailDefinition.json.value.json.collection;

                for (let iValue = 0; iValue < valueCollection.length; iValue++) {
                    this.pushValue(valueCollection[iValue].json);
                }
            }

            if (this.detailDefinition.json.valuePrefix) {
                this.values.valuePrefix = this.detailDefinition.json.valuePrefix;
            }

            if (this.detailDefinition.json.valueSuffix) {
                this.values.valueSuffix = this.detailDefinition.json.valueSuffix;
            }

            this.setDefinitionValues(this.values);
        }

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    this.setDefinitionValues(this.values);
                    return true;
                }
            }
        });
    }

    pushAllowedFileType(value) {
        this.values.file.allowedFileTypes.push(value);
    }

    addAllowedFileTypeValue() {
        this.pushAllowedFileType(this.newAllowedFileType);
        this.newAllowedFileType = '';
    }

    removeAllowedFileTypeValue(index) {
        this.values.file.allowedFileTypes.splice(index, 1);
    }

    allowedFileTypeIsDuplicate(fileType) {
        const allowedFileTypes = this.values.file.allowedFileTypes;
        for (let i = 0; i < allowedFileTypes.length; i++) {
            if (fileType === allowedFileTypes[i]) {
                return true;
            }
        }

        return false;
    }

    pushValue(value) {
        this.values.value.push(value);
    }

    addValue() {
        this.pushValue({
            minimum: this.input.value.minimum,
            maximum: this.input.value.maximum,
            step: this.input.value.step
        });
        this.setDefinitionValues(this.values);
    };

    removeValue(index) {
        this.values.value.splice(index, 1);
        this.setDefinitionValues(this.values);
    };

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

export default ['aptoFileUploadElement', Component];