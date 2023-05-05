import Controller from './apto.controller';

const ContainerControllerInject = ['$ngRedux'];
class ContainerController extends Controller {
    constructor($ngRedux) {
        // call parent constructor
        super();

        // set services
        this.ngRedux = $ngRedux;

        // set redux properties
        this.state = {};
        this.actions = {};
    }

    connectProps() {
        return (state) => {
            // state mapping object
            return {

            }
        }
    }

    connectActions() {
        // actions mapping object
        return {

        }
    }

    onStateChange(state) {
        this.state = state;
    }

    connectRedux() {
        this.eventListeners.push(
            this.ngRedux.connect(
                this.connectProps(),
                this.connectActions()
            )((selectedState, actions) => {
                this.actions = actions;
                this.onStateChange(selectedState);
            })
        );
    }

    $onInit() {
        this.connectRedux();
    }
}

ContainerController.$inject = ContainerControllerInject;
export default ContainerController;