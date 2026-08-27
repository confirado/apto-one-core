import { fabric } from 'fabric';

import AreaEditorTemplate from './area-editor.component.html';

const AreaEditorControllerInject = ['$window', '$scope', '$ngRedux', 'MessageBusFactory'];
class AreaEditorController {

    constructor($window, $scope, $ngRedux, MessageBusFactory) {
        this.$scope = $scope;

        this.canvas = null;
        this.backgroundImage = null;
        this.backgroundImageFileInput = null;

        this.printableArea = {
            identifier: '',
            perspective: '',
            left: 0,
            top: 0,
            width: 0,
            height: 0,
            data: {}
        };

        this.defaultPrintableAreaShape = 'Rectangle';

        this.printableAreaShapes = [
            this.defaultPrintableAreaShape,
            'Circle',
            'Polygon'
        ];
        this.printableAreaShape = this.defaultPrintableAreaShape;

        this.shapeObject = null;
        this.shapeObjectProperties = null;

        this.shapeStroke = 'lime';
        this.shapeFill = '';
        this.shapeStrokeWidth = 5;
        this.shapeStrokeDashArray = [5];
        this.shapeOpacity = 0.5;


        this.updateCanvasSize = () => {
            if (!this.canvas) {
                return;
            }

            const printableAreaEditorContainer = document.getElementById('printableAreaEditorContainer');
            if (!printableAreaEditorContainer) {
                return;
            }

            let width = printableAreaEditorContainer.clientWidth;
            let height = printableAreaEditorContainer.clientHeight;

            this.canvas.getObjects().forEach((obj) => {
                const objBoundingRect = obj.getBoundingRect();

                width = Math.max(width, objBoundingRect.left + objBoundingRect.width);
                height = Math.max(height, objBoundingRect.top + objBoundingRect.height);
            });

            this.canvas.setDimensions({
                width: width,
                height: height
            });
        };

        angular.element($window).bind('resize', this.updateCanvasSize);


        this.fetchAllProductIds = () => {
            return MessageBusFactory.query('FindProductIdsByFilter', [{
                searchString: '',
                categories: []
            }]);
        }

        this.fetchSectionsElements = (productId) => {
            return MessageBusFactory.query('FindProductSectionsElements', [
                productId
            ]);
        }

        this.fetchRenderImages = (elementId) => {
            return MessageBusFactory.query('FindElementRenderImages', [
                elementId
            ]);
        }
    }

    mapState(state) {
        return {
            section: state.sections
        };
    }

    $onInit() {
        this.initEditor();
    }

    getSectionsForPrintableArea(sectionElements) {
        for (const sectionElement of sectionElements) {
            if (!sectionElement
                || !sectionElement.data
                || !sectionElement.data.result
                || !sectionElement.data.result.sections) {
                continue;
            }

            const sections = sectionElement.data.result.sections;

            for (const section of sections) {
                for (const element of section.elements) {
                    if (element.identifier === this.printableArea.identifier) {
                        return sections;
                    }
                }
            }
        }

        return null;
    }

    findProductWhereCurrentPrintableAreaIsUsed() {
        return this.fetchAllProductIds().then((productValues) => {
            const promises = [];

            const productIds = productValues.data.result;

            for (const productId of productIds) {
                promises.push(this.fetchSectionsElements(productId));
            }

            return Promise.all(promises);
        })
        .then((sectionElements) => {
            const sectionsList = [];

            const sectionsForPrintableArea = this.getSectionsForPrintableArea(sectionElements);
            if (sectionsForPrintableArea) {
                for (const section of sectionsForPrintableArea) {
                    if (!Array.isArray(section.elements)) {
                        continue;
                    }

                    sectionsList.push(section);
                }
            }

            return sectionsList;
        });
    }

    findRenderImagesForProduct(perspective, callback) {
        this.findProductWhereCurrentPrintableAreaIsUsed().then(sections => {
            this.findRenderImages(sections, perspective).then((renderImages) => {
                callback(renderImages);
            });
        });
    }

