import Template from './image-upload.component.html';
import UploadTemplate from './upload/upload.html';
import TextTemplate from './text/text.html';
import ActionsTemplate from './actions/actions.html';
import MobileTemplate from './mobile/mobile.html';
const FontFaceObserver = require('fontfaceobserver');

const ControllerInject = ['$http', '$rootScope', '$templateCache', '$window', '$document', '$sce', '$timeout', '$location', '$ngRedux', 'MessageBusFactory', 'ConfigurationService', 'ImageUploadDefinitionActions', 'PersistedPropertiesFactory', 'FabricCanvasFactory', 'RenderImageActions', 'SnippetFactory', 'ngDialog'];
class Controller {
    static getState(prop, state, sectionId, elementId, getPersistedProperty) {
        return getPersistedProperty(
            sectionId,
            elementId,
            prop,
            state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values'][prop]
        );
    }

    constructor($http, $rootScope, $templateCache, $window, $document, $sce, $timeout, $location, $ngRedux, MessageBusFactory, ConfigurationService, ImageUploadDefinitionActions, PersistedPropertiesFactory, FabricCanvasFactory, RenderImageActions, SnippetFactory, ngDialog) {
        // templates
        this.templates = {
            upload: 'apto-plugin-image-upload/components/image-upload/upload/upload.html',
            text: 'apto-plugin-image-upload/components/image-upload/text/text.html',
            actions: 'apto-plugin-image-upload/components/image-upload/actions/actions.html',
            mobile: 'apto-plugin-image-upload/components/image-upload/mobile/mobile.html'
        };
        $templateCache.put(this.templates.upload, UploadTemplate);
        $templateCache.put(this.templates.text, TextTemplate);
        $templateCache.put(this.templates.actions, ActionsTemplate);
        $templateCache.put(this.templates.mobile, MobileTemplate);

        // services
        this.http = $http;
        this.rootScope = $rootScope;
        this.window = $window;
        this.document = $document[0];
        this.timeout = $timeout;
        this.location = $location;
        this.ngRedux = $ngRedux;
        this.sce = $sce;
        this.messageBusFactory = MessageBusFactory;
        this.configurationService = ConfigurationService;
        this.imageUploadActions = ImageUploadDefinitionActions;
        this.api = APTO_API;
        this.fabricCanvasFactory = FabricCanvasFactory;
        this.renderImageActions = RenderImageActions;
        this.snippetFactory = SnippetFactory;
        this.ngDialog = ngDialog;


        // service functions
        this.getPersistedProperty = PersistedPropertiesFactory.getPersistedProperty;

        // actions
        this.reduxActions = {};

        // properties
        this.reduxProps = {};
        this.staticValues = {};
        this.input = {
            userText: {
                text: 'Mein Text!',
                fontSize: 25,
                fill: '#ffffff',
                textAlign: 'center'
            }
        };

        this.showMobile = {
            show: false,
            image: false,
            text: false,
            motive: false
        };
        this.boundedEvents = [];
    };

    mapStateProps (state) {
        return (state) => {
            const values = state.configuration.present.configurationState[this.section.id].elements[this.element.id].state.values;

            let stateProps = {
                productId: state.configuration.present.raw.product.id,
                currentPerspective: state.renderImage.currentPerspective,
                configurationState: {
                    renderImage: values.renderImage,
                    fabricItemsOnCanvas: values.fabricItemsOnCanvas
                },
                userUploads: state.pluginImageUploadDefinition.userUploads
            };

            const elements = state.pluginImageUploadDefinition.elements;

            if (elements[this.element.id]) {
                const element = elements[this.element.id];

                stateProps.timestamp = element.timestamp;
                stateProps.background = element.background;
                stateProps.userImageUploadErrors = element.userImageUploadErrors;
                stateProps.renderImage = element.renderImage;
                stateProps.editable = element.editable;
                stateProps.element = element.element;
                stateProps.perspective = element.perspective;
                stateProps.origPerspective = element.origPerspective;
                stateProps.fabricItemsOnCanvas = element.fabricItemsOnCanvas;
                stateProps.activeItem = state.pluginImageUploadDefinition.activeItem;
            }

            // return state mapping object
            return stateProps;
        }
    }

