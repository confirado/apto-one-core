import Template from './date-element.component.html';

const ControllerInject = ['$ngRedux', 'ElementActions'];
class Controller {
    constructor($ngRedux, ElementActions) {
        this.values = {
            rendering: 'date',
            dateStep: 1,
            showDurationInput: false,
            duration: [],
            lockedDates: [],
            lockedDatesErrorMessage: [],
            valuePrefix: [],
            valueSuffix: [],
            unit: 'hours',
        };

        this.hoursFrom = [...Array(24).keys()];
        this.minutesFrom = [...Array(60).keys()];

        this.hoursTo = [...Array(24).keys()];
        this.minutesTo = [...Array(60).keys()];

        this.units = [{
            id: 'hours',
            label: 'Stunden'
        }, {
            id: 'minutes',
            label: 'Minuten'
        }];

        this.renderings = [{
            id: 'date',
            label: 'Datum'
        }, {
            id: 'date-time',
            label: 'Datum und Uhrzeit'
        }];

        this.input = {
            unit: 'hours',
            dateStep: 1,
            lockedDates: {
                fromDate: null,
                toDate: null,
                hoursFrom: 0,
                minutesFrom: 0,
                hoursTo: 0,
                minutesTo: 0,
            },
            duration: {
                minimum: 0,
                maximum: 1,
                step: 1
            }
        };

        this.mapStateToThis = function(state) {
            return {
                detailDefinition: state.element.detail.definition,
                definitionValues: state.element.definition.values
            }
        };

        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues
        })(this);
    };

    $onInit() {
        if (this.detailDefinition.class == 'Apto\\Plugins\\DateElement\\Domain\\Core\\Model\\Product\\Element\\DateElementDefinition') {

            this.values.rendering = this.detailDefinition.json.rendering;
            if (typeof this.values.rendering === "undefined") {
                this.values.rendering = 'date';
            }

            this.values.dateStep = this.detailDefinition.json.dateStep;
            if (typeof this.values.dateStep === "undefined") {
                this.values.dateStep = 1;
            }

            if (this.detailDefinition.json.showDurationInput) {
                this.values.showDurationInput = this.detailDefinition.json.showDurationInput;
            }

            if (this.detailDefinition.json.duration) {
                const valueCollection = this.detailDefinition.json.duration.json.collection;

                for (let iValue = 0; iValue < valueCollection.length; iValue++) {
                    this.pushValue(valueCollection[iValue].json);
                }
            }

            if (this.detailDefinition.json.lockedDates) {
                const valueCollection = this.detailDefinition.json.lockedDates;

                for (let iValue = 0; iValue < valueCollection.length; iValue++) {
                    this.pushLockedDate(valueCollection[iValue]);
                }
            }

            if (this.detailDefinition.json.lockedDatesErrorMessage) {
                this.values.lockedDatesErrorMessage = this.detailDefinition.json.lockedDatesErrorMessage;
            }

            if (this.detailDefinition.json.valuePrefix) {
                this.values.valuePrefix = this.detailDefinition.json.valuePrefix;
            }

            if (this.detailDefinition.json.valueSuffix) {
                this.values.valueSuffix = this.detailDefinition.json.valueSuffix;
            }

            if (this.detailDefinition.json.unit) {
                this.values.unit = this.detailDefinition.json.unit;
            }

            this.setDefinitionValues(this.values);
        }

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    this.setDefinitionValues(this.values);
                    return true;
                }
            }
        });
    }

    addLockedDateFormValid() {
        // both dates are required
        if (!this.input.lockedDates.fromDate || !this.input.lockedDates.toDate) {
            return false;
        }

        // from date must be smaller than to date
        if (this.input.lockedDates.fromDate.getTime() > this.input.lockedDates.toDate.getTime()) {
            return false;
        }

        // hours must be within 0-23 range
        if ((this.input.hoursFrom > 23 && this.input.hoursFrom < 0) || (this.input.hoursTo > 23 && this.input.hoursTo < 0)) {
            return false;
        }

        // minutes must be within 0-59 range
        if ((this.input.minutesFrom > 59 && this.input.minutesFrom < 0) || (this.input.minutesTo > 59 && this.input.minutesTo < 0)) {
            return false;
        }

        return true;
    }

    addLockedDate() {
        const fromDate = new Date(this.input.lockedDates.fromDate.getTime() + 60*1000*(60*this.input.lockedDates.hoursFrom + this.input.lockedDates.minutesFrom));
        const toDate = new Date(this.input.lockedDates.toDate.getTime() + 60*1000*(60*this.input.lockedDates.hoursTo + this.input.lockedDates.minutesTo));

        this.pushLockedDate({
            fromDate: fromDate,
            toDate: toDate
        });

        this.setDefinitionValues(this.values);
    }

    pushLockedDate(value) {
        this.values.lockedDates.push(value);
    }

    removeLockedDate(index) {
        this.values.lockedDates.splice(index, 1);
        this.setDefinitionValues(this.values);
    }

    addValue() {
        this.pushValue({
            minimum: this.input.duration.minimum,
            maximum: this.input.duration.maximum,
            step: this.input.duration.step
        });
        this.setDefinitionValues(this.values);
    };

    pushValue(value) {
        this.values.duration.push(value);
    }

    removeValue(index) {
        this.values.duration.splice(index, 1);
        this.setDefinitionValues(this.values);
    };

    $onDestroy() {
        this.unSubscribeActions();
    };
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        definitionValidation: '&'
    },
    template: Template,
    controller: Controller
};

export default ['dateElement', Component];