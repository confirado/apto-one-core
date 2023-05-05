import Template from './customer-group-name.component.html';

const CustomerGroupNameControllerInject = [];

class CustomerGroupNameController {
    constructor() {
        this.customerGroupName = '';
    }

    $onInit() {
        this.updateCustomerGroupName();
    }

    $onChanges = function (changes) {
        if (
            changes.customerGroupId ||
            changes.availableCustomerGroups
        ) {
            this.updateCustomerGroupName();
        }
    }

    updateCustomerGroupName() {
        const customerGroup = this.availableCustomerGroups.find(x => x.id === this.customerGroupId);
        this.customerGroupName = '';
        if (customerGroup) {
            this.customerGroupName = customerGroup['name'];
        }
    }
}

CustomerGroupNameController.$inject = CustomerGroupNameControllerInject;

const CustomerGroupNameComponent = {
    bindings: {
        customerGroupId: '<',
        availableCustomerGroups: '<'
    },
    template: Template,
    controller: CustomerGroupNameController
};

export default ['aptoCustomerGroupName', CustomerGroupNameComponent];