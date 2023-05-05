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

    function fetchImplications(productId) {
        return {
            type: getType('FETCH_IMPLICATIONS'),
            payload: MessageBusFactory.query('FindRuleImplications', [productId])
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
        fetchImplications: fetchImplications,
        reset: reset
    }
};

RuleActions.$inject = RuleActionsInject;

export default ['RuleActions', RuleActions];