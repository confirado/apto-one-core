import { fabric } from 'fabric';

import AreaEditorTemplate from './area-editor.component.html';

const AreaEditorControllerInject = ['$scope', '$ngRedux', 'ElementActions', 'ProductActions'];
class AreaEditorController {

    constructor($scope, $ngRedux, ElementActions, ProductActions) {
        this.$scope = $scope;

        this.editorFabricCanvas = null;
        this.editorPrintableAreaRect = null;
        this.editorBackgroundImage = null;
        this.backgroundImageFileInput = null;

        this.printableArea = {
            left: 0,
            top: 0,
            width: 0,
            height: 0
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
        this.initFabricJs();
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

        if (changes.left || changes.top || changes.width || changes.height) {
            this.updateEditorPrintableArea();
        }
    }


    initFabricJs() {
        setTimeout(() => {
            this.initEditor();
        }, 0)
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
                case 'Polygon':
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

                            this.shapeObject.fill = this.shapeFill;
                            this.shapeObject.stroke = this.shapeStroke;
                            this.shapeObject.strokeWidth = this.shapeStrokeWidth;
                            this.shapeObject.strokeDashArray = this.shapeStrokeDashArray;
                            this.shapeObject.opacity = this.shapeOpacity;

                            this.editorFabricCanvas.add(this.shapeObject);
                            this.editorFabricCanvas.renderAll();
                        }
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

                fill: '',
                stroke: 'red',
                strokeWidth: this.shapeStrokeWidth,
                strokeDashArray: this.shapeStrokeDashArray,
                opacity: this.shapeOpacity,

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

            this.editorFabricCanvas.add(this.editorPrintableAreaRect);
        }

        this.editorFabricCanvas.renderAll();
    }


    clearObject() {
        if (this.shapeObject && this.editorFabricCanvas.contains(this.shapeObject)) {
            this.editorFabricCanvas.remove(this.shapeObject);
        }

        this.shapeObjectProperties = null;
        this.shapeObject = null;
    }

    createCircle(x, y, r) {
        this.clearObject();

        this.shapeObjectProperties = {
            left: x,
            top: y,
            radius: r
        };

        this.shapeObject = new fabric.Circle({
            left: x,
            top: y,
            radius: r,

            fill: this.shapeFill,
            stroke: this.shapeStroke,
            strokeWidth: this.shapeStrokeWidth,

            lockScalingX: true,
            lockScalingY: true,

            lockRotation: true
        });

        this.shapeObject.on('modified', (e) => {
            const obj = e.target;

            this.shapeObjectProperties.left = obj.left;
            this.shapeObjectProperties.top = obj.top;

            this.$scope.$applyAsync();
        });

        this.editorFabricCanvas.add(this.shapeObject);

        this.editorFabricCanvas.renderAll();
    }

    createPolygon() {
        this.clearObject();

        this.shapeObjectProperties = {
            isCreating: false,
            points: []
        };

        this.editorFabricCanvas.renderAll();
    }


    onChangeShape() {
        switch (this.editorPrintableAreaShape) {
            case 'Circle':
                this.createCircle(10, 10, 50);
                break;
            case 'Polygon':
                this.createPolygon();
                break;
            default:
                this.clearObject();
                break;
        }
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
        height: '<'
    },
    template: AreaEditorTemplate,
    controller: AreaEditorController
};

export default ['aptoAreaEditorElement', AreaEditorComponent];
