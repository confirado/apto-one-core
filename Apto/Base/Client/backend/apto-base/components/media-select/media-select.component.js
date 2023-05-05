import PresentationalController from '../apto-presentational.controller';
import Template from './media-select.component.html';
import DialogTemplate from './dialog/dialog.controller.html';
import DialogController from './dialog/dialog.controller';

const ControllerInject = ['$mdDialog', 'LanguageFactory', 'APTO_USER_SETTINGS'];
class Controller extends PresentationalController {
    constructor($mdDialog, LanguageFactory, APTO_USER_SETTINGS) {
        super(LanguageFactory);
        this.mdDialog = $mdDialog;
        this.inputType = 'mediaSelect';
        if (APTO_USER_SETTINGS.mediaSelectDefault) {
            this.inputType = APTO_USER_SETTINGS.mediaSelectDefault;
        }
    }

    $onInit() {
        this.path = angular.copy(this.pathInput);
        this.form = angular.copy(this.formInput);
        this.label = angular.copy(this.labelInput);
        this.required = angular.copy(this.requiredInput);
    }

    onSelectMediaFile(path) {
        this.onSelectFile({path: path});
    }

    showMediaDialog($event) {
        const parentEl = angular.element(document.body);

        this.mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: DialogTemplate,
            clickOutsideToClose: true,
            multiple: true,
            locals: {
                targetEvent: $event,
                onSelectMediaFile: this.onSelectMediaFile.bind(this)
            },
            controller: DialogController
        });
    }

    toggleInputType() {
        switch (this.inputType) {
            case 'mediaSelect': {
                this.inputType = 'manual';
                break;
            }
            case 'manual': {
                this.inputType = 'mediaSelect';
                break;
            }
            default: {
                this.inputType = 'mediaSelect';
            }
        }
    }

    getToggleInputTypeText() {
        switch (this.inputType) {
            case 'mediaSelect': {
                return 'Manuelle Eingabe';
            }
            case 'manual': {
                return 'Medienauswahl';
            }
            default: {
                return 'Manuelle Eingabe';
            }
        }
    }

    clearPath($event) {
        this.onSelectFile({path: null});
    }

    $onChanges = function (changes) {
        if (changes.pathInput) {
            this.path = angular.copy(this.pathInput);
        }

        if (changes.formInput) {
            this.form = angular.copy(this.formInput);
        }

        if (changes.labelInput) {
            this.label = angular.copy(this.labelInput);
        }

        if (changes.requiredInput) {
            this.required = angular.copy(this.requiredInput);
        }
    };
}
Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        pathInput: '<path',
        formInput: '<form',
        labelInput: '<label',
        requiredInput: '<required',
        onSelectFile: '&'
    },
    template: Template,
    controller: Controller
};

export default ['aptoMediaSelect', Component];