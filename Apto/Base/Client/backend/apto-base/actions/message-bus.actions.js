const MessageBusActionsInject = [];
const MessageBusActions = function() {
    const TYPE_NS = 'APTO_MESSAGE_BUS_';
    const factory = {
        clearMessageLog: clearMessageLog,
        addMessageLogMessage: addMessageLogMessage,
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function clearMessageLog() {
        return {
            type: getType('CLEAR_MESSAGE_LOG')
        }
    }

    function addMessageLogMessage(payload) {
        return {
            type: getType('ADD_MESSAGE_LOG_MESSAGE'),
            payload: payload
        }
    }

    return factory;
};

MessageBusActions.$inject = MessageBusActionsInject;

export default ['MessageBusActions', MessageBusActions];