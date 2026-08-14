import { fabric } from 'fabric';

import AreaEditorTemplate from './area-editor.component.html';

const AreaEditorControllerInject = ['$scope', '$ngRedux', 'MessageBusFactory'];
class AreaEditorController {

    constructor($scope, $ngRedux, MessageBusFactory) {
        this.$scope = $scope;

        this.editorFabricCanvas = null;
        this.editorPrintableAreaRect = null;
        this.editorBackgroundImage = null;
        this.backgroundImageFileInput = null;

        this.printableArea = {
            identifier: '',
            left: 0,
            top: 0,
            width: 0,
            height: 0,
            data: {}
        };

        this.editorPrintableAreaShapes = [
            'None',
            'Circle',
            'Polygon'
        ];
        this.editorPrintableAreaShape = 'None';

        this.shapeObject = null;
        this.shapeObjectProperties = null;

        this.shapeStroke = 'lime';
        this.shapeFill = '';
        this.shapeStrokeWidth = 5;
        this.shapeStrokeDashArray = [5];
        this.shapeOpacity = 0.5;


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


        this.fetchRenderImages('b731a807-5118-4724-a03c-7c4f782e8460').then(result => {
            console.log(result);
        });
    }

    mapState(state) {
        return {
            section: state.sections
        };
    }

    $onInit() {
        this.initEditor();

        this.findSectionsOfCurrentPrintableArea();
    }

    findSectionsOfCurrentPrintableArea() {
        console.log(this.printableArea.identifier);


        if (!this.printableArea || !this.printableArea.identifier || this.printableArea.identifier === '') {
            return;
        }


        this.fetchAllProductIds().then((productValues) => {
            for (let i = 0; i < productValues.data.result.length; i++) {
                const productId = productValues.data.result[i];

                this.fetchSectionsElements(productId).then((sectionElementValues) => {
                    if (!sectionElementValues.data.result || !sectionElementValues.data.result.sections) {
                        return;
                    }

                    const sections = sectionElementValues.data.result.sections;

                    for (let j = 0; j < sections.length; j++) {
                        const section = sections[j];
                        const elements = section.elements;

                        for (let k = 0; k < elements.length; k++) {
                            const element = elements[k];

                           if (element.identifier === this.printableArea.identifier) {
                                const elementId = element.id;

                                this.fetchRenderImages(elementId).then((renderImageValues) => {
                                    const renderImages = renderImageValues.data.result.renderImages;

                                    console.log(renderImages);
                                }).catch(error => {
                                //    console.error("fetchRenderImages failed:", error);
                                });

                            }
                        }
                    }
                });
            }
        });
    }

    $onChanges = (changes) => {
        if (changes.identifier && this.printableArea.identifier === '') {
            this.printableArea.identifier = this.identifier;
            this.findSectionsOfCurrentPrintableArea();
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
                this.editorPrintableAreaShape = this.printableArea.data.shape;
            }
            else {
                this.editorPrintableAreaShape = 'None';
            }

            this.createShape(this.printableArea.data);
        }

