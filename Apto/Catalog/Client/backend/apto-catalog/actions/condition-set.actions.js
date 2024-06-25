const ConditionSetActionsInject = ['MessageBusFactory'];
const ConditionSetActions = function (MessageBusFactory) {
    function getType(type) {
        return 'APTO_CONDITION_SET_' + type;
    }

    function fetchDetail(conditionSetId) {
        return {
            type: getType('FETCH_DETAIL'),
            payload: MessageBusFactory.query('FindConditionSet', [conditionSetId])
        }
    }

    function fetchSections(productId) {
        return {
            type: getType('FETCH_SECTIONS'),
            payload: MessageBusFactory.query('FindProductSectionsElements', [productId])
        }
    }

    function fetchConditions(productId) {
        return {
            type: getType('FETCH_CONDITIONS'),
            payload: MessageBusFactory.query('FindConditionSetConditions', [productId])
        }
    }

    function fetchCondition(conditionSetId, conditionId) {
        return {
            type: getType('FETCH_CONDITION'),
            payload: MessageBusFactory.query('FindConditionSetCondition', [conditionSetId, conditionId])
        }
    }

    function reset() {
        return {
            type: getType('RESET')
        }
    }

    return {
        fetchDetail: fetchDetail,
        fetchSections: fetchSections,
        fetchConditions: fetchConditions,
        fetchCondition: fetchCondition,
        reset: reset
    }
};

ConditionSetActions.$inject = ConditionSetActionsInject;

export default ['ConditionSetActions', ConditionSetActions];
