import Template from './element-attachment-image-zoom.component.html';

const ControllerInject = ['$window', 'ngDialog'];
class Controller {
    constructor($window, ngDialog) {
        this.window = $window;
        this.ngDialog = ngDialog;

        this.mediaUrl = APTO_API.media;
        this.boundedEvents = [];
    };

    $onInit() {
        this.element = angular.copy(this.elementInput);
    }

    $onDestroy() {
        for (let i = 0; i < this.boundedEvents.length; i++) {
            this.boundedEvents[i]();
        }
    }

    openAlternatePreviewImageDialog($event, attachment) {
        $event.preventDefault();

        let dialogWidth = '800px';
        if (this.window.matchMedia("(max-width: 819px)").matches) {
            dialogWidth = (this.window.innerWidth - 20) + 'px';
        }

        this.ngDialog.open({
            data: {
                attachment: attachment,
                mediaUrl: this.mediaUrl
            },
            template: '<img class="sbs-default-element-preview-image" ng-src="{{ngDialogData.mediaUrl + ngDialogData.attachment.fileUrl}}" />',
            plain: true,
            className: 'ngdialog-theme-default ngdialog-alternate-preview-image',
            width: dialogWidth
        });
    };
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        elementInput: '<element'
    },
    template: Template,
    controller: Controller
};

export default ['aptoElementAttachmentImageZoom', Component];