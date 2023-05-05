import Controller from './apto.controller';

const PresentationalControllerInject = ['LanguageFactory'];
class PresentationalController extends Controller {
    constructor(LanguageFactory) {
        // call parent constructor
        super();

        // set services functions
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
    }
}

PresentationalController.$inject = PresentationalControllerInject;
export default PresentationalController;