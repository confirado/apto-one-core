const AptoSetActiveSectionInject = [];
const AptoSetActiveSection = function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            angular.element(element).click(function ($event) {
                angular.element(attrs.aptoSetActiveSection).each(function () {
                    angular.element(this).removeClass('active-section');
                });
                angular.element(element).addClass('active-section');
            });
        }
    }
};

AptoSetActiveSection.$inject = AptoSetActiveSectionInject;
export default ['aptoSetActiveSection', AptoSetActiveSection];