    reduxConnect() {
        return this.ngRedux.connect(
            this.mapStateProps(),
            {
                setUserImage: this.imageUploadActions.setUserImage,
                setUserImageOptions: this.imageUploadActions.setUserImageOptions,
                setUserImageOnCanvas: this.imageUploadActions.setUserImageOnCanvas,
                setUserText: this.imageUploadActions.setUserText,
                setUserTextOptions: this.imageUploadActions.setUserTextOptions,
                setUserTextOnCanvas: this.imageUploadActions.setUserTextOnCanvas,
                setUserImageUploadErrors: this.imageUploadActions.setUserImageUploadErrors,
                setRenderImage: this.imageUploadActions.setRenderImage,
                setUserImageElement: this.imageUploadActions.setUserImageElement,
                setUserImageEditable: this.imageUploadActions.setUserImageEditable,
                setBackground: this.imageUploadActions.setBackground,
                setTimestamp: this.imageUploadActions.setTimestamp,
                uploadUserImageLoading: this.imageUploadActions.uploadUserImageLoading,
                uploadUserImageProgress: this.imageUploadActions.uploadUserImageProgress,
                uploadUserImageError: this.imageUploadActions.uploadUserImageError,
                setOrigPerspective: this.imageUploadActions.setOrigPerspective,
                reset: this.imageUploadActions.reset,
                setCurrentPerspective: this.renderImageActions.setCurrentPerspective,
                setItemOnCanvas: this.imageUploadActions.setItemOnCanvas,
                setItemsOnCanvas: this.imageUploadActions.setItemsOnCanvas,
                removeItemOnCanvas: this.imageUploadActions.removeItemOnCanvas,
                fetchPoolItems: this.imageUploadActions.fetchPoolItems,
                addUserUpload: this.imageUploadActions.addUserUpload
            }
        )((selectedState, actions) => {
            this.onStateChange(selectedState);
            this.reduxActions = actions;
        });
    }

    onStateChange(state) {
        let itemChanged = false;
        if (this.reduxProps.activeItem !== state.activeItem) {
            itemChanged = true;
        }
        this.reduxProps = state;
        if (itemChanged) {
            let activeItem = this.getActiveItem();
            if (activeItem !== null) {
                if (this.getSelectedItemType() === 'text') {
                    this.input.userText = activeItem.fabricItemInput;
                    this.setActiveFont(activeItem);
                } else {
                    //this.input.userText.text = angular.copy(this.staticValues.text.default);
                }
                if (this.getSelectedItemType() === 'clipArt') {
                    this.setClipArtPreview(activeItem.fabricItemOptions.isColorableSVG, activeItem.fabricItemInput.html);
                }
            }
            if (this.reduxProps.activeItem === null) {
                //this.input.userText.text = angular.copy(this.staticValues.text.default);
            }
        }
    }

    setActiveFont(activeItem) {
        if (!this.fonts) {
            return;
        }
        for (let i = 0; i < this.fonts.length; i ++) {
            if (this.fonts[i].family === activeItem.fabricItemInput.fontFamily) {
                this.selectedFont = this.fonts[i];
            }
        }
    }

    getActiveItem() {
        for (let type in this.reduxProps.fabricItemsOnCanvas) {
            if(this.reduxProps.fabricItemsOnCanvas.hasOwnProperty(type) ) {
                if (this.reduxProps.fabricItemsOnCanvas[type].hasOwnProperty(this.reduxProps.activeItem)) {
                    return angular.copy(this.reduxProps.fabricItemsOnCanvas[type][this.reduxProps.activeItem]);
                }
            }
        }
        return null;
    }

    toggleShowMobile(type) {
        this.showMobile.show = !this.showMobile.show;
        this.showMobile[type] = !this.showMobile[type];
    }

    $onInit() {
        this.boundedEvents.push(
            this.rootScope.$on('REMOVE_ITEM_FROM_CANVAS', this.deleteElement.bind(this))
        );
        this.currentCategory = null;
        this.activeCategoryId = null;
        // init section
        if (typeof this.sectionInput !== "undefined") {
            this.section = angular.copy(this.sectionInput);
        } else {
            this.section = angular.copy(this.sectionCtrlInput);
        }

        // init element
        this.element = angular.copy(this.elementInput);
        this.staticValues = this.element.definition.staticValues;

        this.area = {
            width: this.staticValues.background.area.width,
            height: this.staticValues.background.area.height,
            top: this.staticValues.background.area.top,
            left: this.staticValues.background.area.left,
            fullWidth: this.staticValues.background.width,
            fullHeight: this.staticValues.background.height
        };

        this.allowedFileTypes = this.staticValues.user.allowedFileTypes.join(',');
        this.allowedMimeTypes = this.staticValues.user.allowedMimeTypes.join(',');

        this.validate = {
            maxFiles: 1,
            size: {
                max: this.staticValues.user.maxFileSize + 'MB'
            },
            width: {
                min: this.staticValues.user.minWidth,
                max: 80000
            },
            height: {
                min: this.staticValues.user.minHeight,
                max: 12288
            },
            pattern: this.allowedMimeTypes
        };

        this.filter = {
            colorRating: null,
            priceGroup: null,
            properties: [],
            searchString: ''
        };

        // connect to redux store
        this.reduxDisconnect = this.reduxConnect();

        // fetch pool & set default category
        /*this.reduxActions.fetchPoolItems(this.staticValues.user.selectedPool, this.filter).then(() =>{
            if ( this.reduxProps.categories.length > 0) {
                this.setCurrentCategory(this.reduxProps.categories[0]);
            }

        });*/

        // set element
        this.reduxActions.setUserImageElement(
            this.element
        );

        // set background image
        this.reduxActions.setBackground(
            this.staticValues.background.image,
            this.element.id
        );

        // set orig perspective
        this.reduxActions.setOrigPerspective(
            this.reduxProps.currentPerspective,
            this.element.id
        );

        // switch to element perspective
        // in case the prev element is also an image element we have to queries for product image at once,
        // one from onDestroy and one from onInit, we always want the result of onInit then,
        // as dirty solution we try to use timeout here to catch most cases
        // need to find a better way
        this.timeout(() => {
            this.configurationService.setCurrentPerspective(
                this.staticValues.background.perspective
            );
        }, 500);

        // init user text
        this.initUserText();

        // init fabric Items on Canvas
        if (this.reduxProps.configurationState.fabricItemsOnCanvas !== null) {
            this.reduxActions.setItemsOnCanvas(this.element.id, angular.copy(this.reduxProps.configurationState.fabricItemsOnCanvas));
        }

        // init render image
        if (null !== this.reduxProps.configurationState.renderImage) {
            this.updateRenderImage(angular.copy(this.reduxProps.configurationState.renderImage));
        }
    }

