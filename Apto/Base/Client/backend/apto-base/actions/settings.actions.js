const ActionsInject = ['MessageBusFactory'];
const Actions = function(MessageBusFactory) {
    const TYPE_NS = 'APTO_SETTINGS_';

    function getType(type) {
        return TYPE_NS + type;
    }

    // function reset(colors, favicon) {
    function reset() {
        return {
            type: getType('RESET'),
            // payload: {
            //     colors: colors,
            //     favicon: favicon
            // }
        }
    }

    return {
        reset: reset
    };
};

Actions.$inject = ActionsInject;

export default ['SettingsActions', Actions];
