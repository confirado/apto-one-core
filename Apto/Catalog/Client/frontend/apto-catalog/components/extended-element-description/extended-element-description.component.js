import Template from './extended-element-description.component.html';

const ControllerInject = ['LanguageFactory'];
class Controller {
    constructor(LanguageFactory) {
        this.mediaUrl = APTO_API.media;
        this.translate = LanguageFactory.translate;
    }

    $onInit() {
        this.element = angular.copy(this.elementInput);
    }
}

Controller.$inject = ControllerInject;
const Component = {
    bindings: {
        elementInput: '<element'
    },
    template: Template,
    controller: Controller
};

export default ['aptoExtendedElementDescription', Component];