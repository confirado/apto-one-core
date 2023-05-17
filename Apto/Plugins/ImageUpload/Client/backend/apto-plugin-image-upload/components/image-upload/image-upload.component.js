import ImageUploadTemplate from './image-upload.component.html';

const ImageUploadControllerInject = ['$ngRedux', 'ElementActions', 'ProductActions', 'ImageUploadCanvasActions'];
class ImageUploadController {
    constructor($ngRedux, ElementActions, ProductActions, ImageUploadCanvasActions) {
        this.newAllowedFileType = '';
        this.allowedFontFileExtensions = ['ttf', 'otf', 'woff', 'woff2', 'svg', 'eot'];

        this.newFont = {
            threeD: false,
            file: null,
            name: ''
        };

        this.priceTypes = [
            'Bild',
            'Text'
        ];

        this.canvasSources = [
            'Element',
            'Global'
        ];

        this.selectedCanvas = null;
        this.canvasSearchTerm = '';

        this.values = {
            canvas: {
                source: 'Element',
                canvasId: null
            },
            user: {
                active: true,
                previewSize: 250,
                maxFileSize: 4,
                minWidth: 0,
                minHeight: 0,
                allowedFileTypes: ['jpg', 'jpeg', 'png'],
                surchargePrices: [],
                useSurchargeAsReplacement: false
            },
            background: {
                image: null,
                width: 1000,
                height: 600,
                perspective: 'persp1',
                layer: '0',
                area: {
                    width: 0,
                    height: 0,
                    left: 0,
                    top: 0
                }
            },
            text: {
                active: false,
                default: 'Mein Text!',
                fontSize: 25,
                textAlign: 'center',
                left: 0,
                top: 0,
                radius: 0,
                fill: '#ffffff',
                multiline: false,
                fonts: []
            }
        };

        this.newPrice = {};
        this.newPrice.currencyCode = 'EUR';

        this.mapStateToThis = function(state) {
            return {
                detailDefinition: state.element.detail.definition,
                availableCustomerGroups: state.product.availableCustomerGroups,
                canvasIds: state.pluginImageUploadCanvas.ids
            }
        };

        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues,
            availableCustomerGroupsFetch: ProductActions.availableCustomerGroupsFetch,
            fetchCanvasIds: ImageUploadCanvasActions.fetchCanvasIds
        })(this);
    };

    $onInit() {
        this.availableCustomerGroupsFetch();
        const fetchCanvasIdsPromise = this.fetchCanvasIds();

        if (this.detailDefinition.class == 'Apto\\Plugins\\ImageUpload\\Domain\\Core\\Model\\Product\\Element\\ImageUploadDefinition') {
            this.values.user = this.detailDefinition.json.user;
            this.values.background = this.detailDefinition.json.background;
            this.values.user.maxFileSize = parseInt(this.values.user.maxFileSize);
            this.values.user.previewSize = parseInt(this.values.user.previewSize);
            this.values.background.width = parseInt(this.values.background.width);
            this.values.background.height = parseInt(this.values.background.height);

            if (!this.values.user.minWidth) {
                this.values.user.minWidth = 0;
            }

            if (!this.values.user.minHeight) {
                this.values.user.minHeight = 0;
            }

            if (!this.values.background.area) {
                this.values.background.area = {
                    width: 0,
                    height: 0,
                    left: 0,
                    top: 0
                };
            } else {
                this.values.background.area = {
                    width: parseInt(this.values.background.area.width),
                    height: parseInt(this.values.background.area.height),
                    left: parseInt(this.values.background.area.left),
                    top: parseInt(this.values.background.area.top)
                };
            }

            if (this.detailDefinition.json.text) {
                this.values.text = this.detailDefinition.json.text;
            }

            if (!Array.isArray(this.values.user.surchargePrices)) {
                this.values.user.surchargePrices = [];
            }

            if (typeof this.values.user.useSurchargeAsReplacement === 'undefined') {
                this.values.user.useSurchargeAsReplacement = false;
            }

            fetchCanvasIdsPromise.then(() => {
                // set local canvas from saved canvas
                if (this.detailDefinition.json.canvas) {
                    this.values.canvas = this.detailDefinition.json.canvas;
                }

                // if element has no canvasId set we have nothing to do anymore
                if (this.values.canvas.canvasId === null) {
                    return;
                }

                // search for selected canvasId in all canvasIds
                for (let i = 0; i < this.canvasIds.length; i++) {
                    if (this.canvasIds[i].id === this.values.canvas.canvasId) {
                        this.selectedCanvas = this.canvasIds[i];
                        return;
                    }
                }

                // if canvas was not found reset canvasId to null
                this.values.canvas.canvasId = null

                // set definition values
                this.setDefinitionValues(this.values);
            });
        }

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    this.setDefinitionValues(this.values);
                    return true;
                }
            }
        });
        this.resetNewFontInput();
    }

    onSelectCanvas(canvas) {
        if (!canvas) {
            this.values.canvas.canvasId = null;
        } else {
            this.values.canvas.canvasId = canvas.id;
        }
    }

    onSelectPreviewImage(path) {
        this.values.background.image = path;
    }

    pushAllowedFileType(value) {
        this.values.user.allowedFileTypes.push(value);
    }

    addAllowedFileTypeValue() {
        this.pushAllowedFileType(this.newAllowedFileType);
        this.newAllowedFileType = '';
    }

    removeAllowedFileTypeValue(index) {
        this.values.user.allowedFileTypes.splice(index, 1);
    }

    allowedFileTypeIsDuplicate(fileType) {
        const allowedFileTypes = this.values.user.allowedFileTypes;
        for (let i = 0; i < allowedFileTypes.length; i++) {
            if (fileType === allowedFileTypes[i]) {
                return true;
            }
        }

        return false;
    }

    onSelectNewFont(path) {
        this.newFont.file = path;
    }

    newFontIsDuplicate() {
        for (let i = 0; i < this.values.text.fonts.length; i++) {
            if (this.newFont.name === this.values.text.fonts[i].name) {
                return true;
            }
        }
        return false;
    }

    newFontFileTypeIsAllowed() {
        return (this.allowedFontFileExtensions.indexOf(this.newFont.file.split('.').pop()) > -1);
    }

    addNewFont() {
        // set first font to be added as default
        if (this.values.text.fonts.length === 0) {
            this.newFont.isDefault = true;
        }
        this.values.text.fonts.push(this.newFont);
        this.resetNewFontInput();

    }

    resetNewFontInput() {
        this.newFont = {
            threeD: false,
            file: null,
            name: '',
            isActive: true,
            isDefault: false
        };
    }

    setFontIsActive(index, isActive) {
        if (!isActive && this.values.text.fonts[index].isDefault) {
            // cannot deactivate Default
            this.values.text.fonts[index].isActive = true;
            return;
        }
        this.values.text.fonts[index].isActive = isActive;
    }

    setFontThreeD(index, threeD) {
        this.values.text.fonts[index].threeD = threeD;
    }

    setDefaultFont(index) {
        for (let i = 0; i < this.values.text.fonts.length; i++) {
            if (this.values.text.fonts[i].isDefault && index !== i) {
                this.values.text.fonts[i].isDefault = false;
            }
        }
        this.values.text.fonts[index].isDefault = true;
    }

    removeFont(index) {
        let defaultToBeDeleted = false;
        if (this.values.text.fonts[index].isDefault) {
            defaultToBeDeleted = true;
        }
        this.values.text.fonts.splice(index, 1);

        // if default gets delete and is not last font => set new Default
        if (defaultToBeDeleted && this.values.text.fonts.length !== 0) {
            this.values.text.fonts[0].isDefault = true;
        }
    }

    addSurchargePrice() {
        if (this.priceExist(this.newPrice)) {
            return;
        }

        this.newPrice.externalId = this.availableCustomerGroups.find(x => x.id === this.newPrice.customerGroupId).externalId;

        this.values.user.surchargePrices.push(this.newPrice);
        this.newPrice = {};
        this.newPrice.currencyCode = 'EUR';
    }

    removePrice(index) {
        this.values.user.surchargePrices.splice(index, 1);
    }

    priceExist(newPrice) {
        for (let i = 0; i < this.values.user.surchargePrices.length; i++) {
            if (newPrice.currencyCode === this.values.user.surchargePrices[i].currencyCode
                && newPrice.customerGroupId === this.values.user.surchargePrices[i].customerGroupId
                && newPrice.type === this.values.user.surchargePrices[i].type) {
                return true;
            }
        }
        return false;
    }

    $onDestroy() {
        this.unSubscribeActions();
    };
}

ImageUploadController.$inject = ImageUploadControllerInject;

const ImageUploadComponent = {
    bindings: {
        definitionValidation: '&'
    },
    template: ImageUploadTemplate,
    controller: ImageUploadController
};

export default ['aptoImageUploadElement', ImageUploadComponent];
