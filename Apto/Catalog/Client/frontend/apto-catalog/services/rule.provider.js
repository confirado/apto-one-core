import RuleCompiler from './rule-compiler';

const RuleServiceInject = [];
const RuleService = function() {
    const self = this;
    const compiledRules = {};

    /**
     * Return value of given section/element/property path or undefined
     * @param configurationState
     * @param sectionId
     * @param elementId
     * @param property
     * @returns {*}
     */
    function stateGetValue(configurationState, sectionId, elementId, property) {

        // section not given, return
        if (typeof sectionId === 'undefined' || sectionId === null) {
            return undefined;
        }

        // section not existing, return
        if (typeof configurationState[sectionId] === 'undefined') {
            return undefined;
        }

        // if query for section, return section.active or undefined
        if (typeof elementId === 'undefined' || elementId === null) {
            if (configurationState[sectionId]['state']['active']) {
                return true;
            }
            return undefined;
        }

        // element not existing, return
        if (typeof configurationState[sectionId]['elements'][elementId] === 'undefined') {
            return undefined;
        }

        // if query for element without property, return section.element.active or undefined
        if (typeof property === 'undefined' || property === null) {
            if (configurationState[sectionId]['elements'][elementId]['state']['active']) {
                return true;
            }
            return undefined;
        }

        // property not existing, return
        if (typeof configurationState[sectionId]['elements'][elementId]['state']['values'][property] === 'undefined') {
            return undefined;
        }

        // if all existing, return value or undefined
        if (configurationState[sectionId]['elements'][elementId]['state']['values'][property] !== null) {
            return configurationState[sectionId]['elements'][elementId]['state']['values'][property];
        }

        return undefined;
    }

    self.getRuleState = function (rule, configurationStateConditions, configurationStateImplications) {
        if (typeof compiledRules[rule.id] === 'undefined') {
            self.compileRule(rule);
        }
        return compiledRules[rule.id](configurationStateConditions, configurationStateImplications, stateGetValue);
    };

    self.compileRule = function (rule) {
        compiledRules[rule.id] = RuleCompiler(rule);
    };

    self.$getInject = [];
    self.$get = function() {};
    self.$get.$inject = self.$getInject;
};

RuleService.$inject = RuleServiceInject;

export default ['RuleService', RuleService];