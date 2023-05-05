const AptoDialogConfigInject = ['ngDialogProvider',];
const AptoDialogConfig = function (ngDialogProvider) {
    if (typeof AptoInline !== "undefined") {
        ngDialogProvider.setDefaults({
            appendTo: '.apto-inline'
        });
    }
};
AptoDialogConfig.$inject = AptoDialogConfigInject;

export default ['AptoDialogConfig', AptoDialogConfig];