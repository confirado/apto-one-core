import Template from './category-tree.component.html';
const ControllerInject = ['$ngRedux','MessageBusFactory', 'LanguageFactory'];

class Controller {
    constructor ($ngRedux, MessageBusFactory, LanguageFactory) {
        this.translate = LanguageFactory.translate;
    }

    $onInit() {
        this.data = angular.copy(this.dataInput);
        this.orderBy = 'position';
        this.reverse = false;
        this.actions = angular.copy(this.actionsInput);
        this.treeOptions = {
            nodeChildren: "children",
            dirSelectable: false,
            injectClasses: {
                ul: "a1",
                li: "a2",
                liSelected: "a7",
                iExpanded: "a3",
                iCollapsed: "a4",
                iLeaf: "a5",
                label: "a6",
                labelSelected: "a8"
            }
        };
    }

    $onChanges(changes) {
        if (changes.dataInput) {
            this.data = angular.copy(this.dataInput);
        }
        if (changes.actionsInput) {
            this.actions = angular.copy(this.actionsInput);
        }
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        dataInput: '<data',
        actionsInput: '<actions'
    },
    template: Template,
    controller: Controller,
};

export default ['aptoCategoryTree', Component];