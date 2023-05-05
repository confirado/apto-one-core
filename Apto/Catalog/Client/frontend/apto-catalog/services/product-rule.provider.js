const ProviderInject = [];
const Provider = function() {
    const provider = this;
    const OPERATOR_AND = 0;
    const OPERATOR_OR = 1;
    const NOT_ACTIVE = 0;
    const ACTIVE = 1;
    const LOWER = 2;
    const LOWER_OR_EQUAL = 3;
    const EQUAL = 4;
    const NOT_EQUAL = 5;
    const GREATER_OR_EQUAL = 6;
    const GREATER = 7;

    function isRuleFulfilledByState(state, rule) {
        // if conditions are not fulfilled, rule is not applied and automatically fulfilled
        if (!areCriterionsFulfilledByState(state, rule.conditions)) {
            return true;
        }

        // if implications are not fulfilled, rule is not fulfilled
        if (!areCriterionsFulfilledByState(state, rule.implications)) {
            return false;
        }

        // conditions and implications are fulfilled, whole rule is fulfilled
        return true;
    }

    function areCriterionsFulfilledByState(state, criterions, criterionOperator) {
        // no criterions, criterions are fulfilled
        if (criterions.length === 0) {
            return true;
        }

        if (parseInt(criterionOperator) === OPERATOR_AND) {
            // all criterions must be fulfilled
            for (let i = 0; i < criterions.length; i++) {
                if (!isCriterionFulfilledByState(state, criterions[i])) {
                    return false;
                }
            }
            return true;

        }

        if (parseInt(criterionOperator) === OPERATOR_OR) {
            // one criterions must be fulfilled
            for (let i = 0; i < criterions.length; i++) {
                if (isCriterionFulfilledByState(state, criterions[i])) {
                    return true;
                }
            }
            return false;
        }

        throw {
            message: 'Invalid criterion operator!',
            criterionOperator: criterionOperator
        }
    }

    function isCriterionFulfilledByState(state, criterion) {
        let value;

        if (null !== criterion.property) {
            value = getElementPropertyValue(state, criterion.sectionId, criterion.elementId, criterion.property);
        } else if (null !== criterion.elementId) {
            value = isElementSelected(state, criterion.sectionId, criterion.elementId);
        } else {
            value = isSectionSelected(state, criterion.sectionId);
        }

        return compareValues(value, criterion.value, criterion.operator);
    }

    function getElementPropertyValue(state, sectionId, elementId, property) {
        if (
            typeof state[sectionId].elements[elementId].state.values[property] !== "undefined" &&
            isElementSelected(state, sectionId, elementId)
        ) {
            return state[sectionId].elements[elementId].state.values[property];
        }

        return null;
    }

    function isElementSelected(state, sectionId, elementId) {
        return state[sectionId]['elements'][elementId]['state']['active'];
    }

    function isSectionSelected(state, sectionId) {
        return state[sectionId]['state']['active'];
    }

    function compareValues(stateValue, criterionValue, operator) {
        switch(parseInt(operator)) {

            case NOT_ACTIVE: {
                return false === stateValue;
            }

            case ACTIVE: {
                return true === stateValue;
            }

            case LOWER: {
                return stateValue < criterionValue;
            }

            case LOWER_OR_EQUAL: {
                return stateValue <= criterionValue;
            }

            case EQUAL: {
                return stateValue == criterionValue;
            }

            case NOT_EQUAL: {
                return stateValue != criterionValue;
            }

            case GREATER_OR_EQUAL: {
                return stateValue >= criterionValue;
            }

            case GREATER: {
                return stateValue > criterionValue;
            }

            default: {
                return false;
            }
        }
    }

    // public functions
    provider.isRuleFulfilledByState = isRuleFulfilledByState;
    provider.areCriterionsFulfilledByState = areCriterionsFulfilledByState;

    // service definition
    const ServiceInject = ['$ngRedux'];
    provider.$get = function($ngRedux) {
        // init redux
        const redux = {};

        // connect redux props
        function connectProps(state) {
            return {
                configurationState: state.configuration.present.configurationState
            }
        }

        // connect redux
        $ngRedux.connect(connectProps, {})(redux);

        // public functions
        function isRuleFulfilledByState(rule) {
            return provider.isRuleFulfilledByState(redux.configurationState, rule);
        }

        function areCriterionsFulfilledByState(criterions, criterionOperator) {
            return provider.areCriterionsFulfilledByState(redux.configurationState, criterions, criterionOperator);
        }

        return {
            isRuleFulfilledByState: isRuleFulfilledByState,
            areCriterionsFulfilledByState: areCriterionsFulfilledByState
        }
    };
    provider.$get.$inject = ServiceInject;
};

Provider.$inject = ProviderInject;

export default ['ProductRuleService', Provider];