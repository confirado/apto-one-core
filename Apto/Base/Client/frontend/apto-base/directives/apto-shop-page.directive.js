const AptoShopPageInject = ['XDomainRequestFactory', 'ngDialog'];
const AptoShopPage = function (XDomainRequestFactory, ngDialog) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            angular.element(element).click(function ($event) {
                $event.preventDefault();
                let $page = XDomainRequestFactory.query('GetPage', [attrs.aptoShopPage]);
                $page.then(function ($result) {
                    //@todo something is wrong with the scrollbar here, you can not snip scrollbar with mouse cursor, this is also the case with non fullscreen dialogs which are heigher than die viewport
                    ngDialog.open({
                        template: '<div class="apto-ngdialog-scroll-viewport">' + $result.data.result + '</div>',
                        plain: true,
                        className: 'ngdialog-theme-default apto-ngdialog-scroll',
                        overlay: true,
                        width: '100%'
                    });
                });
            });
        }
    }
};

AptoShopPage.$inject = AptoShopPageInject;
export default ['aptoShopPage', AptoShopPage];
