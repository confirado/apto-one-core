const DirectiveInject = ['$sce', 'ngDialog', '$timeout'];

//  apto-open-description-links-in-dialog
const Directive = function ($sce, ngDialog, $timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            // this action should be allowed from backend!
            if (scope.$eval(attrs.aptoOpenDescriptionLinksInDialog)) {
                $timeout(() => {
                    angular.element(element).find('.description a').on('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var href = angular.element(event.currentTarget).attr('href');

                        ngDialog.open({
                            template: '<iframe src="' + $sce.trustAsResourceUrl(href) + '" style="width: 100%;height: 100%;"></iframe>',
                            plain: true,
                            width: '80%',
                            height: '100%'
                        });
                    });
                })
            }
        }
    }
};

Directive.$inject = DirectiveInject;

export default ['aptoOpenDescriptionLinksInDialog', Directive];

