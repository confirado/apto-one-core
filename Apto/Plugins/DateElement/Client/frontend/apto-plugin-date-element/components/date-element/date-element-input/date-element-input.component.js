import Template from './date-element-input.component.html';
import DialogTemplate from './date-element-input.dialog.html';

const ControllerInject = ['$location', '$ngRedux', 'ngDialog', 'ConfigurationService', 'LanguageFactory', 'ElementValuesService', 'PersistedPropertiesFactory', 'SnippetFactory', 'UserInputParser'];
class Controller {
    static getStateDate(state, sectionId, elementId, getPersistedProperty) {
        return {
            date: getPersistedProperty(
                sectionId,
                elementId,
                'date',
                state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['date']
            ),
            year: getPersistedProperty(
                sectionId,
                elementId,
                'year',
                state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['year']
            ),
            month: getPersistedProperty(
                sectionId,
                elementId,
                'month',
                state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['month']
            ),
            weekDay: getPersistedProperty(
                sectionId,
                elementId,
                'weekDay',
                state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['weekDay']
            ),
            day: getPersistedProperty(
                sectionId,
                elementId,
                'day',
                state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['day']
            ),
            dayDiff: getPersistedProperty(
                sectionId,
                elementId,
                'dayDiff',
                state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['dayDiff']
            ),
            hour: getPersistedProperty(
                sectionId,
                elementId,
                'hour',
                state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['hour']
            ),
            minute: getPersistedProperty(
                sectionId,
                elementId,
                'minute',
                state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['minute']
            ),
            duration: getPersistedProperty(
                sectionId,
                elementId,
                'duration',
                state.configuration.present.configurationState[sectionId]['elements'][elementId]['state']['values']['duration']
            )
        };
    }

    constructor ($location, $ngRedux, ngDialog, ConfigurationService, LanguageFactory, ElementValuesService, PersistedPropertiesFactory, SnippetFactory, UserInputParser) {
        this.location = $location;
        this.ngRedux = $ngRedux;
        this.ngDialog = ngDialog;
        this.configurationService = ConfigurationService;
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        this.elementValuesService = ElementValuesService;
        this.snippetFactory = SnippetFactory;
        this.elementIsSelected = this.configurationService.elementIsSelected;
        this.elementIsDisabled = this.configurationService.elementIsDisabled;
        this.getPersistedProperty = PersistedPropertiesFactory.getPersistedProperty;
        this.convertFloat = UserInputParser.convertFloat;

        this.reduxProps = {};
        this.reduxActions = {};
    }

    mapStateProps(getStateDate, sectionId, elementId, getPersistedProperty) {
        return (state) => {
            let mapping = {
                ... getStateDate(state, sectionId, elementId, getPersistedProperty),
                useStepByStep: state.product.productDetail.useStepByStep
            };

            if (state.livePrice) {
                mapping.livePricePrices = state.livePrice.prices
            }

            return mapping;
        }
    }

    reduxConnect() {
        return this.ngRedux.connect(
            this.mapStateProps(
                Controller.getStateDate,
                this.section.id,
                this.element.id,
                this.getPersistedProperty
            ), {
            }
        )((selectedState, actions) => {
            this.reduxProps = selectedState;
            this.reduxActions = actions;
        });
    }

    onDateChange() {
        if (!this.input.date) {
            return;
        }

        if (this.staticValues.dateStep > 1 && this.staticValues.rendering === 'date-time') {
            this.input.date = this.calculateDateTime(this.getDateWithoutTime(this.input.date), this.input.hour, this.input.minute);
        }

        this.input.year = this.input.date.getFullYear();
        this.input.month = this.input.date.getMonth() + 1;
        this.input.weekDay = this.input.date.getDay() === 0 ? 7 : this.input.date.getDay(); // getDay() returns 0 for sunday, 1-6 for Monday to Saturday, we want 7 for Sunday
        this.input.day = this.input.date.getDate();
        this.input.dayDiff = this.timeDifference(this.getDateWithoutTime(new Date()), this.getDateWithoutTime(this.input.date))['days'];
        this.input.hour = this.input.date.getHours();
        this.input.minute = this.input.date.getMinutes();
    }

