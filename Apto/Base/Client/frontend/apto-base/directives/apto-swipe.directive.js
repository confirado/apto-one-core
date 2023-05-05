require('slick-carousel/slick/slick');

const DirectiveInject = ['$timeout'];

const Directive = function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $timeout(function() {
                angular.element(element).slick({
                    arrows: false,
                    dots: true
                });
            });
        }
    }
};

Directive.$inject = DirectiveInject;

export default ['aptoSwipe', Directive];// apto-swipe