    findRenderImages(sections, perspective) {
        const promises = [];

        for (let j = 0; j < sections.length; j++) {
            const section = sections[j];
            const elements = section.elements;

            if (!Array.isArray(elements)) {
                continue;
            }

            if (elements.length > 0 && elements[0].id) {
                promises.push(this.fetchRenderImages(elements[0].id));
            }
        }


        return Promise.all(promises).then((renderImageValuesList) => {
            const renderImageList = [];

            for (const renderImageValues of renderImageValuesList) {
                if (!renderImageValues.data
                || !renderImageValues.data.result
                || !renderImageValues.data.result.renderImages) {
                    continue;
                }

                const renderImageResults = renderImageValues.data.result.renderImages;
                if (renderImageResults && renderImageResults.length > 0) {
                    for (const renderImage of renderImageResults) {
                        if (renderImage.perspective === perspective) {
                            renderImageList.push(renderImage);
                        }
                    }
                }
            }

            return renderImageList;
        });
    }

    mergeImages(urls) {
        return Promise.all(
            urls.map(url => new Promise((resolve, reject) => {
                const img = new Image();

                img.crossOrigin = 'anonymous';

                img.onload = () => resolve(img);
                img.onerror = reject;

                img.src = url;
            }))).then(images => {
                const width = Math.max(...images.map(img => img.width));
                const height = Math.max(...images.map(img => img.height));

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');

                images.forEach(img => {
                    ctx.drawImage(img, 0, 0);
                });

                return canvas;
            }
        );
    }


    getRootPath() {
        const path = location.href;

        const regexWeb = /\/web\/#/;
        const regexWebLocale = /\/web\/[a-zA-Z_]+\/#/;
        const regexLocale = /\/[a-zA-Z_]+\/#/;

        const matchRegexWeb = path.match(regexWeb);
        const matchRegexWebLocale = path.match(regexWebLocale);
        const matchRegexLocale = path.match(regexLocale);

        const slash = '/';
        const web = '/web/';

        if (matchRegexWebLocale) {
            return path.substring(0, path.indexOf(matchRegexWebLocale[0])) + web;
        }
        else if (matchRegexWeb) {
            return path.substring(0, path.indexOf(matchRegexWeb[0])) + web;
        }
        else if (matchRegexLocale) {
            return path.substring(0, path.indexOf(matchRegexLocale[0])) + slash;
        }
        else {
            return '';
        }
    }

    getMediaPath(path) {
        const rootPath = this.getRootPath();
        const mediaPath = 'public/media/';
        if (path) {
            return rootPath + mediaPath + path;
        }
        return rootPath + mediaPath;
    }


    drawRenderImage() {
        if (!this.printableArea
        || !this.printableArea.identifier
        || this.printableArea.identifier === ''
        || !this.printableArea.perspective
        || this.printableArea.perspective === '') {
            return;
        }


        this.removeBackgroundImage();

        if (!this.printableArea.perspective.startsWith('persp')) {
            return;
        }

        this.findRenderImagesForProduct(this.printableArea.perspective, (renderImages) => {
            if (renderImages && renderImages.length > 0) {
                const imagePaths = [];

                for (const renderImage of renderImages) {
                    for (const mf of renderImage.mediaFile) {
                        imagePaths.push(this.getMediaPath() + mf.path + '/' + mf.filename + '.' + mf.extension);
                    }
                }

                this.mergeImages(imagePaths)
                    .then(canvas => {
                        const img = new fabric.Image(canvas);

                        this.removeBackgroundImage();
                        this.backgroundImage = img;

                        this.lockObject(img, true);

                        this.canvas.add(img);
                        this.canvas.sendToBack(img);
                        this.canvas.renderAll();
                    });
            }
        });
    }

    $onChanges = (changes) => {
        if (changes.identifier) {
            let prevIdentifier = this.printableArea.identifier;

            if (this.identifier !== prevIdentifier) {
                this.printableArea.identifier = this.identifier;
                this.drawRenderImage();
            }
        }

        if (changes.perspective) {
            let prevPerspective = this.printableArea.perspective;

            if (this.perspective !== prevPerspective) {
                this.printableArea.perspective = this.perspective;
                this.drawRenderImage();
            }
        }

        if (changes.left) {
            this.printableArea.left = angular.copy(this.left);
        }

        if (changes.top) {
            this.printableArea.top = angular.copy(this.top);
        }

        if (changes.width) {
            this.printableArea.width = angular.copy(this.width);
        }

        if (changes.height) {
            this.printableArea.height = angular.copy(this.height);
        }

        if (changes.data) {
            // TODO fix reference
            this.printableArea.data = this.data;

            if (this.printableArea.data && this.printableArea.data.shape) {
                this.printableAreaShape = this.printableArea.data.shape;
            }
            else {
                this.printableAreaShape = this.defaultPrintableAreaShape;
            }

            this.createShape(this.printableArea.data);
        }
    }


