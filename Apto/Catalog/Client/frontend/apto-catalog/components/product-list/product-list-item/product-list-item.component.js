import Presentational from 'apto-base/components/apto-presentational.controller.js';
import Template from './product-list-item.component.html';

const ControllerInject = ['LanguageFactory'];

class Controller extends Presentational {
    getProductUrl() {
        if (!this.product) {
            return null;
        }

        const productUrl = this.product.seoUrl ? this.product.seoUrl : this.product.id;
        return APTO_API.root + '/#!/product/' + productUrl;
    }

    getPreviewImage() {
        if (this.product.previewImage) {
            return APTO_API.media + this.product.previewImage;
        }

        return false;
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        product: '<'
    },
    template: Template,
    controller: Controller
};

export default ['aptoProductListItem', Component];