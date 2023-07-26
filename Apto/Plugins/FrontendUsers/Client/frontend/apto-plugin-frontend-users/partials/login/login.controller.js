import LoginTemplate from './login.controller.html';

const LoginControllerInject = ['$scope', '$location', '$ngRedux', '$window', '$http', 'ngDialog', '$templateCache', 'SnippetFactory', 'FrontendUsersActions', 'ConfigurationService', 'IndexActions', 'APTO_DEFAULT_CUSTOMER_GROUP'];
const LoginController = function ($scope, $location, $ngRedux, $window, $http, ngDialog, $templateCache, SnippetFactory, FrontendUsersActions, ConfigurationService, IndexActions, APTO_DEFAULT_CUSTOMER_GROUP) {
    $templateCache.put('plugin/frontendUser/login/login.controller.html', LoginTemplate);

    function mapStateToThis(state) {
        return {
            isLoggedIn: state.frontendUser.isLoggedIn,
            currentUserName: state.frontendUser.userName
        }
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {
        findCurrentFrontendUser: FrontendUsersActions.findCurrentFrontendUser,
        logoutCurrentFrontendUser: FrontendUsersActions.logoutCurrentFrontendUser,
        fetchShopSessionCustomerGroupByExternalId: IndexActions.fetchShopSessionCustomerGroupByExternalId
    })($scope);

    function doLogin() {
        $http.post(APTO_API.root + '/login', {
            "username": $scope.userLogin.email,
            "password": $scope.userLogin.password
        }).then((response) => {
            $scope.findCurrentFrontendUser(response.data.username).then((response) => {
                let externalCustomerGroupId = response.action.payload.data.result.externalCustomerGroupId;
                if (externalCustomerGroupId) {
                    onCustomerChange(externalCustomerGroupId);
                }
            });
            ngDialog.closeAll();
        }, (response) =>  {
            switch (response.data.error) {
                case 'Invalid credentials.': {
                    $scope.error = snippet('InvalidCredentials');
                    break;
                }
                case 'Account is disabled.': {
                    $scope.error = snippet('AccountIsDisabled');
                    break;
                }
                default: {
                    $scope.error = snippet('LoginNotSuccessFull');
                }
            }
        });
    }

    function doLogout() {
        $http.post(APTO_API.root + '/logout').then(
            function (response) {
                $scope.logoutCurrentFrontendUser();
                onCustomerChange(APTO_DEFAULT_CUSTOMER_GROUP.id);
                ngDialog.closeAll();
            }
        );
    }

    function onCustomerChange(customerId) {
        return $scope.fetchShopSessionCustomerGroupByExternalId(customerId).then(() => {
            const path = $location.path();
            if (path.startsWith('/product') || path.startsWith('/configuration')) {
                ConfigurationService.fetchCurrentStatePrice();
            }
        });
    }

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('plugins.frontendUsers.' + path, trustAsHtml);
    }

    $scope.userLogin = {
        email: '',
        password: ''
    };

    $scope.error = '';
    $scope.doLogin = doLogin;
    $scope.doLogout = doLogout;
    $scope.snippet = snippet;

    $scope.$on('$destroy', reduxSubscribe);
};

LoginController.$inject = LoginControllerInject;

export default ['LoginController', LoginController];
