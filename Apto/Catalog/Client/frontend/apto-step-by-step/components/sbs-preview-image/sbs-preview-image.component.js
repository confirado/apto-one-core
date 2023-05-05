import PreviewImageTemplate from './sbs-preview-image.component.html';

const PreviewImageControllerInject = ['$window', 'ngDialog'];
const PreviewImageController = function ($window, ngDialog) {
    const self = this;

    self.$onChanges = function (changes) {
        if (changes.element) {
            self.element = angular.copy(self.element);
        }
    };

    self.$onInit = function () {
        self.element = angular.copy(self.element);
        self.mediaUrl = APTO_API.media;
    };

    self.openAlternatePreviewImageDialog = function ($event) {
        $event.preventDefault();

        let dialogWidth = '800px';
        if ($window.matchMedia("(max-width: 819px)").matches) {
            dialogWidth = ($window.innerWidth - 20) + 'px';
        }

        ngDialog.open({
            data: {
                previewImage: self.element.previewImage
            },
            template: '<img class="sbs-default-element-preview-image" ng-src="{{ngDialogData.previewImage.fileUrl}}" />',
            plain: true,
            className: 'ngdialog-theme-default ngdialog-alternate-preview-image',
            width: dialogWidth
        });
    };
};

const PreviewImage = {
    template: PreviewImageTemplate,
    controller: PreviewImageController,
    bindings: {
        element: "<element"
    }
};

PreviewImageController.$inject = PreviewImageControllerInject;

export default ['aptoSbsPreviewImage', PreviewImage];
