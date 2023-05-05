const AptoScrollToInject = [];
const AptoScrollTo = function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            element.bind('click',function (event) {
                event.preventDefault();
                let offset = angular.element(attrs.aptoScrollTo).offset();
                let duration = attrs.aptoScrollToDuration;

                // return if element not found
                if (!offset) {
                    return;
                }

                // set duration
                switch (duration) {
                    case 'slow': {
                        duration = 600;
                        break;
                    }
                    case 'fast': {
                        duration = 200;
                        break;
                    }
                    default: {
                        if (!duration) {
                            duration = 400;
                        } else {
                            duration = parseInt(duration);
                        }
                    }
                }

                // scroll to offset with duration
                angular.element('html, body').animate({
                    scrollTop: offset.top
                }, duration);
            });
        }
    }
};

AptoScrollTo.$inject = AptoScrollToInject;
export default ['aptoScrollTo', AptoScrollTo];