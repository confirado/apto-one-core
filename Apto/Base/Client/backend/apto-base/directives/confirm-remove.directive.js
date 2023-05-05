const DirectiveInject = ['$parse', '$mdDialog'];
const Directive = function($parse, $mdDialog) {
    return {
        restrict: 'A',
        compile: function ($element, $attr) {
            const fn = $parse($attr['aptoConfirmRemove']);
            const stopPropagation = $parse($attr['aptoConfirmRemoveStopPropagation'])();
            const preventDefault = $parse($attr['aptoConfirmRemovePreventDefault'])();

            return (scope, element) => {
                element.on('click', (event) => {
                    if (true === stopPropagation) {
                        event.stopPropagation();
                    }

                    if (true === preventDefault) {
                        event.preventDefault();
                    }

                    const callback = () => {
                        fn(scope, {$event: event});
                    };

                    const confirm = $mdDialog.confirm()
                        .title('Den gewählten Eintrag wirklich löschen?')
                        .textContent('Das Löschen kann nicht rückgängig gemacht werden!')
                        .targetEvent(event)
                        .multiple(true)
                        .ok('Löschen')
                        .cancel('Abbrechen');

                    $mdDialog.show(confirm).then(() => {
                        scope.$evalAsync(callback);
                    }, () => {});
                });
            };
        }
    }
};

Directive.$inject = DirectiveInject;
export default ['aptoConfirmRemove', Directive];