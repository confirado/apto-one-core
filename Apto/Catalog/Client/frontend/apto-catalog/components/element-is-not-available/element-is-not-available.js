import ElementIsNotAvailableTemplate from './element-is-not-available.html';

// This element must be shown when from product -> section -> element "nicht Verfügbar" checkbox is checked
// it shows only static value from snippets (AptoIsNotAvailableElement->buttonText) and is not represented by any definition or so.

const ElementIsNotAvailableControllerInject = ['$ngRedux', 'SnippetFactory', 'LanguageFactory'];
const ElementIsNotAvailableController = function ($ngRedux, SnippetFactory, LanguageFactory) {
    const self = this;

    function mapStateToThis(state) {
        return  {
            useStepByStep: state.configuration.present.raw.product.useStepByStep
        };
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {
    })(self);

    self.$onInit = function () {
        self.element = self.elementInput;
        self.section = self.sectionInput;
        self.translate = LanguageFactory.translate;
        self.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        self.snippet = snippet;
    };

    self.$onDestroy = function () {
        reduxSubscribe();
    };

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('AptoIsNotAvailableElement.' + path, trustAsHtml);
    }
};

const ElementIsNotAvailable = {
    template: ElementIsNotAvailableTemplate,
    controller: ElementIsNotAvailableController,
    bindings: {
        elementInput: '<element',
        sectionInput: '<section'
    }
};

ElementIsNotAvailableController.$inject = ElementIsNotAvailableControllerInject;

export default ['aptoElementIsNotAvailable', ElementIsNotAvailable];
