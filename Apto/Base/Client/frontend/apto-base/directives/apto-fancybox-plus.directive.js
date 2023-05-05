require('fancybox-plus/dist/jquery.fancybox-plus');

const AptoFancyboxPlusInject = ['$timeout'];
const AptoFancyboxPlus = function ($timeout) {
    return {
        restrict: 'A',
        scope: {
            aptoFancyboxPlusOptions: '=',
            gallery: '='
        },
        link: function (scope, element, attrs) {

            // display single image preview
            if (scope.gallery === undefined) {
                angular.element(element).fancyboxPlus();

                if (scope.aptoFancyboxPlusOptions) {
                    angular.element(element).fancyboxPlus(scope.aptoFancyboxPlusOptions);
                } else {
                    angular.element(element).fancyboxPlus();
                }
            }
            // display multiple images in a gallery
            else if (scope.gallery.length > 0) {
                $timeout(() => {
                    let applyFancyboxTo = angular.element(element).find('a');

                    angular.forEach(applyFancyboxTo, function(value, key) {
                        angular.element(value).fancyboxPlus();
                    });
                })
            }
        }
    }
};

AptoFancyboxPlus.$inject = AptoFancyboxPlusInject;

export default ['aptoFancyboxPlus', AptoFancyboxPlus];
