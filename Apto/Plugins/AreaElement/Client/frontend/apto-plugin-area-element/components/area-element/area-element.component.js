import Template from './area-element.component.html';
import DialogTemplate from './area-element.dialog.html';

const ControllerInject = ['$ngRedux', 'ngDialog', 'LanguageFactory', 'ConfigurationService'];
class Controller {
    constructor($ngRedux, ngDialog, LanguageFactory, ConfigurationService) {
        this.ngRedux = $ngRedux;
        this.ngDialog = ngDialog;
        this.translate = LanguageFactory.translate;
        this.elementIsSelected = ConfigurationService.elementIsSelected;
        this.elementIsDisabled = ConfigurationService.elementIsDisabled;

        this.reduxProps = {};
        this.reduxActions = {};

        this.boundedEvents = [];
    };

    $onInit() {
        this.element = this.elementInput;

        if (typeof this.sectionInput !== "undefined") {
            this.section = this.sectionInput;
        } else {
            this.section = this.sectionCtrlInput;
        }

        this.staticValues = this.element.definition.staticValues;
        this.boundedEvents.push(
            this.reduxConnect()
        );
        this.input = angular.copy(this.reduxProps);
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
        return {
            useStepByStep: state.product.productDetail.useStepByStep,
            shopSession: state.index.shopSession
        }
    }

    openInputDialog() {
        this.ngDialog.open({
            data: {
                title: this.translate(this.element.name),
                section: this.section,
                element: this.element
            },
            template: DialogTemplate,
            className: 'ngdialog-theme-default',
            plain: true
        });
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

export default ['aptoAreaElement', Component];