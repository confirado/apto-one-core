import { fabric } from '../libs/fabric/fabric';

const DirectiveInject = ['$window', '$timeout'];
const Directive = ($window, $timeout) => {
    return {
        restrict: 'A',
        scope: {
            onCanvasCreated: '&',
            onCanvasDestroyed: '&',
            fabricOptions: '=',
            fabricAdaptDimensions: '='
        },
        link: function (scope, element, attrs) {
            const parentElement = element.parent();
            let afterResize = null;
            if (!scope.fabricOptions.width) {
                scope.fabricOptions.width = 1000;
            }

            if (!scope.fabricOptions.height) {
                scope.fabricOptions.height = 600;
            }

            initResizeEvent();
            initCanvas();

            function initCanvas() {
                scope.fabricCanvas = new fabric.Canvas(element[0]);
                scope.fabricCanvas.preserveObjectStacking = true;
                scope.fabricCanvas.selection = false;

                updateDimensions();
                scope.onCanvasCreated({
                    canvas: scope.fabricCanvas
                });
            }

            function initResizeEvent() {
                /* create aptoAfterWindowResize event */
                angular.element($window).resize(function() {
                    if(afterResize) {
                        $timeout.cancel(afterResize);
                    }

                    afterResize = $timeout(() => {
                        angular.element($window).trigger('aptoFabricCanvasResize');
                    }, 200);
                });
            }

            function updateDimensions() {
                const current = getAdaptedDimensions();

                const source = {
                    width: scope.fabricOptions.width,
                    height: scope.fabricOptions.height
                };

                const scale = {
                    width: current.width /  source.width,
                    height: current.height /  source.height,
                    smallest: null
                };

                scale.smallest = scale.width < scale.height ? scale.width : scale.height;

                const result = {
                    width: scale.smallest === scale.width ? current.width : source.width * scale.smallest,
                    height: scale.smallest === scale.height ? current.height : source.height * scale.smallest
                };

                const area = {
                    width: scope.fabricOptions.area.width  * scale.smallest,
                    height: scope.fabricOptions.area.height * scale.smallest,
                    left: scope.fabricOptions.area.left * scale.smallest,
                    top: scope.fabricOptions.area.top * scale.smallest,
                }

                // set canvas width and height
                scope.fabricCanvas.setWidth(result.width);
                scope.fabricCanvas.setHeight(result.height);

                // set canvas zoom
                scope.fabricCanvas.setZoom(scale.smallest);
                scope.fabricCanvas.renderAll();

                // calculate left and top offset to center canvas in its container
                result.left = (current.width - result.width) / 2;
                result.top = (current.height - result.height) / 2;

                // center canvas in its container
                const container = angular.element('.image-upload-control-container');
                container.css('width', result.width);
                container.css('height', result.height);
                container.css('left', result.left + current.leftOffset);
                container.css('top', result.top + current.topOffset);

                // set area offset
                const printArea = angular.element('#print-area');
                printArea.css('left', area.left);
                printArea.css('top', area.top);
                printArea.css('width', area.width);
                printArea.css('height', area.height);
            }

            function getAdaptedDimensions() {
                let adaptedElement = angular.element(scope.fabricAdaptDimensions);

                if (adaptedElement.length < 1) {
                    adaptedElement = parentElement;
                }

                let width = adaptedElement.width(), height = adaptedElement.height();

                let adapted = {
                    width: width,
                    height: height,
                    leftOffset: (adaptedElement.parent().width() - width) / 2,
                    topOffset: (adaptedElement.parent().height() - height) / 2
                };

                return adapted;
            }

            // destructor
            element.on('$destroy', () => {
                scope.onCanvasDestroyed({
                    canvas: scope.fabricCanvas
                });
            });

            /* update sizes after window resize */
            angular.element($window).on('aptoFabricCanvasResize', () => {
                updateDimensions();
            });
        }
    }
};

Directive.$inject = DirectiveInject;
export default ['aptoFabricCanvas', Directive];
