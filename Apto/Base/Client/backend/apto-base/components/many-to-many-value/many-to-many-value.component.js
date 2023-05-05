import ManyToManyValueTemplate from './many-to-many-value.component.html';

const ManyToManyValueControllerInject = ['LanguageFactory'];
const ManyToManyValueController = function(LanguageFactory) {
    const self = this;

    self.treeOptions = {
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

    function getAssignIndex(assign) {
        if(typeof self.assigned !== "undefined") {
            for(let i = 0; i < self.assigned.length; i++){
                if(self.assigned[i][self.idField] == assign[self.idField]) {
                    return i;
                }
            }
        }
        return -1;
    }

    function assignExists(assign) {
        return getAssignIndex(assign) > -1;
    }

    function assignToggle(assign) {
        let index = getAssignIndex(assign);
        if (index > -1) {
            self.assigned.splice(index, 1);
        }
        else {
            self.assigned.push(assign);
        }
        self.onToggle({assigned: self.assigned});
    }

    self.assignExists = assignExists;
    self.assignToggle = assignToggle;

    self.$onInit = function () {
        self.available = angular.copy(self.availableInput);
        self.assigned = angular.copy(self.assignedInput);
        self.nameField = angular.copy(self.nameFieldInput);
        self.nameFieldTranslate = angular.copy(self.nameFieldTranslateInput);
        self.idField = angular.copy(self.idFieldInput);
        self.orderField = angular.copy(self.orderFieldInput);
        self.languageFactory = LanguageFactory;
    };

    self.$onChanges = function (changes) {
        if (changes.availableInput) {
            self.available = angular.copy(self.availableInput);
        }

        if (changes.assignedInput) {
            self.assigned = angular.copy(self.assignedInput);
        }

        if (changes.nameFieldInput) {
            self.nameField = angular.copy(self.nameFieldInput);
        }

        if (changes.nameFieldTranslateInput) {
            self.nameFieldTranslate = angular.copy(self.nameFieldTranslateInput);
        }

        if (changes.idFieldInput) {
            self.idField = angular.copy(self.idFieldInput);
        }

        if (changes.orderFieldInput) {
            self.orderField = angular.copy(self.orderFieldInput);
        }

        if (changes.renderAvailableAsTreeInput) {
            self.renderAvailableAsTree = angular.copy(self.renderAvailableAsTreeInput);
        }
    };
};

ManyToManyValueController.$inject = ManyToManyValueControllerInject;

const ManyToManyValueComponent = {
    bindings: {
        availableInput: '<available',
        assignedInput: '<assigned',
        nameFieldInput: '@nameField',
        nameFieldTranslateInput: '<nameFieldTranslate',
        idFieldInput: '@idField',
        orderFieldInput: '@orderField',
        renderAvailableAsTreeInput: '<renderAvailableAsTree',
        onToggle: '&'
    },
    template: ManyToManyValueTemplate,
    controller: ManyToManyValueController
};

export default ['aptoManyToManyValue', ManyToManyValueComponent];