import Template from './home.component.html';
import Image from '../../assets/img/apto-backend-home.jpg';

const ControllerInject = ['$ngRedux', 'APTO_DIST_PATH_URL'];

class Controller {
    constructor($ngRedux, APTO_DIST_PATH_URL) {
        this.image = APTO_DIST_PATH_URL + Image;
        this.versions = APTO_API.versions;
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        amount: '='
    },
    template: Template,
    controller: Controller
};

export default ['aptoHome', Component];
