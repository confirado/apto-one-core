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
                settingsDetail: state.settings.settingsDetail
            }
        }
    }

    connectActions() {
        // actions mapping object
        return {
            resetSettings: this.settingsActions.resetSettings,
            fetchSettings: this.settingsActions.fetchSettings,
            addSettings: this.settingsActions.addSettings,
            updateSettings: this.settingsActions.updateSettings
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
        this.actions.fetchSettings();
    }

    saveSettings() {
        if (null === this.state.settingsDetail.id) {
            this.actions.addSettings(this.state.settingsDetail).then(()=>{
                this.actions.fetchSettings();
            });
        } else {
            this.actions.updateSettings(this.state.settingsDetail).then(()=>{
                this.actions.fetchSettings();
            });
        }
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
    bindings: {},
    template: Template,
    controller: Controller,
};

export default ['aptoSettings', Component];
