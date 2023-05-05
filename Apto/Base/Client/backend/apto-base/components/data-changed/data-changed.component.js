import DataChangedTemplate from './data-changed.component.html';

const DataChangedControllerInject = [];
class DataChangedController {
    constructor () {
        this.changed = 0;
    }

    $onChanges = function (changes) {
        if (changes.data) {
            this.changed++;
        }
    };
}

DataChangedController.$inject = DataChangedControllerInject;

const DataChangedComponent = {
    bindings: {
        data: '<',
        label: '@'
    },
    template: DataChangedTemplate,
    controller: DataChangedController
};

export default ['aptoDataChanged', DataChangedComponent];