import { fabric } from '../libs/fabric/fabric';
import Rotate from '../assets/img/syc-drehen.svg';
import Scale from '../assets/img/syc-skalieren.svg';
import ScaleMirror from '../assets/img/syc-skalieren-mirror.svg';
import Trash from '../assets/img/trash-bin.svg';
const FontFaceObserver = require('fontfaceobserver');

const FactoryInject = ['$rootScope', '$window', '$document', '$timeout', 'APTO_PLUGIN_IMAGE_UPLOAD_FABRIC_OUTLINE_OPTIONS', 'APTO_PLUGIN_IMAGE_UPLOAD_FABRIC_NEW_ITEM_SAFE_BUFFER', 'APTO_DIST_PATH_URL'];
const Factory = function($rootScope, $window, $document, $timeout, APTO_PLUGIN_IMAGE_UPLOAD_FABRIC_OUTLINE_OPTIONS, APTO_PLUGIN_IMAGE_UPLOAD_FABRIC_NEW_ITEM_SAFE_BUFFER, APTO_DIST_PATH_URL) {
    let self = this;
    this.canvas = {};
    this.fabricItems = {};
    this.reAddElements = false;
    this.controlsInitialized = false;
    this.timeout = $timeout;
    this.deleteIcon = APTO_DIST_PATH_URL + Trash;
    this.rotateIcon = APTO_DIST_PATH_URL + Rotate;
    this.scaleIcon = APTO_DIST_PATH_URL + Scale;
    this.scaleIconMirror = APTO_DIST_PATH_URL + ScaleMirror;
    this.rootScope = $rootScope;
    self.newItemSafeBuffer = APTO_PLUGIN_IMAGE_UPLOAD_FABRIC_NEW_ITEM_SAFE_BUFFER;

    this.tintFilter = new fabric.Image.filters.BlendColor({
        color: '#ff0000',
        mode: 'tint',
        alpha: 0.5
    })

    function addElementsToCanvas(elementId) {
        if (!self.canvas[elementId] || !self.fabricItems[elementId]) {
            return;
        }
        const canvasElements = Object.values(self.fabricItems[elementId]).sort(function(a,b) {
            return a.zIndex - b.zIndex;
        });

        for (let i = 0; i < canvasElements.length; i++) {
            self.canvas[elementId].add(canvasElements[i]);
            if (canvasElements[i].itemType === "text") {
                let font = new FontFaceObserver(canvasElements[i].fontFamily);
                font.load().then(() => {
                    canvasElements[i].dirty = true;
                    render(elementId);
                });
            }
        }
        render(elementId);
    }

    function removeAllFabricElements(canvasId) {
        if (!self.canvas[canvasId] || !self.fabricItems[canvasId]) {
            return;
        }
        self.canvas[canvasId].clear();
        self.fabricItems[canvasId] = {};
    }

    function addElementToCanvas(elementId, fabricItemId) {
        if(!self.canvas[elementId] || !hasElement(elementId, fabricItemId)) {
            return;
        }

        self.canvas[elementId].add(self.fabricItems[elementId][fabricItemId]);
        selectElement(elementId, fabricItemId);
        updateZIndices(elementId);
        render(elementId);
    }

    function removeElementFromCanvas(elementId, fabricItemId) {
        if (self.canvas[elementId] && hasElement(elementId, fabricItemId)) {
            self.canvas[elementId].remove(
                self.fabricItems[elementId][fabricItemId]
            );
            render(elementId);
        }
    }

    function setCanvas(elementId, canvas) {
        if (!self.controlsInitialized) {
            makeControls();
        }
        self.canvas[elementId] = canvas;
        if (self.reAddElements) {
            addElementsToCanvas(elementId);
            self.reAddElements = false;
        }
    }

    function makeControls() {
        const size = 20;
        const xOffset = ((size + 8) / 2);
        const yOffset = ((size / 4));
        let deleteImage = $document[0].createElement('img');
        deleteImage.src = self.deleteIcon;
        let scaleImage = $document[0].createElement('img');
        scaleImage.src = self.scaleIcon;
        let scaleImageMirror = $document[0].createElement('img');
        scaleImageMirror.src = self.scaleIconMirror;
        let rotateImage = $document[0].createElement('img');
        rotateImage.src = self.rotateIcon;
        fabric.Object.prototype.controls.deleteControl = new fabric.Control({
            x: 0.5,
            y: -0.5,
            offsetY: -yOffset,
            offsetX: xOffset,
            cursorStyle: 'pointer',
            mouseUpHandler: deleteObject,
            render: renderIcon,
            cornerSize: size,
            img: deleteImage
        });
        fabric.Object.prototype.controls.bl = new fabric.Control({
            x: -0.5,
            y: 0.5,
            offsetY: yOffset,
            offsetX: -xOffset,
            cursorStyleHandler: fabric.controlsUtils.scaleCursorStyleHandler,
            actionHandler: fabric.controlsUtils.scalingEqually,
            render: renderIcon,
            cornerSize: size,
            img: scaleImage
        });
        fabric.Object.prototype.controls.br = new fabric.Control({
            x: 0.5,
            y: 0.5,
            offsetY: yOffset,
            offsetX: xOffset,
            cursorStyleHandler: fabric.controlsUtils.scaleCursorStyleHandler,
            actionHandler: fabric.controlsUtils.scalingEqually,
            render: renderIcon,
            cornerSize: size,
            img: scaleImageMirror
        });
        fabric.Object.prototype.controls.tl = new fabric.Control({
            x: -0.5,
            y: -0.5,
            offsetY: -yOffset,
            offsetX: -xOffset,
            cursorStyleHandler: fabric.controlsUtils.rotationStyleHandler,
            actionHandler: fabric.controlsUtils.rotationWithSnapping,
            actionName: 'rotate',
            render: renderIcon,
            cornerSize: size,
            img: rotateImage
        });
        self.controlsInitialized = true;
    }

    function renderIcon(ctx, left, top, styleOverride, fabricObject) {
        let size = this.cornerSize;
        ctx.save();
        ctx.translate(left, top);
        ctx.rotate(fabric.util.degreesToRadians(fabricObject.angle));
        ctx.drawImage(this.img, -size/2, -size/2, size, size);
        ctx.restore();
    }

    function deleteObject(eventData, target) {
        let canvas = target.canvas;
        canvas.remove(target);
        canvas.requestRenderAll();
        self.rootScope.$emit('REMOVE_ITEM_FROM_CANVAS', target.fabricItemId);
    }

    function changeTextSize(canvasId, fabricItemId, value) {
        if (!hasElement(canvasId, fabricItemId)) {
            return;
        }

        let newScale = self.fabricItems[canvasId][fabricItemId].scaleX + (value / 7);
        if (newScale <= 0) {
            newScale = self.fabricItems[canvasId][fabricItemId].scaleX;
        }
        self.fabricItems[canvasId][fabricItemId].set({
            'scaleX': newScale,
            'scaleY': newScale
        });

        render(canvasId);
    }

    function getCanvas(elementId) {
        if (!self.canvas[elementId]) {
            return null;
        }

        return self.canvas[elementId];
    }

    function setReAddElements(value) {
        self.reAddElements = value;
    }

    function setElement(elementId, fabricItemId, element) {
        if (!element) {
            return;
        }

        if (!self.fabricItems[elementId]) {
            self.fabricItems[elementId] = {};
        }
        self.fabricItems[elementId][fabricItemId] = element;
    }

    function reRenderTexts(canvasId) {
        for (let item in self.fabricItems) {
            if(self.fabricItems.hasOwnProperty(item) ) {
                self.fabricItems[item].dirty = true;
            }
        }
        render(canvasId);
    }

    function centerElement(elementId, fabricItemId, size) {
        if (!self.canvas[elementId] || !hasElement(elementId, fabricItemId)) {
            return;
        }
        let selfSize = 0;
        if (self.fabricItems[elementId][fabricItemId].originX !== 'center') {
            selfSize = 1;
        };
        let newLeft = (size.left + (size.width / 2)) - selfSize * (self.fabricItems[elementId][fabricItemId].width * self.fabricItems[elementId][fabricItemId].scaleX / 2 );
        let newTop = (size.top + (size.height / 2)) - selfSize * (self.fabricItems[elementId][fabricItemId].height * self.fabricItems[elementId][fabricItemId].scaleY / 2 );

        self.canvas[elementId].viewportCenterObject(self.fabricItems[elementId][fabricItemId]);
        self.fabricItems[elementId][fabricItemId].set({
            left: newLeft,
            top: newTop
        })
        self.fabricItems[elementId][fabricItemId].setCoords();
        checkIfItemInsidePrintArea(self.fabricItems[elementId][fabricItemId], size);
        render(elementId);
    }

    function selectElement(elementId, fabricItemId) {
        if (!self.canvas[elementId] || !hasElement(elementId, fabricItemId)) {
            return;
        }

        self.canvas[elementId].setActiveObject(self.fabricItems[elementId][fabricItemId]);
        render(elementId);
    }

    function deselectElements(elementId) {
        if (!self.canvas[elementId]) {
            return;
        }
        self.canvas[elementId].discardActiveObject();
        render(elementId);
    }

    function sendElementToFront(elementId, fabricItemId) {
        if (!hasElement(elementId, fabricItemId)) {
            return;
        }
        self.fabricItems[elementId][fabricItemId].bringToFront();
        updateZIndices(elementId);
        render(elementId);
    }

    function updateZIndices(elementId) {
        const canvasObjects = self.canvas[elementId].getObjects();
        for (let i = 0; i < canvasObjects.length; i++) {
            canvasObjects[i].zIndex = getElementZIndex(elementId, canvasObjects[i].fabricItemId);
        }
    }

    function getElementZIndex(elementId, fabricItemId) {
        return self.canvas[elementId].getObjects().indexOf(self.fabricItems[elementId][fabricItemId]);
    }

    function sendElementToBack(elementId, fabricItemId) {
        if (!hasElement(elementId, fabricItemId)) {
            return;
        }

        self.fabricItems[elementId][fabricItemId].sendToBack();
        render(elementId);
    }

    function removeElement(elementId, fabricItemId) {
        if (self.canvas[elementId] && hasElement(elementId, fabricItemId)) {
            self.canvas[elementId].remove(
                self.fabricItems[elementId][fabricItemId]
            );
            render(elementId);
        }

        if (hasElement(elementId, fabricItemId)) {
            delete self.fabricItems[elementId][fabricItemId];
        }
    }

    function getElement(elementId, fabricItemId) {
        if(!hasElement(elementId, fabricItemId)) {
            return null;
        }

        return self.fabricItems[elementId][fabricItemId];
    }

    function getElementOptions(elementId, fabricItemId) {
        if(!hasElement(elementId, fabricItemId)) {
            return null;
        }
        const target = self.fabricItems[elementId][fabricItemId];
        let options = {
            fabricItemId: fabricItemId,
            itemType: target.itemType,
            left: target.left,
            top: target.top,
            width: target.width,
            height: target.height,
            angle: target.angle,
            scaleX: target.scaleX,
            scaleY: target.scaleY,
            skewX: target.skewX,
            skewY: target.skewY,
            originX: target.originX,
            originY: target.originY,
            zIndex: target.zIndex
        };

        if (target.itemType === 'text') {
            options.text = target.text;
            options.fontSize = target.fontSize;
            options.fontFamily = target.fontFamily;
            options.textAlign = target.textAlign;
            options.fill = target.fill;
            options.editable = target.editable;
        }

        if (target.itemType === 'image') {
            options.path = target.path;
        }

        if (target.itemType === 'clipArt') {
            options.path = target.path;
            options.fill = target.fill;
        }

        if (target.hasOwnProperty('colorMapping')) {
            options.colorMapping = target.colorMapping;
            options.isColorableSVG = true;
        }

        return options;
    }

    function setElementOptions(elementId, fabricItemId, options) {
        if(!hasElement(elementId, fabricItemId) || !options) {
            return;
        }

        const target = self.fabricItems[elementId][fabricItemId];
        target.set(options);
        target.setCoords();

        render(elementId);
    }

    function makeFile(elementId, size, fileName, callback) {
        if (!callback) {
            callback = function (snapshot) {};
        }

        makeBlob(elementId, size,(blob) => {
            callback(blobToFile(blob, fileName + '.png'));
        });
    }

    function makeBlob(elementId, size, callback) {
        if (!callback) {
            callback = function (blob) {};
        }

        makeDataUrl(elementId, size,(dataUrl) => {
            callback(dataUrlToBlob(dataUrl));
        });
    }

    function makeDataUrl(elementId, size, callback) {
        if (!callback) {
            callback = function (dataUrl) {};
        }
        // create a copy from fabric original canvas
        let canvasCopyBuffer = $document[0].createElement('canvas');
        let canvasCopy = new fabric.Canvas(canvasCopyBuffer);
        canvasCopy.loadFromJSON(JSON.stringify(self.canvas[elementId]), () => {
            // @todo research why we have to zoom the copied canvas to "1 / devicePixelRatio"
            // let devicePixelRatio = $window.devicePixelRatio || 1;
            // zoom copied canvas to 1 to prevent wrong cutouts
            canvasCopy.setWidth(size.width);
            canvasCopy.setHeight(size.height);
            canvasCopy.setZoom(1);
            canvasCopy.renderAll();
            // callback function
            callback(canvasCopy.toDataURL({
                format: 'png',
                left: size.left,
                top: size.top,
                width: size.width,
                height: size.height
            }));
        });
    }

    function dataUrlToBlob(dataUrl) {
        let arr = dataUrl.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new Blob([u8arr], {type:mime});
    }

    function blobToFile(theBlob, fileName){
        theBlob.lastModifiedDate = new Date();
        theBlob.name = fileName;
        return theBlob;
    }

    function hasElement(elementId, fabricItemId) {
        if(!self.fabricItems[elementId] || !self.fabricItems[elementId][fabricItemId]) {
            return false;
        }
        return true;
    }

    function canvasHasElement(elementId, fabricItemId) {
        if (!self.fabricItems[elementId] || !hasElement(elementId, fabricItemId)) {
            return false;
        }

        const canvasObjects = self.canvas[elementId].getObjects();
        for (let i = 0; i < canvasObjects.length; i++) {
            if (self.fabricItems[elementId][fabricItemId] === canvasObjects[i]) {
                return true;
            }
        }

        return false;
    }

    function getNextFabricItemId() {
        return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }

    function makeFabricText(elementId, input, options) {
        let fabricText = new fabric.IText(input.text, {
            ...options,
            ...getOutlineOptions()
        });

        //fabricText.setControlsVisibility(getControlVisibility());

        // set element
        setElement(
            elementId,
            input.fabricItemId,
            fabricText
        );
        return fabricText
    }

    function makeFabricImage(elementId, image, max, reAdd, clipArt, callback) {
        fabric.Image.fromURL(getUserImageUrl(image), (fabricImage) => {

            let dimensions = getScaledImageDimensions(
                max,
                image
            );
            let scale = dimensions.width / image.width;

            fabricImage.set({
                ...{
                    'originX': 'center',
                    'originY': 'center',
                    'width': image.width,
                    'height': image.height,
                    'scaleX': scale,
                    'scaleY': scale,
                    'itemType': 'image',
                    'fabricItemId': image.fabricItemId,
                    'path': image.path,
                    'top': image.top,
                    'left': image.left,
                    'zIndex': image.zIndex
                },
                ...getOutlineOptions()
            });

            if (clipArt) {
                fabricImage.set({
                    'itemType': 'clipArt'
                })
            }

            if (reAdd) {
                fabricImage.set(
                    {
                        'angle': image.angle,
                        'scaleX': image.scaleX,
                        'scaleY': image.scaleY,
                        'skewX': image.skewX,
                        'skewY': image.skewY
                    }
                )
            }

            //fabricImage.setControlsVisibility(getControlVisibility());

            // set element
            setElement(
                elementId,
                image.fabricItemId,
                fabricImage
            );
            return callback(fabricImage);
        });
    }

    function checkForClose(position, area) {

        if (position.x < area.left || position.x > (area.left + area.width) || position.y < area.top || position.y > (area.top + area.heigt)) {
            self.rootScope.$emit('CUSTOMIZE_PRODUCT_SAVE');
        }
    }

    function makeFabricSVG(elementId, image, max, reAdd, clipArt, callback) {
        let svgObjects = [];
        fabric.loadSVGFromString(image.html, (objects, options) => {
                let dimensions = getScaledImageDimensions(
                    max,
                    image
                );
                let scale = dimensions.width / image.width;
                let svgGroup = new fabric.Group(svgObjects);

                svgGroup.set({
                    ...{
                        'originX': 'center',
                        'originY': 'center',
                        'width': image.width,
                        'height': image.height,
                        'scaleX': scale,
                        'scaleY': scale,
                        'itemType': 'image',
                        'fabricItemId': image.fabricItemId,
                        'path': image.path,
                        'top': image.top,
                        'left': image.left,
                        'colorMapping': image.colorMapping,
                        'zIndex': image.zIndex
                    },
                    ...getOutlineOptions()
                });
                if (clipArt) {
                    svgGroup.set({
                        'itemType': 'clipArt'
                    })
                }
                if (reAdd) {
                    svgGroup.set(
                        {
                            'angle': image.angle,
                            'scaleX': image.scaleX,
                            'scaleY': image.scaleY,
                            'skewX': image.skewX,
                            'skewY': image.skewY,
                            'colorMapping': image.colorMapping
                        }
                    )
                }

                //svgGroup.setControlsVisibility(getControlVisibility());
                svgGroup.objectCaching = false;
                // set element
                setElement(
                    elementId,
                    image.fabricItemId,
                    svgGroup
                );
                return callback(svgGroup);
            },
            (item, object) => {
                svgObjects.push(object);
            });


    }

    function setSVGColor(canvasId, fabricElementId, pathId, color) {
        if (!hasElement(canvasId,fabricElementId)) {
            return false;
        }
        self.fabricItems[canvasId][fabricElementId].forEachObject((object) => {
            if (object.id === pathId) {
                object.fill = color;
                // set object dirty to force rerender
                object.dirty = true;
            }
        })
        for (let i = 0; i < self.fabricItems[canvasId][fabricElementId].colorMapping.length; i++) {
            if (self.fabricItems[canvasId][fabricElementId].colorMapping[i].id === pathId) {
                self.fabricItems[canvasId][fabricElementId].colorMapping[i].color = color;
            }
        }
        render(canvasId);
        return self.fabricItems[canvasId][fabricElementId].colorMapping;
    }

    function getUserImageUrl(image) {
        if (!image || !image.path) {
            console.error('Can not generate userImageUrl because off missing userImage.path.');
            return;
        }

        return APTO_API.media + image.path;
    }

    function getOutlineOptions() {
        return APTO_PLUGIN_IMAGE_UPLOAD_FABRIC_OUTLINE_OPTIONS;
    }

    function getControlVisibility() {
        return {
            tl: true,
            tr: true,
            bl: true,
            br: true,
            mb: false,
            ml: false,
            mr: false,
            mt: false,
            mtr: true
        };
    }

    function render(elementId) {
        if (self.canvas[elementId]) {
            self.canvas[elementId].renderAll();
        }
    }

    function clearElements(elementId) {
        self.fabricItems[elementId] = {};
        if (self.canvas.hasOwnProperty(elementId))  {
            self.canvas[elementId].remove(...self.canvas[elementId].getObjects());
        }
    }

    function scaleElementToFitCanvas(canvasId, elementId, size) {
        let maxWidth = size.width;
        let maxHeight = size.height;
        let elementWidth = self.fabricItems[canvasId][elementId].width;
        let elementHeight = self.fabricItems[canvasId][elementId].height;
        const areaRatio = maxWidth / maxHeight;
        const elementRatio = elementWidth / elementHeight;

        let referenceSize = elementHeight;
        let targetSize = maxHeight;

        if (elementRatio > areaRatio) {
            referenceSize = elementWidth;
            targetSize = maxWidth;
        }

        let newScalingFactor = targetSize / referenceSize;
        let safeBuffer = self.newItemSafeBuffer.default;
        if (self.fabricItems[canvasId][elementId].itemType === 'text') {
            safeBuffer = self.newItemSafeBuffer.text;
        }
        self.fabricItems[canvasId][elementId].scaleX = newScalingFactor*safeBuffer;
        self.fabricItems[canvasId][elementId].scaleY = newScalingFactor*safeBuffer;
        centerElement(canvasId, elementId, size);
    }

    function getScaledImageDimensions(max, userImage) {
        let width = null, height = null;

        if (userImage.width >= userImage.height) {
            width = max;
            height = Math.floor(max / (userImage.width / userImage.height));
        }

        if (userImage.height > userImage.width) {
            width = Math.floor(max / (userImage.height / userImage.width));
            height = max;
        }

        return {
            width: width,
            height: height
        }
    }

    function getSVGColors(canvasId, fabricElementId) {
        if (!hasElement(canvasId, fabricElementId)) {
            return null
        } else {
            return self.fabricItems[canvasId][fabricElementId].colorMapping;
        }
    }

    function checkIfItemInsidePrintArea(object, area) {
        if (!object) {
            return;
        }
        const coordsX = [
            object.aCoords.bl.x,
            object.aCoords.br.x,
            object.aCoords.tl.x,
            object.aCoords.tr.x,
        ];
        const coordsY = [
            object.aCoords.bl.y,
            object.aCoords.br.y,
            object.aCoords.tl.y,
            object.aCoords.tr.y,
        ];
        const borderCoords = {
            xMin: parseFloat(area.left),
            xMax: parseFloat(area.left) + parseFloat(area.width),
            yMin: parseFloat(area.top),
            yMax: parseFloat(area.top) + parseFloat(area.height)
        }

        for (let i = 0; i < coordsX.length; i++) {
            if (coordsX[i] < borderCoords.xMin || coordsX[i] > borderCoords.xMax) {
                markObjectAsOutOfBorder(object);
                return
            }
        }
        for (let i = 0; i < coordsY.length; i++) {
            if (coordsY[i] < borderCoords.yMin || coordsY[i] > borderCoords.yMax) {
                markObjectAsOutOfBorder(object);
                return
            }
        }
        if (!object.marked) {
            return;
        }
        removeMark(object);
    }

    function markObjectAsOutOfBorder(object) {
        if (object.marked) {
            return;
        }
        object.marked = true;
        if (object.itemType === "text") {
            object.backgroundColor = "rgba(255, 0, 0, 0.5)";
            object.dirty = true;
            return;
        }
        if (object.itemType === "clipArt") {
            for (let i = 0; i < object._objects.length; i++) {
                object._objects[i].backgroundColor = "rgba(255, 0, 0, 0.5)";
                object._objects[i].dirty = true;
            }
            return;
        }
        object.filters = [self.tintFilter];
        object.applyFilters();
    }

    function removeAllMarks(canvasId, callback) {
        let objectsUnmarked = 0;
        const canvasObjects = self.canvas[canvasId].getObjects();
        if (canvasObjects.length === 0) {
            callback();
        }
        for (let i = 0; i < canvasObjects.length; i++) {
            if (removeMark(canvasObjects[i])) {
                objectsUnmarked++;
            }
            if (objectsUnmarked === canvasObjects.length)  {
                callback();
            }
        }

    }

    function removeMark(object) {
        if (!object.marked) {
            return true;
        }
        object.marked = false;
        if (object.itemType === "text") {
            object.backgroundColor = "";
            object.dirty = true;
            return true;
        }
        if (object.itemType === "clipArt") {
            for (let i = 0; i < object._objects.length; i++) {
                object._objects[i].backgroundColor = "";
                object._objects[i].dirty = true;
            }
            return true;
        }
        object.filters = [];
        object.applyFilters();
        return true;
    }

    return {
        setCanvas: setCanvas,
        getCanvas: getCanvas,
        makeFile: makeFile,
        getElement: getElement,
        setElement: setElement,
        centerElement: centerElement,
        selectElement: selectElement,
        deselectElements: deselectElements,
        sendElementToFront: sendElementToFront,
        sendElementToBack: sendElementToBack,
        removeElement: removeElement,
        getElementOptions: getElementOptions,
        setElementOptions: setElementOptions,
        addElementToCanvas: addElementToCanvas,
        removeElementFromCanvas: removeElementFromCanvas,
        canvasHasElement: canvasHasElement,
        render: render,
        getNextFabricItemId: getNextFabricItemId,
        makeFabricText: makeFabricText,
        makeFabricImage: makeFabricImage,
        makeFabricSVG: makeFabricSVG,
        getControlVisibility: getControlVisibility,
        getSVGColors: getSVGColors,
        clearElements: clearElements,
        addElementsToCanvas: addElementsToCanvas,
        setReAddElements: setReAddElements,
        setSVGColor: setSVGColor,
        changeTextSize: changeTextSize,
        scaleElementToFitCanvas: scaleElementToFitCanvas,
        reRenderTexts: reRenderTexts,
        checkForClose: checkForClose,
        checkIfItemInsidePrintArea: checkIfItemInsidePrintArea,
        removeAllMarks: removeAllMarks,
        removeAllFabricElements: removeAllFabricElements
    };
};

Factory.$inject = FactoryInject;

export default ['FabricCanvasFactory', Factory];