    initFonts() {
        this.selectedFont = null;
        this.fonts = [];
        for (let i = 0; i < this.staticValues.text.fonts.length; i++) {
            if (this.staticValues.text.fonts[i].isActive) {
                this.fonts.push({
                    family: this.staticValues.text.fonts[i].name,
                    url: APTO_API.media + this.staticValues.text.fonts[i].file,
                })
                if (this.staticValues.text.fonts[i].isDefault) {
                    this.selectedFont = this.fonts[this.fonts.length - 1];
                }
            }
        }

        // if no font is active, force first font to be active to prevent misbehaving of frontend
        if (this.fonts.length === 0) {
            const fallbackFont = {
                family: 'Open Sans',
                url: null
            };

            this.fonts.push(fallbackFont);
            this.selectedFont = fallbackFont;
        }
    }

    initUserText() {
        this.initFonts();

        if (false === this.staticValues.text.active) {
            return;
        }

        this.setTextDefaults();
    }

    setTextDefaults() {
        this.input.userText.text = this.staticValues.text.default;
        this.input.userText.fontSize = this.staticValues.text.fontSize;
        this.input.userText.fontFamily = this.selectedFont.family;
        this.input.userText.textAlign = this.staticValues.text.textAlign;
        this.input.userText.fill = this.staticValues.text.fill;
        this.input.userText.fabricItemId = null;
        this.input.userText.fontFamily = this.selectedFont.family;
    }

    $onDestroy() {

        for (let i = 0; i < this.boundedEvents.length; i++) {
            this.boundedEvents[i]();
        }

        // set edit mode to false
        this.setValues();
        this.reduxActions.setUserImageEditable(false, this.element.id);

        // reset perspective
        if (this.reduxProps.origPerspective) {
            this.configurationService.setCurrentPerspective(
                this.reduxProps.origPerspective
            );
        }

        // disconnect redux
        this.reduxDisconnect();
    };

    onUserImageFileUpload($files, $invalidFiles) {
        this.reduxActions.setTimestamp(Math.round(Date.now() / 1000), this.element.id);

        if ($files.length === 1) {
            this.uploadFile($files[0], 'user-image');
        }

        if ($invalidFiles.length > 0) {
            this.reduxActions.setUserImageUploadErrors(
                this.getUserUploadErrors($invalidFiles),
                this.element.id
            );
        }
    }

    getUserUploadErrors($invalidFiles) {
        let errors = [];

        for (let i = 0; i < $invalidFiles.length; i++) {
            const file = $invalidFiles[i];

            switch (file.$error) {
                case'pattern': {
                    errors.push({
                        type: 'pattern'
                    });
                    break;
                }
                case 'maxSize': {
                    errors.push({
                        type: 'maxSize'
                    });
                    break;
                }
                case 'minWidth':
                case 'minHeight': {
                    errors.push({
                        type: 'minDimensions'
                    });
                    break;
                }
                default: {
                    errors.push({
                        type: 'default'
                    });
                }
            }
        }

        return errors;
    }

    reAddTextToCanvas(text) {
        return this.fabricCanvasFactory.makeFabricText(this.element.id, text.fabricItemInput, text.fabricItemOptions);
    }

    reAddImageToCanvas(image) {
        return this.fabricCanvasFactory.makeFabricImage(this.element.id, image.fabricItemOptions, this.staticValues.user.previewSize, true, false, (fabricImage) => {
            this.imagesAdded++;
            this.finishReAdd();
        });
    }

