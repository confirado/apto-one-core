const ActionsInject = ['MessageBusFactory'];
const Actions = function(MessageBusFactory) {
    const TYPE_NS = 'APTO_SETTINGS_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchSettings() {
        return {
            type: getType('FETCH_SETTINGS'),
            payload: MessageBusFactory.query('FindSettings')
        };
    }

    function addSettings(settingsDetail) {
        return {
            type: getType('ADD_SETTINGS'),
            payload: MessageBusFactory.command('AddSettings', [
                settingsDetail.primaryColor,
                settingsDetail.secondaryColor,
                settingsDetail.backgroundColorHeader,
                settingsDetail.fontColorHeader,
                settingsDetail.backgroundColorFooter,
                settingsDetail.fontColorFooter
            ])
        };
    }

    function updateSettings(settingsDetail) {
        return {
            type: getType('UPDATE_SETTINGS'),
            payload: MessageBusFactory.command('UpdateSettings', [
                settingsDetail.id,
                settingsDetail.primaryColor,
                settingsDetail.secondaryColor,
                settingsDetail.backgroundColorHeader,
                settingsDetail.fontColorHeader,
                settingsDetail.backgroundColorFooter,
                settingsDetail.fontColorFooter
            ])
        };
    }

    function resetSettings() {
        return {
            type: getType('RESET_SETTINGS')
        }
    }

    return {
        fetchSettings: fetchSettings,
        addSettings: addSettings,
        updateSettings: updateSettings,
        resetSettings: resetSettings
    };
};

Actions.$inject = ActionsInject;

export default ['SettingsActions', Actions];
