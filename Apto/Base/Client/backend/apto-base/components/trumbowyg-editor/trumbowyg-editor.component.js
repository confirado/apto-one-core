import Template from './trumbowyg-editor.component.html';
import ContainerController from '../apto-container.controller';

const ControllerInject = ['$ngRedux'];
class Controller extends ContainerController {
    constructor($ngRedux) {
        super($ngRedux);
    }

    connectProps() {
        return (state) => {
            // state mapping object
            return {
                currentUser: state.index.currentUser
            }
        }
    }

    $onInit() {
        // init parent
        super.$onInit();
    }

    $onChanges(changes) {
        if (changes.sourceCode) {
            this.onSourceCodeChanged({sourceCode: this.sourceCode});
        }
    }

    onDirectiveSourceCodeChanged(sourceCode) {
        this.onSourceCodeChanged({sourceCode: sourceCode});
    }

    onChangeTextArea() {
        this.onSourceCodeChanged({sourceCode: this.sourceCode});
    }

    isTrumbowygEnabled() {
        return this.state.currentUser.rte === 'trumbowyg';
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        sourceCode: '<',
        onSourceCodeChanged: '&'
    },
    template: Template,
    controller: Controller
};

export default ['aptoTrumbowygEditor', Component]