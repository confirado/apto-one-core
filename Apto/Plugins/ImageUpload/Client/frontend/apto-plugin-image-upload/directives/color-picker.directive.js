require('../libs/jquery-wheelcolorpicker-3.0.9/jquery.wheelcolorpicker');

const DirectiveInject = [];
const Directive = () => {
    return {
        restrict: 'A',
        scope: {
            colorPickerColor: '=',
            colorPickerOptions: '=',
            onColorSelected: '&'
        },
        link: function (scope, element, attrs) {
            let options = {
                userinput: false,
                hideKeyboard: true,
                htmlOptions: false,
                sliders: 'wv',
                preview: true
            };

            init();

            function init() {
                if (scope.colorPickerOptions) {
                    initOptions();
                }

                element.wheelColorPicker(options);

                if (scope.colorPickerColor) {
                    element.wheelColorPicker('setColor', scope.colorPickerColor);
                }

                element.on('sliderup', () => {
                    onColorSelected();
                });
            }

            function initOptions() {
                for (let option in scope.colorPickerOptions) {
                    if (!scope.colorPickerOptions.hasOwnProperty(option)) {
                        continue;
                    }

                    options[option] = scope.colorPickerOptions[option];
                }
            }

            function onColorSelected() {
                scope.onColorSelected({
                    color: element.wheelColorPicker('getValue', 'hex')
                });
            }
        }
    }
};

Directive.$inject = DirectiveInject;
export default ['aptoColorPicker', Directive];