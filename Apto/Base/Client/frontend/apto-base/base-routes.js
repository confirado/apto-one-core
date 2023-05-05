import PageNotFoundTemplate from './pages/page-not-found/page-not-found.controller.html';

const AngularRoutes = ['APTO_ANGULAR_ROUTES', {
    'all': {
        '/404': {
            template: PageNotFoundTemplate,
            controller: 'AptoBasePageNotFoundController'
        }
    }
}];

export default [AngularRoutes];