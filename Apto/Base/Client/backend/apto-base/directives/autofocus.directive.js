const AutofocusInject = ['$timeout'];
const Autofocus = function($timeout) {
    return {
        restrict: 'A',
        link : function($scope, $element) {
            $timeout(function() {
                $element[0].focus();
            });
        }
    }
};

Autofocus.$inject = AutofocusInject;
export default ['autofocus', Autofocus];
