import ContainerController from '../apto-container.controller';
import Template from './language-switch.component.html';

const ControllerInject = ['$ngRedux', 'IndexActions', 'LanguageFactory', 'SnippetFactory'];
class Controller extends ContainerController {
    constructor($ngRedux, IndexActions, LanguageFactory, SnippetFactory) {
        super($ngRedux);

        // services
        this.indexActions = IndexActions;

        // service functions
        this.translate = LanguageFactory.translate;
        this.snippetFactory = SnippetFactory;


        this.menuActive = false;
    }

    connectProps() {
        return (state) => {
            // state mapping object
            return {
                languages: state.index.languages,
                activeLanguage: state.index.activeLanguage
            }
        }
    }

    connectActions() {
        // actions mapping object
        return {
            setLocale: this.indexActions.setLocale
        }
    }

    toggleMenu() {
        this.menuActive = !this.menuActive;
    }

    changeLanguageAndCloseMenu(isocode) {
        this.actions.setLocale(isocode);
        this.menuActive = false;
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get('aptoLanguageSwitch.' + path, trustAsHtml);
    }
}

const Component = {
    template: Template,
    controller: Controller
};

Controller.$inject = ControllerInject;

export default ['aptoLanguageSwitch', Component];