    getDateWithoutTime(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    calculateDateTime(date, hour, minute) {
        return new Date(date.getTime() + 60*1000*(60*Number(hour) + Number(minute)));
    }

    dateIsBlocked() {
        if (this.input.date) {
            for (let lockedDate of this.staticValues.lockedDates) {
                let fromLockedDate = new Date(lockedDate.fromDate);
                let toLockedDate = new Date(lockedDate.toDate);

                let hour = Number(this.input.hour);
                let minute = Number(this.input.minute);
                let fromDate = this.calculateDateTime(this.getDateWithoutTime(this.input.date), hour, minute);
                let toDate = fromDate;

                if (this.staticValues.showDurationInput && this.input.duration) {
                    if (this.staticValues.unit === 'hours') {
                        hour = Number(this.convertFloat(this.input.duration));
                        minute = 0;
                    } else if (this.staticValues.unit === 'minutes') {
                        hour = 0;
                        minute = Number(this.convertFloat(this.input.duration));
                    }
                    toDate = this.calculateDateTime(fromDate, hour, minute);
                }

                if ((fromDate >= fromLockedDate && fromDate <= toLockedDate) || (toDate >= fromLockedDate && toDate <= toLockedDate)) {
                    return true;
                }
            }
        }
        return false;
    }

    setValues() {
        if (this.dateIsBlocked()) {
            this.openErrorDialog();
        } else {
            let duration = this.staticValues.showDurationInput ? {duration: this.convertFloat(this.input.duration)} : {};
            let hour = this.staticValues.rendering === 'date-time' ? {hour: this.input.hour} : {};
            let minute = this.staticValues.rendering === 'date-time' ? {minute: this.input.minute} : {};

            // Apto/Catalog/Client/frontend/apto-catalog/services/configuration.provider.js
            this.configurationService.setElementProperties(
                this.section.id,
                this.element.id,
                {
                    aptoElementDefinitionId: this.staticValues.aptoElementDefinitionId,
                    date: this.input.date,
                    year: this.input.year,
                    month: this.input.month,
                    weekDay: this.input.weekDay,
                    day: this.input.day,
                    dayDiff: this.input.dayDiff,
                    ...hour,
                    ...minute,
                    ...duration,
                },
                true,
                this.reduxProps.useStepByStep
            );
        }
    }

    openErrorDialog() {
        this.ngDialog.open({
            data: {
                title: 'Option konnte nicht hinzugefügt werden:',
                message: this.staticValues.lockedDatesErrorMessage,
                section: this.section,
                element: this.element,
                translate: this.translate
            },
            template: DialogTemplate,
            className: 'ngdialog-theme-default',
            plain: true
        });
    }

    removeValues() {
        // Apto/Catalog/Client/frontend/apto-catalog/services/configuration.provider.js
        this.configurationService.removeElement(this.section.id, this.element.id);
        this.input = angular.copy(this.reduxProps);
        this.setHoursMinutes();
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get(path, trustAsHtml);
    }

    $onInit() {
        this.element = this.elementInput;

        if (typeof this.sectionInput !== 'undefined') {
            this.section = this.sectionInput;
        } else {
            this.section = this.sectionCtrlInput;
        }

        this.reduxDisconnect = this.reduxConnect();
        this.input = angular.copy(this.reduxProps);
        this.staticValues = this.element.definition.staticValues;
        this.setHoursMinutes();
    }

    setHoursMinutes() {
        this.input.hours = [...Array(24).keys()];
        this.input.minutes = [];
        for (let i=0; i < 60; i+=this.staticValues.dateStep) {
            this.input.minutes.push(i);
        }
    }

    timeDifference(date1, date2) {
        let oneDay = 24 * 60 * 60;
        let oneHour = 60 * 60;
        let oneMinute = 60;
        let firstDate = date1.getTime();
        let secondDate = date2.getTime();
        let seconds = Math.round(Math.abs(secondDate - firstDate) / 1000);
        let difference = {
            "days": 0,
            "hours": 0,
            "minutes": 0,
            "seconds": 0,
        }
        let sign = Math.sign(secondDate - firstDate);

        while (seconds >= oneDay) {     // calculate all the days and subtract it from the total
            difference.days++;
            seconds -= oneDay;
        }
        while (seconds >= oneHour) {    // calculate all the remaining hours then subtract it from the total
            difference.hours++;
            seconds -= oneHour;
        }
        while (seconds >= oneMinute) {  // calculate all the remaining minutes then subtract it from the total
            difference.minutes++;
            seconds -= oneMinute;
        }
        difference.seconds = seconds;

        difference.days = difference.days * sign;
        difference.hours = difference.hours * sign;
        difference.minutes = difference.minutes * sign;
        difference.seconds = difference.seconds * sign;

        return difference;
    }

    $onDestroy() {
        this.reduxDisconnect();
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        elementInput: '<element',
        sectionInput: '<section',
        sectionCtrlInput: '<sectionCtrl'
    },
    template: Template,
    controller: Controller
};

export default ['aptoDateElementInput', Component];
