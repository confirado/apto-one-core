import DataListTemplate from './data-list.component.html';
import ItemActionsTemplate from './item-actions.html';
import CardListTemplate from './card-list.html';
import DataTableListTemplate from './data-table-list.html';

const AptoDataListControllerInject = ['$templateCache', 'AclIsGrantedFactory', 'LanguageFactory'];
const AptoDataListController = function($templateCache, AclIsGrantedFactory, LanguageFactory) {
    const self = this;

    function getFieldContent(model, field) {
        let fieldContent = '',
            isTranslatedValue = typeof (self.config.listFields[field].translatedValue) !== "undefined",
            hasDisplayField = typeof self.config.listFields[field]['displayField'] !== "undefined";

        if (model[field] instanceof Array && hasDisplayField) {
            let displayField = self.config.listFields[field]['displayField'],
                rawContent = '';
            for (let i = 0; i < model[field].length; i++) {
                rawContent = model[field][i][displayField];
                fieldContent += isTranslatedValue ? self.languageFactory.translate(rawContent) : rawContent;
                if (i < model[field].length - 1) {
                    fieldContent += ', ';
                }
            }
        } else if (model[field] instanceof Object && hasDisplayField) {
            let displayField = self.config.listFields[field]['displayField'],
                rawContent = model[field][displayField];
            fieldContent = isTranslatedValue ? self.languageFactory.translate(rawContent) : rawContent;
        } else if (isTranslatedValue) {
            fieldContent = self.languageFactory.translate(model[field]);
        } else {
            fieldContent = model[field];
        }

        return fieldContent;
    }

    self.getFieldContent = getFieldContent;

    self.$onInit = function () {
        $templateCache.put('item-actions.html', ItemActionsTemplate);
        $templateCache.put('components/data-list/card-list.html', CardListTemplate);
        $templateCache.put('components/data-list/data-table-list.html', DataTableListTemplate);

        self.config = angular.copy(self.configInput);
        self.data = angular.copy(self.dataInput);
        self.actions = angular.copy(self.actionsInput);
        self.additionalData = angular.copy(self.additionalDataInput);
        self.additionalActions = angular.copy(self.additionalActionsInput);
        self.aclIsGranted = AclIsGrantedFactory;
        self.languageFactory = LanguageFactory;
    };

    self.$onChanges = function (changes) {
        if (changes.configInput) {
            self.config = angular.copy(self.configInput);
        }

        if (changes.dataInput) {
            self.data = angular.copy(self.dataInput);
        }

        if (changes.actionsInput) {
            self.actions = angular.copy(self.actionsInput);
        }

        if (changes.additionalDataInput) {
            self.additionalData = angular.copy(self.additionalDataInput);
        }

        if (changes.additionalActionsInput) {
            self.additionalActions = angular.copy(self.additionalActionsInput);
        }
    };
};

AptoDataListController.$inject = AptoDataListControllerInject;

var AptoDataList = {
    bindings: {
        configInput: '<config',
        dataInput: '<data',
        actionsInput: '<actions',
        additionalDataInput: '<additionalData',
        additionalActionsInput: '<additionalActions'
    },
    template: DataListTemplate,
    controller: AptoDataListController
};

export default ['aptoDataList', AptoDataList];