    initEditor() {
        this.backgroundImageFileInput = document.getElementById("background-image-file");
        this.backgroundImageFileInput.addEventListener('change', () => {
            this.backgroundImageSelected();
        });

        this.canvas = new fabric.Canvas('printableAreaEditor');

        this.updateCanvasSize();

        this.canvas.on('mouse:down', (e) => {
            if (!this.shapeObjectProperties) {
                return;
            }

            this.updatePrintableAreaLocation();

            switch (this.printableAreaShape) {
                case 'Rectangle':
                    this.printableArea.data.width = this.shapeObjectProperties.width;
                    this.printableArea.data.height = this.shapeObjectProperties.height;
                    break;
                case 'Circle':
                    this.printableArea.data.radius = this.shapeObjectProperties.radius;
                    break;
                case 'Polygon':
                    if (this.shapeObjectProperties.isCreating) {
                        const pointer = this.canvas.getPointer(e.e);

                        this.shapeObjectProperties.points.push({
                            x: pointer.x,
                            y: pointer.y
                        });

                        if (this.shapeObjectProperties.points.length >= 2) {
                            if (this.canvas.contains(this.shapeObject)) {
                                this.canvas.remove(this.shapeObject);
                            }

                            this.shapeObject = new fabric.Polygon(
                                [...this.shapeObjectProperties.points]
                            );

                            this.initShape(this.shapeObject);

                            this.canvas.add(this.shapeObject);
                            this.canvas.renderAll();
                        }

                        this.printableArea.data.points = this.shapeObjectProperties.points;
                    }
                    break;
                default:
                    break;
            }
        });

        this.canvas.renderAll();
    }


    updatePrintableAreaLocation() {
        if (!this.printableArea || !this.printableArea.data || !this.shapeObjectProperties) {
            return;
        }

        this.printableArea.data.left = this.shapeObjectProperties.left;
        this.printableArea.data.top = this.shapeObjectProperties.top;
    }


    clearObject(data) {
        if (this.shapeObject && this.canvas.contains(this.shapeObject)) {
            this.canvas.remove(this.shapeObject);
        }

        this.shapeObjectProperties = null;
        this.shapeObject = null;

        if (!data) {
            this.updateAreaData();
        }
    }

    createRectangle(data) {
        this.clearObject(data);

        this.shapeObjectProperties = {
            shape: 'Rectangle',
            left: data ? data.left : 0,
            top: data ? data.top: 0,
            width: data ? data.width : 100,
            height: data ? data.height : 50
        };

        this.shapeObject = new fabric.Rect({
            width: this.shapeObjectProperties.width,
            height: this.shapeObjectProperties.height
        });

        this.updateShapeObjectLocation();

        this.initShape(this.shapeObject);

        this.canvas.add(this.shapeObject);
        this.canvas.renderAll();
    }

    createCircle(data) {
        this.clearObject(data);

        this.shapeObjectProperties = {
            shape: 'Circle',
            left: data ? data.left : 0,
            top: data ? data.top : 0,
            radius: data ? data.radius : 50
        };

        this.shapeObject = new fabric.Circle({
            radius: this.shapeObjectProperties.radius
        });

        this.updateShapeObjectLocation();

        this.initShape(this.shapeObject);

        this.canvas.add(this.shapeObject);
        this.canvas.renderAll();
    }

    createPolygon(data) {
        this.clearObject(data);

        this.shapeObjectProperties = {
            shape: 'Polygon',
            left: data ? data.left : 0,
            top: data ? data.top : 0,
            isCreating: data ? data.isCreating : false,
            points: data ? data.points : []
        };

        if (data) {
            this.shapeObject = new fabric.Polygon([
                ...data.points
            ]);

            this.updateShapeObjectLocation();

            this.initShape(this.shapeObject);

            this.canvas.add(this.shapeObject);
        }

        this.canvas.renderAll();
    }

    lockObject(obj, isBackgroundObject) {
        obj.set({
            selectable: true,
            evented: true,
            hasControls: false,
            hasBorders: true,
            lockScalingX: true,
            lockScalingY: true,
            lockRotation: true
        });

        if (isBackgroundObject) {
            obj.set({
                selectable: false,
                evented: false,
                hasControls: false,
                hasBorders: false,
                lockMovementX: true,
                lockMovementY: true,
                lockScalingX: true,
                lockScalingY: true,
                lockRotation: true
            });
        }
    }

