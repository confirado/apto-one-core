import UserStatusTemplate from './user-status.component.html';

const UserStatusControllerInject = ['$ngRedux', 'CurrentUserFactory'];
class UserStatusController {
    constructor($ngRedux, CurrentUserFactory) {
        this.reduxState = {};
        this.reduxActions = {};
        this.ngRedux = $ngRedux;
        this.currentUserFactory = CurrentUserFactory;
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

UserStatusController.$inject = UserStatusControllerInject;

const UserStatusComponent = {
    bindings: {
        logoutLink: '@'
    },
    template: UserStatusTemplate,
    controller: UserStatusController
};

export default ['aptoUserStatus', UserStatusComponent];