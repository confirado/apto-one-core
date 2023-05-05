const AptoWidthMaterialsInject = ['$window'];
const AptoWidthMaterials = function ($window) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            try {
                if (window.matchMedia("(max-width: 767px)").matches) {
                    setWidth();
                }
            }
            catch (err) {
                // DO nothing
            }

            angular.element($window).bind('resize', function () {
                try {
                    if (window.matchMedia("(min-width: 768px)").matches) {
                        angular.element('.materials').css('width','');
                    } else {
                        setWidth();
                    }
                }
                catch (err) {
                    // DO nothing
                }
            });

            function setWidth() {
                angular.element('.select-section').removeClass('active');
                angular.element('.sidebar-section').removeClass('active');
                angular.element('.materials').css('width', angular.element('.materials > .material').length * 2.625 + 'rem');
            }
        }
    }
};

AptoWidthMaterials.$inject = AptoWidthMaterialsInject;
export default ['aptoWidthMaterials', AptoWidthMaterials];