import MediaIconTemplate from './media-icon.component.html';

const MediaIconControllerInject = ['APTO_ENVIRONMENT'];
class MediaIconController {
    constructor (APTO_ENVIRONMENT) {
        // @todo extensions should be static const
        this.iconExtensions = {
            txt: 'fa-file-text-o',
            pdf: 'fa-file-pdf-o',
            gif: 'fa-file-image-o',
            jpg: 'fa-file-image-o',
            jpeg: 'fa-file-image-o',
            png: 'fa-file-image-o',
            doc: 'fa-file-word-o',
            docx: 'fa-file-word-o',
            ppt: 'fa-file-powerpoint-o',
            pptx: 'fa-file-powerpoint-o',
            xls: 'fa-file-excel-o',
            xlsx: 'fa-file-excel-o',
            mp3: 'fa-file-audio-o',
            wav: 'fa-file-audio-o',
            ogg: 'fa-file-audio-o',
            avi: 'fa-file-video-o',
            mkv: 'fa-file-video-o',
            wmv: 'fa-file-video-o',
            mv4: 'fa-file-video-o',
            mp4: 'fa-file-video-o',
            js: 'fa-file-code-o',
            php: 'fa-file-code-o',
            html: 'fa-file-code-o',
            htm: 'fa-file-code-o',
            py: 'fa-file-code-o',
            cgi: 'fa-file-code-o',
            sh: 'fa-file-code-o',
            zip: 'fa-file-archive-o',
            tar: 'fa-file-archive-o',
            gz: 'fa-file-archive-o',
            rar: 'fa-file-archive-o'
        };
        this.thumbnailExtensions = {
            jpg: 1,
            jpeg: 1,
            gif: 1,
            png: 1,
            pdf: 1
        };
        this.thumbUrl = APTO_ENVIRONMENT.routes.routeUrls.thumb_url;
    };

    $onChanges = function (changes) {
        if (changes.file) {
            this.updateExtension();
        }
    };

    $onInit = function () {
        this.updateExtension();
    };

    hasThumbnail = function (extension) {
        return extension in this.thumbnailExtensions;
    };

    getThumbnail = function (path) {
        let size = '32x32';
        if (typeof this.size !== "undefined") {
            size = this.size;
        }
        return this.thumbUrl + path + '_' + size + '.jpg';
    };

    getIcon = function (extension) {
        return (extension in this.iconExtensions) ? this.iconExtensions[extension] : 'fa-file-o';
    };

    updateExtension = function () {
        if (typeof this.file.extension === "undefined" && this.file.path) {
            let extension = this.file.path.split(".");
            if (extension.length === 1 || (extension[0] === "" && extension.length === 2)) {
                return "";
            }

            this.file.extension = extension.pop();
        }
    }
}

MediaIconController.$inject = MediaIconControllerInject;

const MediaIconComponent = {
    bindings: {
        file: '<',
        size: '@'
    },
    template: MediaIconTemplate,
    controller: MediaIconController
};

export default ['aptoMediaIcon', MediaIconComponent];