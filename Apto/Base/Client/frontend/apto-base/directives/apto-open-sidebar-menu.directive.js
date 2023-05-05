const AptoOpenSidebarMenuInject = [];
const AptoOpenSidebarMenu = function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            let aptoOpenSidebarMenuOtherContent = attrs.aptoOpenSidebarMenuOtherContent;
            let aptoOpenSidebarMenuNotScrollContent = attrs.aptoOpenSidebarMenuNotScrollContent;
            let aptoOpenSidebarMenuContent = attrs.aptoOpenSidebarMenuContent;
            let aptoOpenSidebarMenuOpenButton = attrs.aptoOpenSidebarMenuOpenButton ? attrs.aptoOpenSidebarMenuOpenButton : element;

            angular.element(element).click(function () {
                toggleVisibilityMenu(aptoOpenSidebarMenuOpenButton);
            });

            angular.element('.sidebar-menu-shadow').unbind();
            angular.element('.sidebar-menu-shadow').click(function () {
                toggleVisibilityMenu('.hamburger-menu');

            });

            function toggleVisibilityMenu(element) {
                angular.element(aptoOpenSidebarMenuContent).toggleClass('active');
                angular.element(aptoOpenSidebarMenuOtherContent).toggleClass('pushed');
                angular.element(aptoOpenSidebarMenuNotScrollContent).toggleClass('sidebar-menu-is-open');
                angular.element(element).toggleClass('active');
            }
        }
    }
};

AptoOpenSidebarMenu.$inject = AptoOpenSidebarMenuInject;
export default ['aptoOpenSidebarMenu', AptoOpenSidebarMenu];