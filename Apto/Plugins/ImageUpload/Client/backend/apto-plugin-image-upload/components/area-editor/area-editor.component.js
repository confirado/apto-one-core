import { fabric } from 'fabric';

import AreaEditorTemplate from './area-editor.component.html';

const AreaEditorControllerInject = ['$scope'];
class AreaEditorController {

    constructor($scope) {
        this.$scope = $scope;

        this.editorFabricCanvas = null;
        this.editorPrintableAreaRect = null;
        this.editorBackgroundImage = null;
        this.backgroundImageFileInput = null;

        this.printableArea = {
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
    }


    $onInit() {
        this.initEditor();
    }

    $onChanges = (changes) => {
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

                            this.lockShape(this.shapeObject);
                            this.initShapeProperties(this.shapeObject);

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

            this.initShapeProperties(this.shapeObject);

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

        if (data) {
            this.shapeObjectProperties = {
                left: data.left,
                top: data.top,
                radius: data.radius
            };
        }
        else {
            this.shapeObjectProperties = {
                left: 10,
                top: 10,
                radius: 50
            };
        }

        this.shapeObject = new fabric.Circle({
            left: this.shapeObjectProperties.left,
            top: this.shapeObjectProperties.top,
            radius: this.shapeObjectProperties.radius
        });

        this.initShapeProperties(this.shapeObject);

        this.shapeObject.on('modified', (e) => {
            const obj = e.target;

            this.shapeObjectProperties.left = obj.left;
            this.shapeObjectProperties.top = obj.top;

            this.updateAreaData();

            this.$scope.$applyAsync();
        });

        this.editorFabricCanvas.add(this.shapeObject);

        this.editorFabricCanvas.renderAll();
    }

    createPolygon(data) {
        this.clearObject(data);

        if (data) {
            this.shapeObjectProperties = {
                isCreating: data.isCreating,
                points: data.points
            }

            this.shapeObject = new fabric.Polygon([
                ...data.points
            ]);

            this.initShapeProperties(this.shapeObject);
            this.lockShape();

            this.editorFabricCanvas.add(this.shapeObject);

            this.editorFabricCanvas.renderAll();
        }
        else {
            this.shapeObjectProperties = {
                isCreating: false,
                points: []
            };
        }

        this.editorFabricCanvas.renderAll();
    }


    lockShape() {
        if (!this.shapeObject) {
            return;
        }

        this.shapeObject.set({
            lockScalingX: true,
            lockScalingY: true,

            lockRotation: true
        });
    }

    initShapeProperties(shapeObj) {
        if (!shapeObj) {
            return;
        }

        shapeObj.fill = this.shapeFill;
        shapeObj.stroke = this.shapeStroke;
        shapeObj.strokeWidth = this.shapeStrokeWidth;
        shapeObj.strokeDashArray = this.shapeStrokeDashArray;
        shapeObj.opacity = this.shapeOpacity;
    }


    createShape(data) {
        let shape = '';
        if (data) {
            shape = data.shape;
        }
        else {
            shape = this.editorPrintableAreaShape;
        }

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

        this.lockShape();
    }


    startCreatingPolygon() {
        if (!this.shapeObjectProperties) {
            return;
        }

        this.shapeObjectProperties.isCreating = true;
        this.$scope.$applyAsync();
    }

    stopCreatingPolygon() {
        if (!this.shapeObjectProperties) {
            return;
        }

        this.shapeObjectProperties.isCreating = false;
        this.$scope.$applyAsync();
    }


    updateShapeObject() {
        if (!this.shapeObject) {
            return;
        }

        this.shapeObject.set({
            left: this.shapeObjectProperties.left,
            top: this.shapeObjectProperties.top,
            radius: this.shapeObjectProperties.radius
        });

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
