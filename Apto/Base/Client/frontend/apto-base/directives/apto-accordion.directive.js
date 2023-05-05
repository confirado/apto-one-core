const AptoAccordionInject = ['$timeout', '$ngRedux', 'OnePageElementActions'];
const AptoAccordion = function ($timeout, $ngRedux, OnePageElementActions) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            let redux = {};

            const reduxSubscribe = $ngRedux.connect(null, {
                setSidebarOpen: OnePageElementActions.setSidebarOpen
            })(redux);

            $timeout(function () {

                if (angular.element('.all-groups ul').length > 1) {

                    angular.element('.accordion-toggle').slice(0, 1).addClass('active');
                    angular.element('.accordion-toggle').slice(0, 1).next().slideToggle('fast', 'linear');
                    element.find('.accordion-toggle').click(function () {
                        let elem = angular.element(this);
                        let isActive = elem.hasClass('active');

                        angular.element('.accordion-toggle').each(function () {
                            angular.element(this).removeClass('active');
                        });

                        if (isActive) {
                            elem.removeClass('active');
                        } else {
                            elem.addClass('active');
                        }

                        elem.next().slideToggle('fast', 'linear');
                        angular.element(".accordion-content").not(angular.element(this).next()).slideUp('fast', 'linear');

                        redux.setSidebarOpen(false);
                    });
                }
            });

            scope.$on('$destroy', function () {
                reduxSubscribe();
            });
        }
    }
};

AptoAccordion.$inject = AptoAccordionInject;
export default ['aptoAccordion', AptoAccordion];