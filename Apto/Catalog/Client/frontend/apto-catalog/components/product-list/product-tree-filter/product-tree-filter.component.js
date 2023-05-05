import Presentational from 'apto-base/components/apto-presentational.controller.js';
import Template from './product-tree-filter.component.html';

const ControllerInject = ['LanguageFactory', 'SnippetFactory'];
class Controller extends  Presentational {
    constructor(LanguageFactory, SnippetFactory) {
        super(LanguageFactory);
        this.snippetFactory = SnippetFactory;
    }

    onChangeCategoryFilter($event, category) {
        this.onUpdateFilter({
            category: {
                id: category.id
            }
        });
    };

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get('aptoProductList.' + path, trustAsHtml);
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        categoryTree: '<',
        categoryFilter: '<',
        treeOptions: '<',
        products: '<',
        selectedNode: '<',
        expandedNodes: '<',
        onUpdateFilter: '&'
    },
    template: Template,
    controller: Controller
};

export default ['aptoProductTreeFilter', Component];