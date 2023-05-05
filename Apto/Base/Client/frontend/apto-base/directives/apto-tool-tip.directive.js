const AptoToolTipInject = [];
const AptoToolTip = function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            let toolTipContainer = null;
            let toolTip = element.find('.tool-tip');
            
            function setToolTipContainer(event) {
                toolTipContainer = angular.element('<div class="tool-tip-container-fixed tool-tip-container" />');
                toolTipContainer.append(toolTip);
                toolTip.css(getToolTipCss(event));
                angular.element('body').append(toolTipContainer);
            }

            function getToolTipCss(event) {
                const left = event.pageX;
                const top = event.pageY + 15;

                let leftOffset = angular.element(element).offset().left;
                let topOffset = angular.element(element).offset().top;

                if (attrs.aptoToolTip === 'fixed') {
                    leftOffset = 0;
                    topOffset = angular.element(document).scrollTop();

                    return {
                        position: 'fixed',
                        top: top - topOffset,
                        left: left - toolTip.outerWidth() / 2,
                        opacity: 1
                    };
                }

                return {
                    top: top - topOffset,
                    left: left - leftOffset
                };
            }

            function removeToolTipContainer() {
                if (attrs.aptoToolTip === 'fixed' && toolTipContainer !== null) {
                    toolTipContainer.remove();
                    toolTipContainer = null;
                }
            }

            angular.element(element).mouseenter(function (event) {
                if (attrs.aptoToolTip === 'fixed') {
                    setToolTipContainer(event);
                }
            });

            angular.element(element).mousemove(function (event) {
                if (attrs.aptoToolTip === 'fixed' && toolTipContainer === null) {
                    setToolTipContainer(event);
                }
                toolTip.css(getToolTipCss(event));
            });

            angular.element(element).mouseout(function () {
                removeToolTipContainer();
            });

            scope.$on('$destroy', function() {
                removeToolTipContainer();
            });
        }
    }
};

AptoToolTip.$inject = AptoToolTipInject;
export default ['aptoToolTip', AptoToolTip];