    finishReAdd() {
        const size = {
            width: this.staticValues.background.area.width,
            height: this.staticValues.background.area.height,
            top: this.staticValues.background.area.top,
            left: this.staticValues.background.area.left
        };

        if (this.clipArtsToAdd > this.clipArtsAdded || this.imagesToAdd > this.imagesAdded) {
            return;
        }
        this.fabricCanvasFactory.addElementsToCanvas(this.element.id);
        this.removeValues();
    }

    reAddClipArtToCanvas(clipArt) {
        if (clipArt.fabricItemOptions.isColorableSVG) {
            clipArt.fabricItemOptions.html = clipArt.fabricItemInput.html;
            clipArt.fabricItemOptions.reColor = true;
            return this.fabricCanvasFactory.makeFabricSVG(this.element.id, clipArt.fabricItemOptions, this.staticValues.user.previewSize, true, true, (fabricSvg) => {
                this.clipArtsAdded++;
                this.finishReAdd();
            });
        } else {
           return this.fabricCanvasFactory.makeFabricImage(this.element.id, clipArt.fabricItemOptions, this.staticValues.user.previewSize, true, true, (fabricImage) => {
                this.clipArtsAdded++;
                this.finishReAdd();
            });
        }

    }

    onEdit() {
        this.imagesToAdd = 0;
        this.imagesAdded = 0;
        // set editable flag
        this.reduxActions.setUserImageEditable(true, this.element.id);

        // clear current CanvasFactory Elements
        this.fabricCanvasFactory.clearElements(this.element.id);
        const size = {
            width: this.staticValues.background.area.width,
            height: this.staticValues.background.area.height,
            top: this.staticValues.background.area.top,
            left: this.staticValues.background.area.left
        };


        //TODO: iterate over all items => add by element type and add itemId

        for (let textId in this.reduxProps.fabricItemsOnCanvas.text) {
            if(this.reduxProps.fabricItemsOnCanvas.text.hasOwnProperty(textId) ) {
               let text = this.reduxProps.fabricItemsOnCanvas.text[textId];
               this.fabricCanvasFactory.checkIfItemInsidePrintArea(this.reAddTextToCanvas(text),size);
            }
        }

        for (let imageId in this.reduxProps.fabricItemsOnCanvas.image) {
            if(this.reduxProps.fabricItemsOnCanvas.image.hasOwnProperty(imageId) ) {
                this.imagesToAdd++;
            }
        }

        for (let imageId in this.reduxProps.fabricItemsOnCanvas.image) {
            if(this.reduxProps.fabricItemsOnCanvas.image.hasOwnProperty(imageId) ) {
                let image = this.reduxProps.fabricItemsOnCanvas.image[imageId];
                this.fabricCanvasFactory.checkIfItemInsidePrintArea(this.reAddImageToCanvas(image),size);
            }
        }

        for (let clipArtId in this.reduxProps.fabricItemsOnCanvas.clipArt) {
            if(this.reduxProps.fabricItemsOnCanvas.clipArt.hasOwnProperty(clipArtId) ) {
                this.clipArtsToAdd++;
            }
        }

        for (let clipArtId in this.reduxProps.fabricItemsOnCanvas.clipArt) {
            if(this.reduxProps.fabricItemsOnCanvas.clipArt.hasOwnProperty(clipArtId) ) {
                let clipArt = this.reduxProps.fabricItemsOnCanvas.clipArt[clipArtId];
                this.fabricCanvasFactory.checkIfItemInsidePrintArea(this.reAddClipArtToCanvas(clipArt),size);
            }
        }
        if (this.imagesToAdd === this.imagesAdded && this.clipArtsToAdd === this.clipArtsAdded) {
            this.fabricCanvasFactory.setReAddElements(true);
        }
        this.finishReAdd();
    }

    onSave(callback) {
        if (this.saveAllFabricItems()) {
            this.saveRenderImage(callback);
        }
        this.reduxActions.setUserImageEditable(false, this.element.id);
    }

    onCancel() {
        // set editable flag
        this.reduxActions.setUserImageEditable(false, this.element.id);

        // set values
        this.setValues();
    }

    onReset() {
        this.fabricCanvasFactory.removeAllFabricElements(this.element.id);
        this.reduxActions.reset(this.element.id);
        this.removeValues();
    }

    saveRenderImage(callback) {
        const size = {
            width: this.staticValues.background.area.width,
            height: this.staticValues.background.area.height,
            top: this.staticValues.background.area.top,
            left: this.staticValues.background.area.left
        };

        const fileName = 'snapshot';
        this.fabricCanvasFactory.removeAllMarks(this.element.id, () => {
            this.fabricCanvasFactory.makeFile(this.element.id, size, fileName, (file) => {
                // set progress timestamp
                this.reduxActions.setTimestamp(Math.round(Date.now() / 1000), this.element.id);

                // upload file
                this.uploadFile(file, 'render-image', callback);
            })
        });
    }

