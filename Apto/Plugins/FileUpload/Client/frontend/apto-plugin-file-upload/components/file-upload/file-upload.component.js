import Presentational from 'apto-base/components/apto-presentational.controller';
import Template from './file-upload.component.html';

const ControllerInject = ['$ngRedux', 'LanguageFactory','MessageBusFactory', 'ConfigurationService', 'SnippetFactory', 'ElementValuesService'];
class Controller extends Presentational {
    constructor($ngRedux, LanguageFactory, MessageBusFactory, ConfigurationService, SnippetFactory, ElementValuesService) {
        // call parent constructor
        super(LanguageFactory);

        this.ngRedux = $ngRedux;

        // services
        this.messageBusFactory = MessageBusFactory;
        this.configurationService = ConfigurationService;
        this.snippetFactory = SnippetFactory;
        this.elementValuesService = ElementValuesService;

        // properties
        this.staticValues = null;
        this.allowedFileTypes = null;
        this.allowedMimeTypes = null;
        this.validate = null;
        this.uploadErrors = [];
        this.uploadedFile = null;

        this.input = {
            value: null
        }

        this.boundedEvents = [];
    }

    $onInit() {
        this.element = this.elementInput;
        if (typeof this.sectionInput !== "undefined") {
            this.section = this.sectionInput;
        } else {
            this.section = this.sectionCtrlInput;
        }

        this.boundedEvents.push(
            this.reduxConnect()
        );

        this.staticValues = this.element.definition.staticValues;
        this.allowedFileTypes = this.getAllowedFileTypes();
        this.allowedMimeTypes = this.staticValues.file.allowedMimeTypes.join(',');
        this.validate = {
            maxFiles: 1,
            size: {
                max: this.staticValues.file.maxFileSize + 'MB'
            },
            width: {
                max: 80000
            },
            height: {
                max: 12288
            },
            pattern: this.allowedFileTypes
        };

        // init values
        this.uploadedFile = this.configurationService.getElementPropertyValue(
            this.section.id,
            this.element.id,
            'file'
        );
    }

    $onDestroy() {
        for (let i = 0; i < this.boundedEvents.length; i++) {
            this.boundedEvents[i]();
        }
    }

    reduxConnect() {
        return this.ngRedux.connect(
            this.mapStateProps, {}
        )((selectedState, actions) => {
            this.reduxProps = selectedState;
            this.reduxActions = actions;
        });
    }

    mapStateProps(state) {
        //Micha hat sich schon beschwert, dass es eine Presentational Component ist, aber nun ist es halt so.
        return {
            useStepByStep: state.product.productDetail.useStepByStep
        }
    }

    onFileSelect($files, $invalidFiles) {
        // reset errors
        this.uploadErrors = [];

        // generate uuid
        if ($files.length === 1) {
            const file = $files[0];

            this.messageBusFactory.query('GenerateAptoUuid', []).then((response) => {
                // handle error
                if (true === response.data.message.error) {
                    this.uploadErrors = this.getUploadErrors([file]);
                    return;
                }

                // upload file
                this.uploadFile(
                    response.data.result,
                    file
                );
            }, (error) => {
                // handle error
                this.uploadErrors = this.getUploadErrors([file]);
            });
        }

        // handle error
        if ($invalidFiles.length > 0) {
            this.uploadErrors = this.getUploadErrors($invalidFiles);
        }
    }

    uploadFile(aptoUuid, file) {
        const
            timestamp = Math.round(Date.now() / 1000),
            extension = this.getExtensionFromFileName(file.name);

        let
            now = new Date(timestamp * 1000),
            year = now.getFullYear(),
            month = now.getMonth() < 9 ? ('0' + (now.getMonth() + 1)) : (now.getMonth() + 1),
            directory = '/apto-plugin-file-upload',
            path = '', uploadedFile = {};

        directory += '/' + year + '/' + month;
        path += directory + '/' + aptoUuid + '.' + extension;

        // set uploaded file
        uploadedFile = {
            orgFileName: file.name,
            directory: directory,
            fileName: aptoUuid,
            extension: extension,
            path: path
        };

        // upload file
        this.messageBusFactory.uploadCommand(
            'PluginFileUploadUploadFile',
            [aptoUuid, timestamp, extension, directory],
            [file],
            ''
        ).then((response) => {
            // handle error
            if (true === response.data.message.error) {
                this.uploadErrors = this.getUploadErrors([file]);
                return;
            }

            // set uploaded file
            this.uploadedFile = uploadedFile;
        }, (error) => {
            // handle error
            this.uploadErrors = this.getUploadErrors([file]);
        });
    }

    isFileUploadValid() {
        if (this.uploadedFile && !this.needsValue) {
            return true;
        }
        return false;
    }

    setValues() {
        if (!this.uploadedFile) {
            return
        }

        let properties = {
            aptoElementDefinitionId: this.staticValues.aptoElementDefinitionId,
            file: this.uploadedFile
        };

        if (this.staticValues.needsValue) {
            properties.value = this.input.value
        }

        this.configurationService.setElementProperties(
            this.section.id,
            this.element.id,
            properties
        );
        this.postSetActiveValue();
    }

    postSetActiveValue() {
        this.configurationService.continueWithNextSection();
    }

    removeValue() {
        this.configurationService.removeElement(this.section.id, this.element.id);
    }

    getAllowedFileTypes() {
        let fileTypes = [];

        for (let i = 0; i < this.staticValues.file.allowedFileTypes.length; i++) {
            fileTypes.push('.' + this.staticValues.file.allowedFileTypes[i]);
        }

        return fileTypes.join(',');
    }

    getExtensionFromFileName(fileName) {
        let extension = fileName.split('.');
        if (extension.length === 1 || (extension[0] === "" && extension.length === 2)) {
            return '';
        }
        return ('' + extension.pop()).toLowerCase();
    }

    getUploadErrors($invalidFiles) {
        let errors = [];

        for (let i = 0; i < $invalidFiles.length; i++) {
            const file = $invalidFiles[i];

            switch (file.$error) {
                case'pattern': {
                    errors.push({
                        type: 'pattern'
                    });
                    break;
                }
                case 'maxSize': {
                    errors.push({
                        type: 'maxSize'
                    });
                    break;
                }
                default: {
                    errors.push({
                        type: 'default'
                    });
                }
            }
        }

        return errors;
    }

    getTooltipContent() {
        for (let i = 0; i < this.element.customProperties.length; i++) {
            const property = this.element.customProperties[i];
            if (property.key === 'tool-tip') {
                return property.value;
            }
        }
        return null;
    }

    snippetGlobal(path) {
        return this.snippetFactory.get(path);
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

export default ['aptoFileUploadElement', Component];
