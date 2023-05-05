const DirectiveInject = ['$document', '$window', '$timeout', '$rootScope'];
const Directive = function ($document, $window, $timeout, $rootScope) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            let aptoElementHeightChanged = attrs.aptoElementHeightChanged;

            scope.$watch(
                function() {
                    return element[0].clientHeight;
                },
                function() {
                    $rootScope.$emit(aptoElementHeightChanged + '-element-height-changed');
                }
            );
        }
    }
};

Directive.$inject = DirectiveInject;
export default ['aptoElementHeightChanged', Directive];