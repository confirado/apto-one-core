const AptoScrollTopInject = [];
const AptoScrollTop = function() {
    return {
        restrict: 'A',
        link: function(scope, element) {
            element.bind('click',function (event) {
                angular.element("html,body").animate({scrollTop: '0px'}, "fast");
            });
        }
    }
};

AptoScrollTop.$inject = AptoScrollTopInject;
export default ['aptoScrollTop', AptoScrollTop];