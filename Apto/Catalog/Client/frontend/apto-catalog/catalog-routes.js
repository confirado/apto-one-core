import HomeTemplate from './pages/home/home.controller.html';
import ListTemplate from './pages/list/list.controller.html';
import ProductTemplate from './pages/product/product.controller.html';
import ProductInlineTemplate from './pages/product/product-inline.controller.html';

const AngularRoutes = ['APTO_ANGULAR_ROUTES', {
    'all': {
        '/': {
            template: ListTemplate,
            controller: 'AptoCatalogListController'
        },
        '/home': {
            template: HomeTemplate,
            controller: 'AptoCatalogHomeController'
        },
        '/list': {
            template: ListTemplate,
            controller: 'AptoCatalogListController'
        },
        '/product/:productId': {
            template: ProductTemplate,
            controller: 'AptoCatalogProductController'
        },
        '/configuration/:configurationType/:configurationId': {
            template: ProductTemplate,
            controller: 'AptoCatalogProductController'
        }
    }
}];

if (typeof AptoInline !== "undefined") {
    AngularRoutes[1].all['/'].template = ProductInlineTemplate;
    AngularRoutes[1].all['/'].controller = 'AptoCatalogProductController';

    AngularRoutes[1].all['/product/:productId'].template = ProductInlineTemplate;
    AngularRoutes[1].all['/product/:productId'].controller = 'AptoCatalogProductController';

    AngularRoutes[1].all['/configuration/:configurationType/:configurationId'].template = ProductInlineTemplate;
    AngularRoutes[1].all['/configuration/:configurationType/:configurationId'].controller = 'AptoCatalogProductController';
}

export default [AngularRoutes];