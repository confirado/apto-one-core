const RuleActionsInject = ['MessageBusFactory'];
const RuleActions = function (MessageBusFactory) {
    function getType(type) {
        return 'APTO_RULE_' + type;
    }

    function fetchDetail(ruleId) {
        return {
            type: getType('FETCH_DETAIL'),
            payload: MessageBusFactory.query('FindRule', [ruleId])
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
            payload: MessageBusFactory.query('FindRuleConditions', [productId])
        }
    }

    function fetchCondition(ruleId, conditionId) {
        return {
            type: getType('FETCH_CONDITION'),
            payload: MessageBusFactory.query('FindRuleCondition', [ruleId, conditionId])
        }
    }

    function fetchImplications(productId) {
        return {
            type: getType('FETCH_IMPLICATIONS'),
            payload: MessageBusFactory.query('FindRuleImplications', [productId])
        }
    }

    function fetchImplication(ruleId, implicationId) {
        return {
            type: getType('FETCH_IMPLICATION'),
            payload: MessageBusFactory.query('FindRuleImplication', [ruleId, implicationId])
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
        fetchImplications: fetchImplications,
        fetchImplication: fetchImplication,
        reset: reset
    }
};

RuleActions.$inject = RuleActionsInject;

export default ['RuleActions', RuleActions];