        if (changes.left || changes.top || changes.width || changes.height || changes.data) {
            this.updateEditorPrintableArea();
        }
    }


    initEditor() {
        this.backgroundImageFileInput = document.getElementById("background-image-file");
        this.backgroundImageFileInput.addEventListener('change', () => {
            this.editorBackgroundImageSelected();
        });

        this.editorFabricCanvas = new fabric.Canvas('printableAreaEditor');

        this.editorFabricCanvas.on('mouse:down', (e) => {
            if (!this.shapeObjectProperties) {
                return;
            }

            switch (this.editorPrintableAreaShape) {
                case 'Circle':
                    this.printableArea.data.left = this.shapeObjectProperties.left;
                    this.printableArea.data.top = this.shapeObjectProperties.top;
                    this.printableArea.data.radius = this.shapeObjectProperties.radius;
                    break;
                case 'Polygon':
                    this.printableArea.data.left = this.shapeObjectProperties.left;
                    this.printableArea.data.top = this.shapeObjectProperties.top;

                    if (this.shapeObjectProperties.isCreating) {
                        const pointer = this.editorFabricCanvas.getPointer(e.e);

                        this.shapeObjectProperties.points.push({
                            x: pointer.x,
                            y: pointer.y
                        });

                        if (this.shapeObjectProperties.points.length >= 2) {
                            if (this.editorFabricCanvas.contains(this.shapeObject)) {
                                this.editorFabricCanvas.remove(this.shapeObject);
                            }

                            this.shapeObject = new fabric.Polygon(
                                [...this.shapeObjectProperties.points]
                            );

                            this.initShape(this.shapeObject);

                            this.editorFabricCanvas.add(this.shapeObject);
                            this.editorFabricCanvas.renderAll();
                        }

                        this.printableArea.data.points = this.shapeObjectProperties.points;
                    }
                    break;
                default:
                    break;
            }
        });

        if (!this.editorPrintableAreaRect) {
            this.editorPrintableAreaRect = new fabric.Rect({
                left: 0,
                top: 0,
                width: 0,
                height: 0,

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

            this.initShape(this.shapeObject);

            this.editorPrintableAreaRect.set({
                fill: '',
                stroke: 'red'
            });

            this.editorFabricCanvas.add(this.editorPrintableAreaRect);
        }

        this.editorFabricCanvas.renderAll();
    }


    clearObject(data) {
        if (this.shapeObject && this.editorFabricCanvas.contains(this.shapeObject)) {
            this.editorFabricCanvas.remove(this.shapeObject);
        }

        this.shapeObjectProperties = null;
        this.shapeObject = null;

        if (!data) {
            this.updateAreaData();
        }
    }

    createCircle(data) {
        this.clearObject(data);

        this.shapeObjectProperties = {
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

        this.editorFabricCanvas.add(this.shapeObject);
        this.editorFabricCanvas.renderAll();
    }

    createPolygon(data) {
        this.clearObject(data);

        this.shapeObjectProperties = {
            isCreating: data ? data.isCreating : false,
            points: data ? data.points : []
        };

        if (data) {
            this.shapeObject = new fabric.Polygon([
                ...data.points
            ]);

            this.initShape(this.shapeObject);

            this.editorFabricCanvas.add(this.shapeObject);
        }

        this.editorFabricCanvas.renderAll();
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

        console.log(e);

        const obj = e.target;

        const boundingRect = obj.getBoundingRect();

        this.shapeObjectProperties.left = boundingRect.left;
        this.shapeObjectProperties.top = boundingRect.top;

        this.updateAreaData();

        this.$scope.$applyAsync();
    }


    createShape(data) {
        const shape = data ? data.shape : this.editorPrintableAreaShape;

        switch (shape) {
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

        this.shapeObjectProperties.isCreating = true;
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
        this.editorFabricCanvas.requestRenderAll();

        this.updateAreaData();
    }


    updateEditorPrintableArea() {
        if (!this.editorPrintableAreaRect) {
            return;
        }

        this.editorPrintableAreaRect.set({
            top: this.printableArea.top,
            left: this.printableArea.left,
            width: this.printableArea.width ,
            height: this.printableArea.height
        });

        this.editorPrintableAreaRect.setCoords();

        let maxWidth = 0;
        let maxHeight = 0;

        [this.editorPrintableAreaRect, this.editorBackgroundImage]
            .filter(Boolean)
            .forEach(obj => {
                obj.setCoords();

                const bounds = obj.getBoundingRect(true, true);

                maxWidth = Math.max(
                    maxWidth,
                    bounds.left + bounds.width
                );

                maxHeight = Math.max(
                    maxHeight,
                    bounds.top + bounds.height
                );
            });

        maxWidth = Math.ceil(maxWidth);
        maxHeight = Math.ceil(maxHeight);

        this.editorFabricCanvas.setDimensions({
            width: maxWidth,
            height: maxHeight
        });

        this.editorFabricCanvas.calcOffset();
        this.editorFabricCanvas.requestRenderAll();
    }

    updateAreaData() {
        if (!this.printableArea || !this.printableArea.data) {
            return;
        }

        if (this.editorPrintableAreaShape === 'None') {
            this.printableArea.data.shape = '';
            this.printableArea.data.points = null;
        }
        else {
            this.printableArea.data.shape = this.editorPrintableAreaShape;
        }

        this.$scope.$applyAsync();
    }


    editorBackgroundImageSelected() {
        const file = this.backgroundImageFileInput.files[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        if (this.editorBackgroundImage) {
            this.editorFabricCanvas.remove(this.editorBackgroundImage);
            this.editorBackgroundImage = null;
        }

        reader.onload = (event) => {
            fabric.Image.fromURL(event.target.result, (img) => {
                this.editorBackgroundImage = img;

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

                this.editorFabricCanvas.add(img);
                this.editorFabricCanvas.sendToBack(img);
                this.editorFabricCanvas.renderAll();

                this.updateEditorPrintableArea();
            });
        };

        reader.readAsDataURL(file);
    }

    openEditorBackgroundImage() {
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
