import { fabric } from 'fabric';

import AreaEditorTemplate from './area-editor.component.html';

const AreaEditorControllerInject = ['$ngRedux', 'ElementActions', 'ProductActions'];
class AreaEditorController {

    constructor($ngRedux, ElementActions, ProductActions) {
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
            'Circle',
            'Polygon'
        ];
        this.editorPrintableAreaShape = 'Polygon';
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
        this.backgroundImageFileInput.addEventListener("change", () => {
            this.editorBackgroundImageSelected();
        });

        this.editorFabricCanvas = new fabric.Canvas('printableAreaEditor');

        if (!this.editorPrintableAreaRect) {
            this.editorPrintableAreaRect = new fabric.Rect({
                left: 0,
                top: 0,
                width: 0,
                height: 0,

                fill: '',
                stroke: 'red',
                strokeWidth: 5,
                strokeDashArray: [5],
                opacity: 0.5,

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


        console.log(this.editorFabricCanvas.getObjects());

        console.log(this.editorFabricCanvas);

        console.log(this.editorFabricCanvas.version);
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