    initShape(shapeObj) {
        if (!shapeObj) {
            return;
        }

        shapeObj.set({
            fill: this.shapeFill,
            stroke: this.shapeStroke,
            strokeWidth: this.shapeStrokeWidth,
            strokeDashArray: this.shapeStrokeDashArray,
            opacity: this.shapeOpacity,
            evented: true
        });

        this.lockObject(shapeObj, false);

        shapeObj.on({
            moving: this.handleShapeTransform,
            rotating: this.handleShapeTransform,
            scaling: this.handleShapeTransform,
            modified: this.handleShapeTransform
        });
    }

    handleShapeTransform = (e) => {
        if (!e.target || !this.shapeObjectProperties) {
            return;
        }


        this.saveShapeObjectLocation();
        this.updateAreaData();
        this.updateCanvasSize();

        this.$scope.$applyAsync();
    }

    saveShapeObjectLocation() {
        const obj = this.shapeObject;
        const boundingRect = obj.getBoundingRect();
        this.shapeObjectProperties.left = boundingRect.left;
        this.shapeObjectProperties.top = boundingRect.top;
    }


    createShape(data) {
        const shape = data ? data.shape : this.printableAreaShape;

        switch (shape) {
            case 'Rectangle':
                this.createRectangle(data);
                break;
            case 'Circle':
                this.createCircle(data);
                break;
            case 'Polygon':
                this.createPolygon(data);
                break;
            default:
                this.clearObject();
                break;
        }
    }


    markCreatingPolygon(isCreating) {
        if (!this.shapeObjectProperties) {
            return;
        }

        this.shapeObjectProperties.isCreating = isCreating;
        this.saveShapeObjectLocation();
        this.$scope.$applyAsync();
    }

    startCreatingPolygon() {
        this.markCreatingPolygon(true);
    }

    stopCreatingPolygon() {
        this.markCreatingPolygon(false);
    }


    isShapeObjectDefined() {
        if (!this.shapeObject) {
            return false;
        }

        if (!this.shapeObjectProperties || !this.shapeObjectProperties.shape) {
            return false;
        }

        return true;
    }

    updateShapeObjectLocation() {
        if (!this.isShapeObjectDefined()) {
            return;
        }

        this.shapeObject.set({
            left: this.shapeObjectProperties.left,
            top: this.shapeObjectProperties.top
        });
    }

    updateShapeObject() {
        if (!this.isShapeObjectDefined()) {
            return;
        }

        this.updateShapeObjectLocation();

        switch (this.shapeObjectProperties.shape) {
            case 'Rectangle':
                this.shapeObject.set({
                    width: this.shapeObjectProperties.width,
                    height: this.shapeObjectProperties.height
                });
                break;
            case 'Circle':
                this.shapeObject.set({
                    radius: this.shapeObjectProperties.radius
                });
                break;
            case 'Polygon':
                this.shapeObject.set({
                    points: this.shapeObjectProperties.points
                });
                break;
            default:
                break;
        }

        this.shapeObject.setCoords();
        this.canvas.requestRenderAll();

        this.updateAreaData();
    }

    updateAreaData() {
        if (!this.printableArea || !this.printableArea.data) {
            return;
        }

        this.printableArea.data.shape = this.printableAreaShape;

        this.$scope.$applyAsync();
    }


    removeBackgroundImage() {
        if (this.backgroundImage) {
            this.canvas.remove(this.backgroundImage);
            this.backgroundImage = null;
        }
    }

    backgroundImageSelected() {
        const file = this.backgroundImageFileInput.files[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        this.removeBackgroundImage();

        reader.onload = (event) => {
            fabric.Image.fromURL(event.target.result, (img) => {
                this.removeBackgroundImage();
                this.backgroundImage = img;

                this.lockObject(img, true);

                this.canvas.add(img);
                this.canvas.sendToBack(img);
                this.canvas.renderAll();

                this.updateCanvasSize();
            });
        };

        reader.readAsDataURL(file);
    }

    openBackgroundImage() {
        document.getElementById('background-image-file').click();
    }
}

AreaEditorController.$inject = AreaEditorControllerInject;

const AreaEditorComponent = {
    bindings: {
        definitionValidation: '&',
        identifier: '<',
        perspective: '<',
        left: '<',
        top: '<',
        width: '<',
        height: '<',
        data: '<'
    },
    template: AreaEditorTemplate,
    controller: AreaEditorController
};

export default ['aptoAreaEditorElement', AreaEditorComponent];
