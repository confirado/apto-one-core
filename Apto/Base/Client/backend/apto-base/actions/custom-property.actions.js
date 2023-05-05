const ActionsInject = ['MessageBusFactory'];
const Actions = function(MessageBusFactory) {
    const TYPE_NS = 'APTO_CUSTOM_PROPERTY_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchUsedCustomPropertyKeys() {
        return {
            type: getType('FETCH_USED_CUSTOM_PROPERTY_KEYS'),
            payload: MessageBusFactory.query('FindUsedCustomPropertyKeys', [])
        }
    }

    return {
        fetchUsedCustomPropertyKeys: fetchUsedCustomPropertyKeys
    };
};

Actions.$inject = ActionsInject;

export default ['CustomPropertyActions', Actions];