    uploadFile(file, type, callback) {
        this.reduxActions.uploadUserImageLoading(true, this.element.id);
        this.reduxActions.uploadUserImageProgress(0, this.element.id);
        this.reduxActions.setUserImageUploadErrors([], this.element.id);

        this.messageBusFactory.query('GenerateAptoUuid', []).then((response) => {
            this.saveImageToDisk(
                response.data.result,
                file,
                type,
                callback
            );
        }, (error) => {
            this.reduxActions.setUserImageUploadErrors(
                this.getUserUploadErrors([file]),
                this.element.id
            );
            console.error(error);
        });
    }

    saveImageToDisk(aptoUuid, file, type, callback) {
        const
            timestamp = this.reduxProps.timestamp,
            extension = this.getExtensionFromFileName(file.name);

        let
            now = new Date(timestamp * 1000),
            year = now.getFullYear(),
            month = now.getMonth() < 9 ? ('0' + (now.getMonth() + 1)) : (now.getMonth() + 1),
            directory = '/apto-plugin-image-upload',
            imagePath = '', image = {};

        switch (type) {
            case 'user-image': {
                directory += '/user-images';
                break;
            }
            case 'render-image': {
                directory += '/render-images';
                break;
            }
        }

        directory += '/' + year + '/' + month;
        imagePath += directory + '/' + aptoUuid + '.' + extension;

        image = {
            directory: directory,
            fileName: aptoUuid,
            extension: extension,
            path: imagePath,
        };

        this.messageBusFactory.uploadCommand(
            'UploadUserImageFile',
            [aptoUuid, timestamp, extension, directory],
            [file],
            ''
        ).then((response) => {
            switch (type) {
                case 'user-image': {
                    // set properties
                    image.width = file.$ngfWidth;
                    image.height = file.$ngfHeight;
                    image.top = 0;
                    image.left = 0;

                    // add image to canvas
                    this.onAddImage(image);
                    this.reduxActions.addUserUpload(image);
                    // set values
                    this.setValues(callback);

                    break;
                }
                case 'render-image': {
                    // set properties
                    image.productId = this.reduxProps.productId;
                    image.renderImageId = aptoUuid;
                    image.layer = this.staticValues.background.layer;
                    image.perspective = this.staticValues.background.perspective;
                    image.offsetX = this.staticValues.background.area.left / (this.staticValues.background.width / 100);
                    image.offsetY = this.staticValues.background.area.top / (this.staticValues.background.height / 100);

                    // update render image
                    this.updateRenderImage(image);

                    // set values
                    this.setValues(callback);

                    break;
                }
            }
        }, (error) => {
            this.reduxActions.uploadUserImageLoading(false, this.element.id);
            this.reduxActions.uploadUserImageProgress(0, this.element.id);
            this.reduxActions.uploadUserImageError(true, this.element.id);
            this.reduxActions.setUserImageUploadErrors(
                this.getUserUploadErrors([file]),
                this.element.id
            );
            console.log(error);
        }, (evt) => {
            switch (type) {
                case 'user-image': {
                    this.reduxActions.uploadUserImageProgress(evt.loaded * 100 / evt.total, this.element.id);
                    break;
                }
            }
        });
    }

    afterImageAdded(image, fabricImage, clipArt, colorable) {
        this.fabricCanvasFactory.addElementToCanvas(
            this.element.id,
            fabricImage.fabricItemId
        );

        // center element
        this.fabricCanvasFactory.scaleElementToFitCanvas(
            this.element.id,
            fabricImage.fabricItemId,
            this.area
        );

        this.reduxActions.setItemOnCanvas(
            this.element.id,
            image,
            this.fabricCanvasFactory.getElementOptions(this.element.id, fabricImage.fabricItemId)
        );
        this.showClipArtDetail = clipArt;
        if (clipArt) {
            this.setClipArtPreview(colorable, image.html);
        }
    }

    onAddColorableSVG (image) {
        image.fabricItemId = this.fabricCanvasFactory.getNextFabricItemId();
        this.fabricCanvasFactory.makeFabricSVG(this.element.id, image,  this.staticValues.user.previewSize, false, true,
            (fabricImage) => {
                this.afterImageAdded(image, fabricImage, true, true);
            }
        );
    }

    onAddImage(image, clipArt) {
        image.fabricItemId = this.fabricCanvasFactory.getNextFabricItemId();
        // add element
        this.fabricCanvasFactory.makeFabricImage(this.element.id, image, this.staticValues.user.previewSize, false, clipArt,
            (fabricImage) => {
                this.afterImageAdded(image, fabricImage, clipArt);
                this.saveAllFabricItems();
            }
        );
    }

