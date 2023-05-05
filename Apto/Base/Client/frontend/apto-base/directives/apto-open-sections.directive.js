const AptoOpenSectionsInject = [];
const AptoOpenSections = function() {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.bind('click',function () {
                angular.element(this).toggleClass('active');
                angular.element(attrs.aptoOpenSectionsSectionContainer).toggleClass('active');
            });
        }
    }
};

AptoOpenSections.$inject = AptoOpenSectionsInject;
export default ['aptoOpenSections', AptoOpenSections];