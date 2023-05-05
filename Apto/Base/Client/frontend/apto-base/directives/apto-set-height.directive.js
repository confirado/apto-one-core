const AptoSetHeightInject = ['$timeout', '$rootScope'];
const AptoSetHeight = function ($timeout, $rootScope) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            let aptoSetHeight = attrs.aptoSetHeight;
            let aptoSetHeightSubtract = splitAttr(attrs.aptoSetHeightSubtract);
            let aptoSetHeightCalculateOn = splitAttr(attrs.aptoSetHeightCalculateOn);
            let aptoSetHeightMinHeight = attrs.aptoSetHeightMinHeight;
            let boundedEvents = [];

            $timeout(function () {
                init();
                initEvents();
                setHeight();
            });

            function init() {
                if (typeof aptoSetHeightMinHeight === "undefined") {
                    aptoSetHeightMinHeight = 'false';
                }
            }

            function initEvents() {
                angular.element(window).resize(function () {
                    if (this.resizeTO) clearTimeout(this.resizeTO);
                    this.resizeTO = setTimeout(function () {
                        $(this).trigger('resizeAfter');
                    }, 500);
                });

                angular.element(window).bind('resizeAfter', function () {
                    setHeight();
                });

                for (let i = 0; i < aptoSetHeightCalculateOn.length; i++) {
                    $rootScope.$on(aptoSetHeightCalculateOn[i], () => {
                        setHeight();
                    });
                }
            }

            function setHeight() {
                let elementHeight = getElementHeight();

                for (let i = 0; i < aptoSetHeightSubtract.length; i++) {
                    elementHeight = elementHeight - angular.element(aptoSetHeightSubtract[i]).outerHeight();
                }

                if (aptoSetHeightMinHeight === 'true') {
                    angular.element(element).css('min-height', elementHeight + 'px');
                } else {
                    angular.element(element).css('height', elementHeight + 'px');
                }
                $rootScope.$emit('APTO_SET_HEIGHT', elementHeight);
            }

            function getElementHeight() {
                let highest = 0;
                let elements = aptoSetHeight.split(',');

                for (let i = 0; i < elements.length; i++) {
                    let elementHeight = angular.element(elements[i]).outerHeight();
                    if (elementHeight > highest) {
                        highest = elementHeight;
                    }
                }
                return highest;
            }

            function splitAttr(value) {
                if (typeof value === "undefined") {
                    value = [];
                } else {
                    value = value.split(',');
                }

                return value;
            }

            scope.$on('$destroy', function() {
                for (let i = 0; i < boundedEvents.length; i++) {
                    boundedEvents[i]();
                }
            });
        }
    }
};

AptoSetHeight.$inject = AptoSetHeightInject;
export default ['aptoSetHeight', AptoSetHeight];