    centerHandler() {
        if (this.reduxProps.activeItem !== null) {
            this.centerElement(this.reduxProps.activeItem);
        }
    }

    controlsDisabled(type) {
        return this.getSelectedItemType() !== type;
    }

    scaleToFitHandler() {
        this.fabricCanvasFactory.scaleElementToFitCanvas(
            this.element.id,
            this.reduxProps.activeItem,
            this.area
        );
    }

    onAddText() {
        if (this.input.userText.text === null || this.input.userText.text === '' ) {
            return;
        }
        this.input.userText.fabricItemId = this.fabricCanvasFactory.getNextFabricItemId();
        //this.updateUserText();
        let fabricTextOptions = this.input.userText;
        fabricTextOptions.itemType = 'text';
        fabricTextOptions.left = 0;
        fabricTextOptions.top = 0;
        fabricTextOptions.editable = false;
        fabricTextOptions.originX = "center";
        fabricTextOptions.originY = "center";
        let font = new FontFaceObserver(fabricTextOptions.fontFamily);
        font.load().then(() => {
            this.fabricCanvasFactory.makeFabricText(this.element.id, this.input.userText, fabricTextOptions);
            // add element
            this.fabricCanvasFactory.addElementToCanvas(
                this.element.id,
                this.input.userText.fabricItemId
            );
            // center element
            this.centerElement(this.input.userText.fabricItemId);

            this.reduxActions.setItemOnCanvas(
                this.element.id,
                this.input.userText,
                this.fabricCanvasFactory.getElementOptions(this.element.id, this.input.userText.fabricItemId)
            );
            this.saveAllFabricItems();

        });
    }

    getSelectedItemType() {
        if (this.reduxProps.activeItem === null) {
            return null;
        }
        return this.getItemType(this.reduxProps.activeItem)
    }

    getItemType(fabricElementId) {
        if (this.reduxProps.fabricItemsOnCanvas.image.hasOwnProperty(fabricElementId)) {
            return 'image';
        }
        if (this.reduxProps.fabricItemsOnCanvas.text.hasOwnProperty(fabricElementId)) {
            return 'text';
        }
        if (this.reduxProps.fabricItemsOnCanvas.clipArt.hasOwnProperty(fabricElementId)) {
            return 'clipArt';
        }
        return null;
    }

    onUserTextColorSelected(color) {
        this.input.userText.fill = '#'+ color;
        this.updateUserText();
    }

    deleteElement(event, fabricItemId) {
        const itemType = this.getItemType(fabricItemId);
        if (!itemType || !fabricItemId) {
            return;
        }
        this.fabricCanvasFactory.removeElementFromCanvas(
            this.reduxProps.element.id,
            fabricItemId
        );

        this.reduxActions.removeItemOnCanvas(this.element.id, fabricItemId, itemType);
        this.saveAllFabricItems();
    }

    centerElement() {
        // center element
        this.fabricCanvasFactory.centerElement(
            this.element.id,
            this.reduxProps.activeItem,
            this.area
        );
    }

    selectElement() {
        // center element
        this.fabricCanvasFactory.selectElement(
            this.element.id,
            this.reduxProps.activeItem
        );
    }

    sendElementToFront() {
        this.fabricCanvasFactory.sendElementToFront(
            this.element.id,
            this.reduxProps.activeItem
        );
    }

    sendElementToBack() {
        this.fabricCanvasFactory.sendElementToBack(
            this.element.id,
            this.reduxProps.activeItem
        );
    }

    alignText(align) {
        this.fabricCanvasFactory.setElementOptions(
            this.element.id,
            this.reduxProps.activeItem,
            {textAlign: align}
        );

        this.input.userText.textAlign = align;
    }

    changeFontSizeHandle(value) {
        this.fabricCanvasFactory.changeTextSize(this.element.id, this.input.userText.fabricItemId, value);
    }

    getFileInfoHandler() {
        return this
            .snippet('upload.uploadFileInfo', false)
            .replace('%maxFileSize%', this.staticValues.user.maxFileSize)
            .replace('%allowedTypes%', this.allowedFileTypes.toUpperCase())
            .replace('%minWidth%', this.staticValues.user.minWidth)
            .replace('%minHeight%', this.staticValues.user.minHeight)
        ;
    }

