import AptoOnePageTemplate from './one-page.component.html';

const OnePageControllerInject = ['$ngRedux', 'ProductActions', 'ConfigurationService', 'SnippetFactory', 'LanguageFactory'];
class OnePageController {
    constructor ($ngRedux, ProductActions, ConfigurationService, SnippetFactory, LanguageFactory) {
        this.snippetFactory = SnippetFactory;
        this.translate = LanguageFactory.translate;
        this.mapStateToThis = function(state) {
            return {
                productDetail: state.product.productDetail,
                selectedSection: state.product.selectedSection
            }
        };

        this.reduxUnsubscribe = $ngRedux.connect(this.mapStateToThis, {
            selectSection: ProductActions.selectSection
        })(this);

        this.addProposedConfiguration = ConfigurationService.addProposedConfiguration;
    }

    snippet(path) {
        return this.snippetFactory.get('aptoOnePage.' + path, true);
    }

    $onChanges(changes) {
    }

    $onDestroy() {
        this.reduxUnsubscribe();
    };
}

OnePageController.$inject = OnePageControllerInject;

const OnePageComponent = {
    bindings: {
    },
    template: AptoOnePageTemplate,
    controller: OnePageController
};

export default ['aptoOnePage', OnePageComponent];