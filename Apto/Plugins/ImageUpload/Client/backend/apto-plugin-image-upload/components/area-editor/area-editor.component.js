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

    findRenderImagesForProduct(callback) {
        this.findProductWhereCurrentPrintableAreaIsUsed().then(sections => {
            this.findRenderImages(sections).then((renderImages) => {
                callback(renderImages);
            });
        });
    }

    findRenderImages(sections) {
        const promises = [];

        for (let j = 0; j < sections.length; j++) {
            const section = sections[j];
            const elements = section.elements;

            if (!Array.isArray(elements)) {
                continue;
            }

            for (let k = 0; k < elements.length; k++) {
                const element = elements[k];
                const elementId = element.id;

                promises.push(this.fetchRenderImages(elementId));

                break;
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
                        renderImageList.push(renderImage);
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
        if (!this.printableArea || !this.printableArea.identifier || this.printableArea.identifier === '') {
            return;
        }


        this.findRenderImagesForProduct((renderImages) => {
            if (renderImages && renderImages.length > 0) {
                const imagePaths = [];

                for (const renderImage of renderImages) {
                    if (renderImage.perspective === 'persp1') {

                        for (const mf of renderImage.mediaFile) {
                            imagePaths.push(this.getMediaPath() + mf.path + '/' + mf.filename + '.' + mf.extension);
                        }
                    }
                }

                this.mergeImages(imagePaths)
                    .then(canvas => {
                        const img = new fabric.Image(canvas);

                        this.backgroundImage = img;

                        img.set({
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

                        this.canvas.add(img);
                        this.canvas.sendToBack(img);
                        this.canvas.renderAll();
                    });
            }
        });
    }

    $onChanges = (changes) => {
        if (changes.identifier && this.printableArea.identifier === '') {
            this.printableArea.identifier = this.identifier;
            this.drawRenderImage();
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

            switch (this.printableAreaShape) {
                case 'Rectangle':
                    this.printableArea.data.left = this.shapeObjectProperties.left;
                    this.printableArea.data.top = this.shapeObjectProperties.top;
                    this.printableArea.data.width = this.shapeObjectProperties.width;
                    this.printableArea.data.height = this.shapeObjectProperties.height;
                    break;
                case 'Circle':
                    this.printableArea.data.left = this.shapeObjectProperties.left;
                    this.printableArea.data.top = this.shapeObjectProperties.top;
                    this.printableArea.data.radius = this.shapeObjectProperties.radius;
                    break;
                case 'Polygon':
                    this.printableArea.data.left = this.shapeObjectProperties.left;
                    this.printableArea.data.top = this.shapeObjectProperties.top;

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
            left: data ? data.left : 10,
            top: data ? data.top: 10,
            width: data ? data.width : 100,
            height: data ? data.height : 50
        };

        this.shapeObject = new fabric.Rect({
            left: this.shapeObjectProperties.left,
            top: this.shapeObjectProperties.top,
            width: this.shapeObjectProperties.width,
            height: this.shapeObjectProperties.height
        });

        this.initShape(this.shapeObject);

        this.canvas.add(this.shapeObject);
        this.canvas.renderAll();
    }

    createCircle(data) {
        this.clearObject(data);

        this.shapeObjectProperties = {
            shape: 'Circle',
            left: data ? data.left : 10,
            top: data ? data.top : 10,
            radius: data ? data.radius : 50
        };

        this.shapeObject = new fabric.Circle({
            left: this.shapeObjectProperties.left,
            top: this.shapeObjectProperties.top,
            radius: this.shapeObjectProperties.radius
        });

        this.initShape(this.shapeObject);

        this.canvas.add(this.shapeObject);
        this.canvas.renderAll();
    }

    createPolygon(data) {
        this.clearObject(data);

        this.shapeObjectProperties = {
            shape: 'Polygon',
            isCreating: data ? data.isCreating : false,
            points: data ? data.points : []
        };

        if (data) {
            this.shapeObject = new fabric.Polygon([
                ...data.points
            ]);

            this.initShape(this.shapeObject);

            this.canvas.add(this.shapeObject);
        }

        this.canvas.renderAll();
    }


    lockShape(shapeObj) {
        if (!shapeObj) {
            return;
        }

        shapeObj.set({
            lockScalingX: true,
            lockScalingY: true,
            lockRotation: true
        });
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

        this.lockShape(shapeObj);

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


        const obj = e.target;

        const boundingRect = obj.getBoundingRect();

        this.shapeObjectProperties.left = boundingRect.left;
        this.shapeObjectProperties.top = boundingRect.top;

        this.updateAreaData();

        this.updateCanvasSize();

        this.$scope.$applyAsync();
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
        this.$scope.$applyAsync();
    }

    startCreatingPolygon() {
        this.markCreatingPolygon(true);
    }

    stopCreatingPolygon() {
        this.markCreatingPolygon(false);
    }


    updateShapeObject() {
        if (!this.shapeObject) {
            return;
        }

        if (!this.shapeObjectProperties || !this.shapeObjectProperties.shape) {
            return;
        }

        switch (this.shapeObjectProperties.shape) {
            case 'Rectangle':
                this.shapeObject.set({
                    left: this.shapeObjectProperties.left,
                    top: this.shapeObjectProperties.top,
                    width: this.shapeObjectProperties.width,
                    height: this.shapeObjectProperties.height
                });
                break;
            case 'Circle':
                this.shapeObject.set({
                    left: this.shapeObjectProperties.left,
                    top: this.shapeObjectProperties.top,
                    radius: this.shapeObjectProperties.radius
                });
                break;
            case 'Polygon':
                this.shapeObject.set({
                    left: this.shapeObjectProperties.left,
                    top: this.shapeObjectProperties.top,
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


    backgroundImageSelected() {
        const file = this.backgroundImageFileInput.files[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        if (this.backgroundImage) {
            this.canvas.remove(this.backgroundImage);
            this.backgroundImage = null;
        }

        reader.onload = (event) => {
            fabric.Image.fromURL(event.target.result, (img) => {
                this.backgroundImage = img;

                img.set({
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
