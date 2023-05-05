import Template from './route-access-failed.component.html';

const ControllerInject = ['SnippetFactory'];
class Controller {
    constructor(SnippetFactory) {
        this.snippetFactory = SnippetFactory;
    };

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get('aptoRouteAccess.' + path, trustAsHtml);
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {},
    template: Template,
    controller: Controller
};

export default ['aptoRouteAccessFailed', Component];
