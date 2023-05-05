import LoginTemplate from './login.controller.html';

const AptoLoginControllerInject = ['$scope', '$window', '$templateCache', '$ngRedux', 'ngDialog', 'IndexAuthActions', 'SnippetFactory'];
const AptoLoginController = function($scope, $window, $templateCache, $ngRedux, ngDialog, IndexAuthActions, SnippetFactory) {
    $templateCache.put('base/partials/login/login.controller.html', LoginTemplate);

    function mapStateToThis (state) {
        return {
            shopSession: state.index.shopSession,
            registerUrl: state.index.shopSession.url.register
        }
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {
        shopLogin: IndexAuthActions.shopLogin
    })($scope);

    function doLogin(loginForm, $event) {
        $event.preventDefault();
        if(loginForm.$valid) {
            $scope.shopLogin($scope.userLogin.email, $scope.userLogin.password).then(() => {
                if (true === $scope.shopSession.loggedIn) {
                    ngDialog.close();
                } else {
                    // @todo show error message
                }
            });
        } else {
            // @todo show error message
        }
    }

    function doRegister($event) {
        $event.preventDefault();
        if ($scope.registerUrl) {
            $window.open($scope.registerUrl, '_blank');
        }
    }

    function snippet(path) {
        return SnippetFactory.get('AptoLoginController.' + path);
    }

    $scope.userLogin = {
        email: '',
        password: ''
    };

    $scope.snippet = snippet;
    $scope.doLogin = doLogin;
    $scope.doRegister = doRegister;

    $scope.$on('$destroy', reduxSubscribe);
};

AptoLoginController.$inject = AptoLoginControllerInject;

export default ['AptoLoginController', AptoLoginController];