const AptoCatalogProductControllerInject = ['$scope', '$ngRedux', 'ConfigurationService', 'IndexActions', 'SnippetFactory', 'RouteAccessFactory'];
const AptoCatalogProductController = function ($scope, $ngRedux, ConfigurationService, IndexActions, SnippetFactory, RouteAccessFactory) {
    const redux = { state: null, actions: null };

    function mapStateToThis(state) {
        return {
            product: state.configuration.present.raw.product,
            productView: state.configuration.present.productView,
            initialized: state.configuration.present.initialized
        }
    }

    const reduxUnSubscribe = $ngRedux.connect(mapStateToThis, {
        setMetaData: IndexActions.setMetaData
    })((selectedState, actions) => {
        redux.state = selectedState;
        redux.actions = actions;

        setMetaData();
    });

    function setMetaData() {
        let title = snippet('productNotFoundMetaTitle', true),
            description = snippet('productNotFoundMetaDescription', true);

        if(redux.state.product !== null) {
            title = redux.state.product.metaTitle;
            description =  redux.state.product.metaDescription
        }

        redux.actions.setMetaData({
            title: title,
            description: description
        });
    }

    function snippet(path, getNode = false, category= 'aptoProduct.', trustAsHtml = false) {
        if(getNode){
            return SnippetFactory.getNode(category + path);
        }
        return SnippetFactory.get(category + path, trustAsHtml);
    }

    ConfigurationService.init();

    $scope.snippet = snippet;
    $scope.$on('$destroy', reduxUnSubscribe);
    $scope.redux = redux;
    $scope.routeAccess = RouteAccessFactory.routeAccess;
};

AptoCatalogProductController.$inject = AptoCatalogProductControllerInject;

export default ['AptoCatalogProductController', AptoCatalogProductController];
