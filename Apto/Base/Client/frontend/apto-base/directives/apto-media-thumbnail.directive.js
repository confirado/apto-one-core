const AptoMediaThumbnailInject = [];
const AptoMediaThumbnail = function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            let path, size, extension;

            function init() {
                initAttributeValues();
                setSrc();
            }

            function initAttributeValues() {
                let sizeX = '',
                    sizeY = '';

                if (attrs.aptoMediaThumbnailSize) {
                    sizeX = attrs.aptoMediaThumbnailSize;
                    sizeY = attrs.aptoMediaThumbnailSize;
                }

                if (attrs.aptoMediaThumbnailSizeX) {
                    sizeX = attrs.aptoMediaThumbnailSizeX;
                }

                if (attrs.aptoMediaThumbnailSizeY) {
                    sizeY = attrs.aptoMediaThumbnailSizeY;
                }

                if (sizeX === '' && sizeY === '') {
                    sizeX = '128';
                    sizeY = '128';
                }

                path = attrs.aptoMediaThumbnail ? attrs.aptoMediaThumbnail : null;
                size = sizeX + 'x' + sizeY;
                extension = attrs.aptoMediaThumbnailExtension ? attrs.aptoMediaThumbnailExtension : 'jpg';
            }

            function setSrc() {
                if (null !== path) {
                    element.attr(
                        'src',
                        APTO_API.thumb + path + '_' + size + '.' + extension
                    );
                }
            }

            attrs.$observe('aptoMediaThumbnail', function(value){
                init();
            });

            attrs.$observe('aptoMediaThumbnailSize', function(value){
                init();
            });

            attrs.$observe('aptoMediaThumbnailSizeX', function(value){
                init();
            });

            attrs.$observe('aptoMediaThumbnailSizeY', function(value){
                init();
            });

            attrs.$observe('aptoMediaThumbnailExtension', function(value){
                init();
            });

            init();
        }
    }
};

AptoMediaThumbnail.$inject = AptoMediaThumbnailInject;
export default ['aptoMediaThumbnail', AptoMediaThumbnail];