    updateUserText() {
        let fabricText = this.fabricCanvasFactory.getElement(
            this.element.id,
            this.input.userText.fabricItemId
        );

        if (this.input.userText.text === '') {
            this.deleteElement(null, this.input.userText.fabricItemId);
            this.input.userText.fabricItemId = null;
            return;
        }

        if (fabricText === null || this.getSelectedItemType() !== 'text') {
            this.onAddText()
            return;
        }

        let fabricTextOptions = {
            fontFamily: this.input.userText.fontFamily,
            fontSize: this.input.userText.fontSize,
            fill: this.input.userText.fill,
            fabricItemId: this.input.userText.fabricItemId,
            itemType: 'text'
        };

        fabricTextOptions.text = this.input.userText.text;
        fabricText.set(fabricTextOptions);
        fabricText.setCoords();
        this.fabricCanvasFactory.render(this.element.id);

        this.reduxActions.setItemOnCanvas(
            this.element.id,
            this.input.userText,
            this.fabricCanvasFactory.getElementOptions(this.element.id, this.input.userText.fabricItemId)
        );
    }

    showColorsHandler() {
        this.isShowColors = !this.isShowColors;
    }

    saveAllFabricItems() {
        let countFabricItems = 0;
        let fabricItemsOnCanvas = angular.copy(this.reduxProps.fabricItemsOnCanvas);
        // texts
        let texts = fabricItemsOnCanvas.text;
        for (let text in texts) {
            if (texts.hasOwnProperty(text)) {
                countFabricItems++
                texts[text].fabricItemOptions = this.fabricCanvasFactory.getElementOptions(this.element.id, text);
            }

        }
        // images
        let images = fabricItemsOnCanvas.image;
        for (let image in images) {
            if (images.hasOwnProperty(image)) {
                countFabricItems++
                images[image].fabricItemOptions = this.fabricCanvasFactory.getElementOptions(this.element.id, image);
            }
        }
        // clipArts
        let clipArts = fabricItemsOnCanvas.clipArt;
        for (let clipArt in clipArts) {
            if (clipArts.hasOwnProperty(clipArt)) {
                countFabricItems++
                clipArts[clipArt].fabricItemOptions = this.fabricCanvasFactory.getElementOptions(this.element.id, clipArt);
            }
        }
        this.reduxActions.setItemsOnCanvas(this.element.id, fabricItemsOnCanvas);
        if (countFabricItems > 0) {
            this.configurationService.setElementProperties(this.section.id, this.element.id, {
                aptoElementDefinitionId: 'apto-element-image-upload',
                fabricItemsOnCanvas: angular.copy(fabricItemsOnCanvas)
            });
            return true;
        }
        else {
            this.removeValues();
            return false;
        }
    }

    fontChanged() {
        // update font family => update canvas
        if (!this.selectedFont) {
            return;
        }
        this.input.userText.fontFamily = this.selectedFont.family;
        // update 3D Text on Canvas
        if (this.reduxProps.userTextOnCanvas) {
            this.reduxActions.setUserTextOnCanvas(
                true,
                this.element.id
            );
        }
        this.updateUserText();
    }

    isFontSelected(font) {
        if (!this.selectedFont) {
            this.selectedFont = font;
            return true;
        }
        return (this.selectedFont.family === font.family);
    }

    updateRenderImage(image) {
        // set render image
        this.reduxActions.setRenderImage(image, this.element.id);
    }

    canvasElementAdded(elementId) {
        return this.fabricCanvasFactory.canvasHasElement(this.element.id, elementId);
    }

    setValues(callback) {
        const
            renderImage = this.reduxProps.renderImage,
            fabricItemsOnCanvas = this.reduxProps.fabricItemsOnCanvas;

        let renderImageToSave = null;
        let payload = null;

        if (renderImage) {
            renderImageToSave = {
                directory: renderImage.directory,
                fileName: renderImage.fileName,
                extension: renderImage.extension,
                path: renderImage.path,
                productId: renderImage.productId,
                renderImageId: renderImage.renderImageId,
                layer: renderImage.layer,
                perspective: renderImage.perspective,
                offsetX: renderImage.offsetX,
                offsetY: renderImage.offsetY
            };

            payload = {
                renderImageURL: APTO_API.media + renderImage.path
            }
        }

        this.configurationService.setElementProperties(this.section.id, this.element.id, {
            aptoElementDefinitionId: 'apto-element-image-upload',
            renderImage: renderImageToSave,
            fabricItemsOnCanvas: angular.copy(fabricItemsOnCanvas),
            payload: payload
        });

        if (callback) {
            callback();
        }

        this.configurationService.continueWithNextSection();
    }

    removeValues() {
        this.configurationService.setElementProperties(
            this.section.id,
            this.element.id,
            {
                aptoElementDefinitionId: null,
                fabricItemsOnCanvas: null,
                payload: null,
                renderImage: null
            }
        );
    }

    getExtensionFromFileName(fileName) {
        let extension = fileName.split('.');
        if (extension.length === 1 || (extension[0] === "" && extension.length === 2)) {
            return '';
        }
        return ('' + extension.pop()).toLowerCase();
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get('plugins.imageUpload.' + path, trustAsHtml);
    }

