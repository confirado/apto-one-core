import HintElementDefinitionTemplate from './hint-element-definition.component.html';

const HintElementDefinitionControllerInject = ['$ngRedux', '$window', 'ConfigurationService', 'LanguageFactory'];
class HintElementDefinitionController {
    constructor($ngRedux, $window, ConfigurationService, LanguageFactory) {
        this.ngRedux = $ngRedux;
        this.window = $window;
        this.configurationService = ConfigurationService;
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        this.elementIsDisabled = this.configurationService.elementIsDisabled;

        this.reduxProps = {};
        this.reduxActions = {};
        this.boundedEvents = [];
    };

    mapStateProps(sectionId, elementId) {
        return (state) => {
            return {
                useStepByStep: state.product.productDetail.useStepByStep
            }
        }
    }

    reduxConnect() {
        return this.ngRedux.connect(
            this.mapStateProps(
                this.section.id,
                this.element.id
            ), {}
        )((selectedState, actions) => {
            this.reduxProps = selectedState;
            this.reduxActions = actions;
        });
    }

    gotoLink() {
        this.window.open(this.staticValues.link, this.staticValues.openLinkInNewTab);
    }

    $onInit() {
        this.element = angular.copy(this.elementInput);

        if (typeof this.sectionInput !== "undefined") {
            this.section = angular.copy(this.sectionInput);
        } else {
            this.section = angular.copy(this.sectionCtrlInput);
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
}

HintElementDefinitionController.$inject = HintElementDefinitionControllerInject;

const HintElementDefinitionComponent = {
    bindings: {
        elementInput: '<element',
        sectionInput: '<section',
        sectionCtrlInput: '<sectionCtrl'
    },
    template: HintElementDefinitionTemplate,
    controller: HintElementDefinitionController
};

export default ['hintElementDefinition', HintElementDefinitionComponent];