import Template from './settings.component.html';
import TemplateTab from './tabs/template.html';
const ControllerInject = ['$ngRedux', 'SettingsActions', '$templateCache'];

class Controller {
    constructor ($ngRedux, SettingsActions, $templateCache) {
        $templateCache.put('base/components/settings/tabs/template.html', TemplateTab);
        // set services
        this.ngRedux = $ngRedux;
        this.settingsActions = SettingsActions;

        // set redux properties
        this.state = {};
        this.actions = {};

        // event listener
        this.eventListeners = [];
    }

    connectProps() {
        return (state) => {
            // state mapping object
            return {

            }
        }
    }

    connectActions() {
        // actions mapping object
        return {
            resetSettings: this.settingsActions.reset
        }
    }

    onStateChange(state) {
        this.state = state;
    }

    connectRedux() {
        this.eventListeners.push(
            this.ngRedux.connect(
                this.connectProps(),
                this.connectActions()
            )((selectedState, actions) => {
                this.actions = actions;
                this.onStateChange(selectedState);
            })
        );
    }

    $onInit() {
        this.connectRedux();
        this.actions.resetSettings();
    }

    $onDestroy() {
        // destroy all listeners
        for (let i = 0; i < this.eventListeners.length; i++) {
            this.eventListeners[i]();
        }
    };
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
    },
    template: Template,
    controller: Controller,
};


export default ['aptoSettings', Component];
