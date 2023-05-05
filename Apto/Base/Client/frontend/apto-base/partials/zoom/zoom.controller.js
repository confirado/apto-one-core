import ZoomTemplate from './zoom.controller.html';

const AptoZoomControllerInject = ['$scope', '$window', '$templateCache', '$ngRedux', 'ngDialog', 'IndexActions'];
const AptoZoomController = function ($scope, $window, $templateCache, $ngRedux, ngDialog, IndexActions) {
    $templateCache.put('base/partials/zoom/zoom.controller.html', ZoomTemplate);
};

AptoZoomController.$inject = AptoZoomControllerInject;

export default ['AptoZoomController', AptoZoomController];