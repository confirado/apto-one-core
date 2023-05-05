import FavoriteDesignsTemplate from './favorite-designs.component.html';

const FavoriteDesignsControllerInject = ['$ngRedux', 'ConfigurationActions', 'SnippetFactory'];
const FavoriteDesignsController = function($ngRedux, ConfigurationActions, SnippetFactory) {
    const self = this;

    function mapStateToThis(state) {
        return {
            designs: state.favoriteDesigns.designs,
            productId: state.configuration.present.productId
        }
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {
        fetchProposedConfigurations: ConfigurationActions.fetchProposedConfigurations
    })(self);

    function snippet(path) {
        return SnippetFactory.get('aptoFavoriteDesigns.' + path);
    }

    self.snippet = snippet;
    self.$onInit = function () {
        self.fetchProposedConfigurations(self.productId);
    };

    self.$onDestroy = function () {
        reduxSubscribe();
    };
};

const FavoriteDesigns = {
    template: FavoriteDesignsTemplate,
    controller: FavoriteDesignsController
};

FavoriteDesignsController.$inject = FavoriteDesignsControllerInject;

export default ['aptoFavoriteDesigns', FavoriteDesigns];