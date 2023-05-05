import Container from 'apto-base/components/apto-container.controller.js';
import Template from './product-list.component.html';

const ControllerInject = ['$ngRedux', 'ProductListActions'];
class Controller extends  Container {
    constructor($ngRedux, ProductListActions) {
        super($ngRedux);

        // init actions
        this.productListActions = ProductListActions;

        // init properties
        this.categoryFilter = [];
        this.searchString = '';
    }

    connectProps() {
        return (state) => {
            // state mapping object
            return {
                products: state.productList.products,
                categories: state.productList.categories,
                filterSaved: state.productList.filterSaved
            }
        }
    }

    connectActions() {
        // actions mapping object
        return {
            fetchProductList: this.productListActions.fetchProductList,
            fetchCategories: this.productListActions.fetchCategories,
        }
    }

    onStateChange(state) {
        super.onStateChange(state);
        this.initCategoryFilter();
    }

    initCategoryFilter() {
        if (!(0 === this.categoryFilter.length && this.state.categories.length > 0)) {
            return;
        }

        for (let i = 0; i < this.state.categories.length; i++) {
            const category = this.state.categories[i];

            this.categoryFilter.push({
                id: category.id,
                name: category.name,
                position: category.position,
                checked: false
            });
        }
    }

    $onInit() {
        super.$onInit();
        if (!this.state.filterSaved) {
            this.actions.fetchCategories();
            this.updateProductList();
        }
    }

    onUpdateCategoryFilter(category) {
        for (let i = 0; i < this.categoryFilter.length; i++) {
            if (this.categoryFilter[i].id !== category.id) {
                continue;
            }

            this.categoryFilter[i].checked = category.checked;
            break;
        }
        this.updateProductList();
    }

    onUpdateSearchString(searchString) {
        this.searchString = searchString;
        this.updateProductList();
    }

    updateProductList() {
        let filter = {};

        // set searchString
        if (this.searchString) {
            filter.searchString = this.searchString;
        }

        // set categories
        for (let i = 0; i < this.categoryFilter.length; i++) {
            if (this.categoryFilter[i].checked) {
                if (!filter['categories']) {
                    filter['categories'] = [];
                }
                filter['categories'].push(this.categoryFilter[i].id);
            }
        }

        // fetch products by filter
        this.actions.fetchProductList(filter);
    }
}

Controller.$inject = ControllerInject;

const Component = {
    template: Template,
    controller: Controller
};

export default ['aptoProductList', Component];
