import DefaultElementDefinitionTemplate from './default-element-definition.component.html';

const DefaultElementDefinitionControllerInject = [];
class DefaultElementDefinitionController {
    constructor () {
    }

    $onChanges = function (changes) {
    }
}

DefaultElementDefinitionController.$inject = DefaultElementDefinitionControllerInject;

const DefaultElementDefinitionComponent = {
    bindings: {
        definitionValues: '='
    },
    template: DefaultElementDefinitionTemplate,
    controller: DefaultElementDefinitionController
};

export default ['aptoDefaultElementDefinition', DefaultElementDefinitionComponent];