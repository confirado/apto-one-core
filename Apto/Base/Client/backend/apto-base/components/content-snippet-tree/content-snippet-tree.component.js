import ContentSnippetTreeTemplate from './content-snippet-tree.component.html';
const ContentSnippetTreeControllerInject = ['$ngRedux','MessageBusFactory', 'LanguageFactory'];

class ContentSnippetTreeController {
    constructor ($ngRedux, MessageBusFactory, LanguageFactory) {
        this.translate = LanguageFactory.translate;
    }

    allowAdd(node) {
        if (node.content === null || Array.isArray(node.content)) {
            return true;
        }
        return false;
    }

    $onInit() {
        this.data = angular.copy(this.dataInput);
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

ContentSnippetTreeController.$inject = ContentSnippetTreeControllerInject;

const ContentSnippetTreeComponent = {
    bindings: {
        dataInput: '<data',
        actionsInput: '<actions'
    },
    template: ContentSnippetTreeTemplate,
    controller: ContentSnippetTreeController,
};

export default ['aptoContentSnippetTree', ContentSnippetTreeComponent];