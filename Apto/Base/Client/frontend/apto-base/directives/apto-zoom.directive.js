import ZoomTemplate from '../partials/zoom/zoom.controller.html';

const AptoZoomInject = ['ngDialog','$timeout', '$window'];
const AptoZoom = function (ngDialog, $timeout, $window) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.bind('click',function () {
                ngDialog.open({
                    template: ZoomTemplate,
                    plain: true,
                    controller: 'AptoZoomController',
                    className: 'ngdialog-theme-default zoom-theme',
                    width: '100%',
                    height: '100%',
                    aptoZoom: attrs['aptoZoom']
                });
                $timeout(function () {
                    angular.element('#ngdialog1').remove();
                });
            });
        }
    }
};

AptoZoom.$inject = AptoZoomInject;
export default ['aptoZoom', AptoZoom];