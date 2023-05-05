import UserImageTemplate from './user-image.component.html';
import Image from '../../assets/img/aptoLogoBackend.png';

const UserImageControllerInject = ['$ngRedux', 'APTO_DIST_PATH_URL'];
class UserImageController {
    constructor($ngRedux, APTO_DIST_PATH_URL) {
        this.reduxState = {};
        this.reduxActions = {};
        this.ngRedux = $ngRedux;
        this.image = APTO_DIST_PATH_URL + Image;
    };

    reduxMapState(state) {
        return {
        }
    }

    reduxMapActions() {
        return {

        }
    }

    reduxConnect() {
        return this.ngRedux.connect(this.reduxMapState, this.reduxMapActions())(this.reduxState, this.reduxActions);
    }

    $onInit() {
        this.reduxDisconnect = this.reduxConnect();
    }

    $onDestroy() {
        this.reduxDisconnect();
    }
}

UserImageController.$inject = UserImageControllerInject;

const UserImageComponent = {
    template: UserImageTemplate,
    controller: UserImageController
};

export default ['aptoUserImage', UserImageComponent];
