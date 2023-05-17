import { fabric } from '../../libs/fabric/fabric';

import ContainerController from 'apto-base/components/apto-container.controller.js';
import Template from './image-upload-control.component.html';

const ControllerInject = ['$window', '$document', '$timeout', '$ngRedux', 'FabricCanvasFactory', 'ImageUploadDefinitionActions'];
class Controller extends ContainerController {
    constructor($window, $document, $timeout, $ngRedux, FabricCanvasFactory, ImageUploadDefinitionActions) {
        // call parent constructor
        super($ngRedux);

        // init services
        this.window = $window;
        this.document = $document[0];
        this.timeout = $timeout;
        this.fabricCanvasFactory = FabricCanvasFactory;
        this.imageUploadActions = ImageUploadDefinitionActions;

        // init properties
        this.mediaUrl = APTO_API.media;
        this.userImage = null;
        this.userText = null;

        // init fabric
        this.options = null;
    }

    connectProps() {
        return (state) => {
            const elementId = state.pluginImageUploadDefinition.activeElement;
            let mapping = {
                editable: false,
                background: null,
                element: null,
                activeElement: null
            };

            if (elementId) {
                mapping = {
                    editable: state.pluginImageUploadDefinition.elements[elementId].editable,
                    background: state.pluginImageUploadDefinition.elements[elementId].background,
                    element: state.pluginImageUploadDefinition.elements[elementId].element,
                    activeElement: elementId
                };
            }

            // return state mapping object
            return mapping;
        }
    }

    connectActions() {
        // actions mapping object
        return {
            setActiveFabricItemId: this.imageUploadActions.setCurrentItem
        }
    }

    connectRedux() {
        this.eventListeners.push(
            this.ngRedux.connect(
                this.connectProps(),
                this.connectActions()
            )((selectedState, actions) => {
                this.onStateChange(selectedState);
                this.actions = actions;
            })
        );
    }

    $onInit() {
        // call parent $onInit
        super.$onInit();
    }

    initOptions() {
        if (null === this.state.element) {
            return;
        }

        const staticValues = this.state.element.definition.staticValues;

        this.options = {
            width: staticValues.background.width,
            height: staticValues.background.height,
            area: staticValues.background.area
        };
    }

    onStateChange(state) {
        if (this.state.editable !== state.editable) {
            this.onEditableChange(state.editable)
        }

        this.state = state;

        // init options
        this.initOptions();
    }

    onEditableChange(editable) {
    }

    onCanvasCreated(canvas) {
        // set canvas
        this.fabricCanvasFactory.setCanvas(this.state.element.id, canvas);

        // add user image
        if (this.state.userImageOnCanvas) {
            this.addUserImageToCanvas();
        }

        canvas.on({
            'mouse:up': this.testMouse.bind(this),
            'selection:created': this.selectionUpdated.bind(this),
            'selection:updated': this.selectionUpdated.bind(this),
            'selection:cleared': this.selectionCleared.bind(this),
            'object:moving': this.onObjectChanged.bind(this),
            'object:scaling': this.onObjectChanged.bind(this),
            'object:rotating': this.onObjectChanged.bind(this),
            'object:skewing': this.onObjectChanged.bind(this)
        })
    }

    testMouse(e) {
        if (!e.target) {
            this.fabricCanvasFactory.checkForClose(e.pointer, this.state.element.definition.staticValues.background.area);
        }
    }


    selectionUpdated(e) {
        this.actions.setActiveFabricItemId(e.target.fabricItemId);
        if (e.target) {
            this.fabricCanvasFactory.sendElementToFront(this.state.element.id, e.target.fabricItemId);
        }
    }

    onObjectChanged(e) {
        if (!e.target) {
            return;
        }
        this.fabricCanvasFactory.checkIfItemInsidePrintArea(e.target, this.options.area);
    }

    selectionCleared(e) {
        this.actions.setActiveFabricItemId(null);
    }

    onCanvasDestroyed(canvas) {
    }

    getCurrentCanvas() {
        return this.fabricCanvasFactory.getCanvas(this.state.element.id);
    }

    log(message, value) {
        console.error(message, angular.copy(value));
    }
}

Controller.$inject = ControllerInject;

const Component = {
    template: Template,
    controller: Controller
};

export default ['aptoImageUploadElementControl', Component];
