import ReadableFilesizeTemplate from './readable-filesize.component.html';

const ReadableFilesizeControllerInject = [];
class ReadableFilesizeController {

    constructor () {
        // @todo sizes should be static const
        this.sizes = ['Byte', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    };

    $onChanges = function (changes) {
    };

    getHumanReadableFilesize = function (bytes, decimals) {
        if (0 > bytes) {
            return '-';
        }
        else if (0 == bytes) {
            return '0 ' + this.sizes[0];
        }
        let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024))),
            dm = decimals || 3;
        return parseFloat((bytes / Math.pow(1024, i)).toFixed(dm)) + ' ' + this.sizes[i];
    };
}

ReadableFilesizeController.$inject = ReadableFilesizeControllerInject;

const ReadableFilesizeComponent = {
    bindings: {
        bytes: '<',
        decimals: '<'
    },
    template: ReadableFilesizeTemplate,
    controller: ReadableFilesizeController
};

export default ['aptoReadableFilesize', ReadableFilesizeComponent];