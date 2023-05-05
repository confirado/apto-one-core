const AptoHomeControllerInject = ['$window', '$scope'];
const AptoHomeController = function($window, $scope) {
    function openHelp() {
        $window.open('https://docs.confirado.net', '_blank');
    }

    $scope.openHelp = openHelp;
};

AptoHomeController.$inject = AptoHomeControllerInject;

export default ['AptoHomeController', AptoHomeController];