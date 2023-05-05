import Presentational from 'apto-base/components/apto-presentational.controller.js';
import Template from './product-list-filter.component.html';

const ControllerInject = ['LanguageFactory', 'SnippetFactory'];
class Controller extends  Presentational {
    constructor(LanguageFactory, SnippetFactory) {
        super(LanguageFactory);
        this.snippetFactory = SnippetFactory;
        this.searchString = '';
    }

    onToggleCategory($event, category) {
        this.onUpdateCategoryFilter({
            category: {
                id: category.id,
                checked: !category.checked
            }
        });
    }

    onSearchSubmit() {
        this.onUpdateSearchString({
            searchString: this.searchString
        });
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get('aptoProductList.' + path, trustAsHtml);
    }
}



Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        categoryFilter: '<',
        products: '<',
        onUpdateCategoryFilter: '&',
        onUpdateSearchString: '&'
    },
    template: Template,
    controller: Controller
};

export default ['aptoProductListFilter', Component];