    setCurrentCategory(category) {
        this.currentCategory = category;
        this.activeCategoryId = category.id
        // parse current Categories SVGs and make SVG Objects for further usage;
        // dont parse if already parsed
        if (!this.svgObjects.hasOwnProperty(category.id)) {
            this.svgObjects[category.id] = {};
            for (let i = 0; i < category.poolItems.length; i++) {
                if (category.poolItems[i].material.hasOwnProperty('previewImage') &&
                    category.poolItems[i].material.previewImage.hasOwnProperty('path') &&
                    category.poolItems[i].material.previewImage.extension === 'svg'
                ) {
                    this.http.get(APTO_API.media + category.poolItems[i].material.previewImage.path).then(data => {
                        this.svgObjects[category.id][category.poolItems[i].material.previewImage.id] = this.setSVGColorMapping(data.data);
                    });
                }
            }
        }
    }

    setClipArtPreview(colorable, svgImage) {
        this.showCurrentColors = false;
        this.currentSVGColorSelected = null;
        // maybe we can set a loading image here
        this.clipArtPreviewImage = null;

        const currentItem = this.getActiveItem();
        if (currentItem === null || currentItem.itemType !== 'clipArt') {
            return
        }
        if (colorable) {
            this.clipArtPreviewType = 'svg';
            this.showCurrentColors = true;
            this.clipArtPreviewImage = this.sce.trustAsHtml(svgImage);
            this.currentSVGColors = this.fabricCanvasFactory.getSVGColors(this.element.id, this.reduxProps.activeItem);
        }
        else {
            this.clipArtPreviewType = 'image';
            this.clipArtPreviewImage = APTO_API.media + currentItem.fabricItemInput.path;
        }
    }

    selectCurrentSVGColor(id) {
        this.currentSVGColorSelected = id;
    }

    setPathColor(color) {
        this.currentSVGColors = this.fabricCanvasFactory.setSVGColor(this.element.id, this.reduxProps.activeItem, this.currentSVGColorSelected, color);
        Array.prototype.slice.call(this.document.querySelectorAll('#detail-svg path'))
            .filter(path => path.getAttribute('id') !== null && +path.getAttribute('id') === this.currentSVGColorSelected)
            .forEach(path => {
                path.setAttribute('fill', color);
            });
        this.updateSVGHTML();
    }

    updateSVGHTML() {
        let input = this.getActiveItem().fabricItemInput;
        input.html = this.document.querySelector('#detail-svg > svg').outerHTML;
        this.reduxActions.setItemOnCanvas(
            this.element.id,
            input,
            this.fabricCanvasFactory.getElementOptions(this.element.id, this.reduxProps.activeItem)
        );
    }

    isWhite(color) {
        if (color.match(/^(?:white|#fff(?:fff)?|rgba?\(\s*255\s*,\s*255\s*,\s*255\s*(?:,\s*1\s*)?\))$/i)) {
            return true;
        }
    }

    setSVGColorMapping(data) {
        let div = document.createElement('div');
        div.innerHTML = data;
        let svgObject = {};
        svgObject.colorMapping = Array.prototype.slice.call(div.querySelectorAll('path'))
            .reduce((result, path) => {
                const fill = path.getAttribute('fill');
                // append color only once
                if (fill !== null) {
                    const existingItem = result.find(f => f.color === fill);
                    if (!existingItem) {
                        const id = result.length; // will always be unique
                        path.setAttribute('id', id)
                        result.push({
                            color: fill,
                            id,
                        })
                    } else {
                        path.setAttribute('id', existingItem.id)
                    }
                }

                return result;
            }, []);
        svgObject.html = div.innerHTML;
        return svgObject;
    }

    onSelectClipArt(clipArt) {
        let img = new Image();
        let image = clipArt.material.previewImage;
        img.src =  image.fileUrl;
        img.onload = () => {
            image.width = img.width;
            image.height = img.height;
            image.top = 0;
            image.left = 0;
            // is colorable SVG?
            if (this.svgObjects.hasOwnProperty(this.activeCategoryId) && this.svgObjects[this.activeCategoryId].hasOwnProperty(image.id)) {
                let svg = angular.copy(this.svgObjects[this.activeCategoryId][image.id]);
                svg.width = image.width;
                svg.height = image.height;
                svg.top = image.top;
                svg.left = image.left;
                this.onAddColorableSVG(svg);
                this.setValues();
            } else {
                this.onAddImage(image, true);
                this.setValues();
            }
        }
    }

}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        elementInput: '<element',
        sectionInput: '<section',
        sectionCtrlInput: '<sectionCtrl'
    },
    template: Template,
    controller: Controller
};

export default ['aptoImageUploadElement', Component];
