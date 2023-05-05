const AptoAfterWindowResizeInject = ['$window', '$timeout'];
const AptoAfterWindowResize = ($window, $timeout) => {
    return {
        restrict: 'A',
        scope: {
            afterWindowResize: '&',
            afterWindowResizeSizes: '@'
        },
        link: function (scope, element, attrs) {
            let resizeTO, sizes = {}, requestedSizes = scope.afterWindowResizeSizes ? scope.afterWindowResizeSizes.split(',') : [];

            /* create aptoAfterWindowResize event */
            angular.element($window).resize(function() {
                if(resizeTO) {
                    $timeout.cancel(resizeTO);
                }

                resizeTO = $timeout(() => {
                    angular.element($window).trigger('aptoAfterWindowResize');
                }, 200);
            });

            /* update sizes after window resize */
            angular.element($window).on('aptoAfterWindowResize', () => {
                updateSizes();
            });

            function updateSizes() {
                sizes['window'] = {
                    width: angular.element($window).width(),
                    height: angular.element($window).height()
                };

                for (let i = 0; i < requestedSizes.length; i++) {
                    const selector = requestedSizes[i];
                    sizes[selector] = {
                        width: angular.element(selector).width(),
                        height: angular.element(selector).height()
                    }
                }
                scope.afterWindowResize({sizes: sizes});
            }
            updateSizes();
        }
    }
};

AptoAfterWindowResize.$inject = AptoAfterWindowResizeInject;
export default ['aptoAfterWindowResize', AptoAfterWindowResize];