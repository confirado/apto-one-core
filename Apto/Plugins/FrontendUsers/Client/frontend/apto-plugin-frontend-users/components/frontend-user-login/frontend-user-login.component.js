import Container from 'apto-base/components/apto-container.controller';
import Template from './frontend-user-login.component.html';
import LoginTemplate from "../../partials/login/login.controller.html";

const ControllerInject = ['$http', '$location', '$ngRedux', 'ngDialog', 'SnippetFactory', 'ConfigurationService', 'IndexActions', 'FrontendUsersActions'];
class Controller extends Container {
    constructor($http, $location, $ngRedux, ngDialog, SnippetFactory, ConfigurationService, IndexActions, FrontendUsersActions) {
        super($ngRedux);

        this.http = $http;
        this.location = $location;
        this.ngDialog = ngDialog;
        this.snippetFactory = SnippetFactory;
        this.configurationService = ConfigurationService;
        this.indexActions = IndexActions;
        this.frontendUsersActions = FrontendUsersActions;
    }

    connectProps() {
        return (state) => {
            return {
                isLoggedIn: state.frontendUser.isLoggedIn
            }
        }
    }

    connectActions() {
        // actions mapping object
        return {
            findCurrentFrontendUser: this.frontendUsersActions.findCurrentFrontendUser,
            fetchShopSessionCustomerGroupByExternalId: this.indexActions.fetchShopSessionCustomerGroupByExternalId
        }
    }

    $onInit() {
        super.$onInit();

        this.http.post(APTO_API.root + '/current-user').then((response) => {
            if (!response.data.isLoggedIn) {
                return;
            }

            this.actions.findCurrentFrontendUser(response.data.username).then((response) => {
                let externalCustomerGroupId = response.action.payload.data.result.externalCustomerGroupId;
                if (externalCustomerGroupId) {
                    this.onCustomerChange(externalCustomerGroupId);
                }
            });
        });
    }

    onCustomerChange(customerId) {
        return this.actions.fetchShopSessionCustomerGroupByExternalId(customerId).then(() => {
            const path = this.location.path();
            if (path.startsWith('/product') || path.startsWith('/configuration')) {
                this.configurationService.fetchCurrentStatePrice();
            }
        });
    }

    showLoginDialog($event) {
        $event.preventDefault();
        this.ngDialog.open({
            template: LoginTemplate,
            plain: true,
            controller: 'LoginController',
            className: 'ngdialog-theme-default',
            width: '360px'
        });
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get('plugins.frontendUsers.' + path, trustAsHtml);
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {},
    template: Template,
    controller: Controller
};

export default ['aptoFrontendUserLogin', Component];
