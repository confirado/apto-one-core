/**
 * Compile a rule to native javascript
 * @param rule
 */

// @todo rule compiler needs to know, whether section has multiple selection allowed
const AptoCatalogProductCompileRule = function(rule) {

    /**
     * Valid compare operator types
     * @type {{0: string, 1: string, 2: string, 3: string, 4: string, 5: string, 6: string, 7: string}}
     */
    const compareOperators = {
        0: '===',   // NOT_ACTIVE (=== undefined)
        1: '!==',   // ACTIVE (!== undefined)
        2: '<',     // LOWER
        3: '<=',    // LOWER_OR_EQUAL
        4: '==',    // EQUAL
        5: '!=',    // NOT_EQUAL
        6: '>=',    // GREATER_OR_EQUAL
        7: '>',      // GREATER
        8: 'contains',
        9: 'does not contain'
    };

    /**
     * Operators for which values must be casted as float
     * @type {{2: boolean, 3: boolean, 6: boolean, 7: boolean}}
     */
    const castFloatCompareOperators = {
        2: true,    // LOWER
        3: true,    // LOWER_OR_EQUAL
        6: true,    // GREATER_OR_EQUAL
        7: true     // GREATER
    };

    /**
     * Valid boolean operators
     * @type {{0: string, 1: string}}
     */
    const booleanOperators = {
        0: '&&',    // AND
        1: '||'     // OR
    };

    /**
     * Return a compiled compare operator with the necessary value
     * @param operator
     * @param value
     * @return string
     */
    function compileCompareOperator(operator, value) {
        if (typeof compareOperators[operator] === 'undefined') {
            throw 'The compare operator ' + operator + ' is invalid.';
        }
        if (operator <= 1) {
            value = undefined;
        } else if (operator in castFloatCompareOperators) {
            value = parseFloat(value);
        } else if (operator === "8" || operator === "9") {
            return ".includes('" + value + "')"
        } else {
            value = "'" + value + "'";
        }
        return compareOperators[operator] + ' ' + value
    }

    /**
     * Return a compiled boolean operator
     * @param operator
     * @return string
     */
    function compileBooleanOperator(operator) {
        if (typeof booleanOperators[operator] === 'undefined') {
            throw 'The boolean operator ' + operator + ' is invalid.';
        }
        return booleanOperators[operator];
    }

    /**
     * Compile a value for sectionId/elementId/property
     * @param value
     * @returns string|null
     */
    function compileValue(value) {
        if (null === value) {
            return null;
        } else {
            return "'" + value + "'";
        }
    }

    /**
     * Return compiled criteria
     * @param criteria
     * @param booleanOperator
     * @param state
     * @returns string
     */
    function compileCriteria(criteria, booleanOperator, stateVar) {
        let i, l, criterion, compiled = '',
            compiledBooleanOperator = compileBooleanOperator(booleanOperator);
        for (i = 0, l = criteria.length; i < l; i++) {
            criterion = criteria[i];
            if (criterion.property !== null && criterion.operator <= 1) {
                throw 'The operator ' + criterion.operator + ' is invalid for properties other than null.';
            }
            if ('' !== compiled) {
                compiled += ' ' + compiledBooleanOperator + ' ';
            }
            if (criterion.operator === "8") {
                let value = 'get(' + stateVar + ', ' +
                    compileValue(criterion.sectionId) + ', ' +
                    compileValue(criterion.elementId) + ', ' +
                    compileValue(criterion.property) +
                    ')';
                compiled +=
                    'typeof(' + value + ') !== "undefined" && '
                    + value +
                    compileCompareOperator(criterion.operator, criterion.value);
            }
            else if (criterion.operator === "9") {
                let value = 'get(' + stateVar + ', ' +
                    compileValue(criterion.sectionId) + ', ' +
                    compileValue(criterion.elementId) + ', ' +
                    compileValue(criterion.property) +
                    ')';
                compiled +=
                    'typeof(' + value + ') !== "undefined" && !('
                    + value +
                    compileCompareOperator(criterion.operator, criterion.value)
                    + ')';
            }
            else {
                compiled +=
                    'get(' + stateVar + ', ' +
                    compileValue(criterion.sectionId) + ', ' +
                    compileValue(criterion.elementId) + ', ' +
                    compileValue(criterion.property) +
                    ')' +
                    compileCompareOperator(criterion.operator, criterion.value);
            }
        }
        return compiled !== '' ? compiled : 'true'; // empty criteria default to true
    }

    return new Function(
        'stateCond', 'stateImpl', 'get',
        '/* rule ' + rule.id + ' */' + "\n" +
        'if (' + compileCriteria(rule.conditions, rule.conditionsOperator, 'stateCond') + ') {' +  "\n" +
        'return ' + compileCriteria(rule.implications, rule.implicationsOperator, 'stateImpl') + ';' + "\n" +
        '}' + "\n" +
        'return null;'
    );
};

export default AptoCatalogProductCompileRule;