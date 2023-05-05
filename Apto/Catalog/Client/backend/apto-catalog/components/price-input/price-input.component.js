import Template from './price-input.component.html';

const ControllerInject = ['$ngRedux', '$scope', 'LanguageFactory'];

class Controller {
    constructor($ngRedux, $scope, LanguageFactory) {
        this.translate = LanguageFactory.translate;
        this.scrope = $scope;
        this.activateListener = false
    }

    $onInit() {
        this.displayAmount = this.amount;
    }

    $onChanges(changes) {
    }

    $doCheck() {
        if (this.activateListener) {
            if (this.amount === '') {
                this.displayAmount = this.amount;
                this.activateListener = false;
            }
        }
    }


    amountChange($event) {
        let value = $event.originalEvent.key, keyCode = $event.originalEvent.keyCode,
            selectionStart = $event.target.selectionStart;
        // allow arrow right & left, delete & Strg + A
        if (keyCode !== 37 && keyCode !== 39 && keyCode !== 46 && keyCode !== 8 && keyCode !== 65) {
            $event.preventDefault();
        }

        // mark text and replace it...
        let element = $event.currentTarget,
            selectedTextBefore = element.value.substring(0, element.selectionStart),
            selectedTextBehind = element.value.substring(element.selectionEnd, element.value.length),
            amount = selectedTextBefore + selectedTextBehind;

        // set price
        if ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105)) {
            let begin = amount.substring(0, selectionStart);
            let end = amount.substring(selectionStart, this.displayAmount.length);

            amount = begin + value + end;
            if (amount.includes(',')) {
                begin = amount.split(',')[0];
                end = amount.split(',')[1].substring(0, 2);
                amount = begin + ',' + end;
            }
            this.displayAmount = amount;
        }

        // set comma
        if (keyCode === 188 || keyCode === 110) {
            if (!amount.includes(',')) {
                let begin = amount.substring(0, selectionStart);
                let end = amount.substring(selectionStart, this.displayAmount.length);
                end = end.substring(0, 2);
                this.displayAmount = begin + ',' + end;
            }
        }
    }

    amountChangeFinal() {
        let amount = this.displayAmount;
        if (!amount.includes(',')) {
            amount = amount + ',00';
        } else {
            let begin = amount.split(',')[0];
            let end = amount.split(',')[1].substring(0, 2).padEnd(2, '0');
            amount = begin + ',' + end;
        }

        if (amount.substring(0, 1) === ',') {
            amount = '0' + amount;
        }

        this.displayAmount = amount;
        amount = amount.replace(',', '');
        this.amount = parseInt(amount);
        this.activateListener = true;
    }

}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        amount: '='
    },
    template: Template,
    controller: Controller
};

export default ['aptoPriceInput', Component];