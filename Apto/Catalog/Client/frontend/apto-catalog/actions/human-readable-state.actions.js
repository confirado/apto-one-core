const HumanReadableStateActionsInject = ['MessageBusFactory'];
const HumanReadableStateActions = function (MessageBusFactory) {
    const TYPE_NS = 'APTO_HUMAN_READABLE_STATE_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchCurrentHumanReadableState(productId, compressedState) {
        return {
            type: getType('FETCH_CURRENT_HUMAN_READABLE_STATE'),
            payload: MessageBusFactory.query('FindHumanReadableState', [productId, compressedState])
        }
    }

    return {
        fetchCurrentHumanReadableState: fetchCurrentHumanReadableState
    };
};

HumanReadableStateActions.$inject = HumanReadableStateActionsInject;

export default ['HumanReadableStateActions', HumanReadableStateActions];