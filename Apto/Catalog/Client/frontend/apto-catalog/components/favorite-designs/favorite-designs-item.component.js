import Template from './favorite-designs-item.component.html';

const ControllerInject = ['$location', 'SnippetFactory'];
class Controller {
    constructor ($location, SnippetFactory) {
        this.snippetFactory = SnippetFactory;
        this.location = $location;
    }

    snippet(path) {
        return this.snippetFactory.get('aptoFavoriteDesignsItem.' + path, true);
    }

    loadDesign() {
        this.location.url('/configuration/proposed/' + this.item.id);
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        item: '<',
        index: '<',
        first: '<',
        middle: '<',
        last: '<',
        even: '<',
        odd: '<'
    },
    template: Template,
    controller: Controller
};

export default ['aptoFavoriteDesignsItem', Component];