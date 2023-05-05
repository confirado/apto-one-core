import Template from "./confirm-select-section.controller.html";

function confirmSelectSectionDialog(ngDialog, SnippetFactory) {
    const snippets = SnippetFactory.getNode('aptoStepByStep');
    if (!snippets.confirmSelectSectionDialog) {
        return new Promise((resolve, reject) => {
            resolve({value: true});
        });
    }

    return ngDialog.open({
        template: Template,
        plain: true,
        controller: 'AptoDialogConfirmSelectSectionController',
        className: 'ngdialog-theme-default apto-confirm-dialog',
        width: '360px',
        showClose: false,
        closeByEscape: false,
        closeByNavigation: false,
        closeByDocument: false
    }).closePromise;
}

export default confirmSelectSectionDialog;