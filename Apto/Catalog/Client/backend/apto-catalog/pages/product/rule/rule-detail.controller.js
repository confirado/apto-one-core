import { Promise } from 'es6-promise';

import RuleTab from './rule-tab.html';
import ConditionTab from './condition-tab.html';
import ImplicationTab from './implication-tab.html';

const RuleDetailControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'ProductActions', 'RuleActions', 'targetEvent', 'productId', 'ruleId'];
const RuleDetailController = function($scope, $templateCache, $mdDialog, $ngRedux, ProductActions, RuleActions, targetEvent, productId, ruleId) {
    $templateCache.put('catalog/pages/product/rule/rule-tab.html', RuleTab);
    $templateCache.put('catalog/pages/product/rule/condition-tab.html', ConditionTab);
    $templateCache.put('catalog/pages/product/rule/implication-tab.html', ImplicationTab);

    $scope.mapStateToThis = function(state) {
        return {
            detail: state.rule.detail,
            operatorsActive: state.rule.operatorsActive,
            operatorsEqual: state.rule.operatorsEqual,
            operatorsFull: state.rule.operatorsFull,
            sections: state.rule.sections,
            conditions: state.rule.conditions,
            implications: state.rule.implications,
            computedValues: state.product.computedValues
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchDetail: RuleActions.fetchDetail,
        fetchSections: RuleActions.fetchSections,
        fetchConditions: RuleActions.fetchConditions,
        fetchImplications: RuleActions.fetchImplications,
        resetStore: RuleActions.reset,
        updateProductRule: ProductActions.updateProductRule,
        fetchProductRules: ProductActions.fetchRules,
        addProductRuleCondition: ProductActions.addProductRuleCondition,
        addProductRuleImplication: ProductActions.addProductRuleImplication,

        updateProductRuleCondition: ProductActions.updateProductRuleCondition,
        copyProductRuleCondition: ProductActions.copyProductRuleCondition,
        removeProductRuleCondition: ProductActions.removeProductRuleCondition,

        updateProductRuleImplication: ProductActions.updateProductRuleImplication,
        copyProductRuleImplication: ProductActions.copyProductRuleImplication,
        removeProductRuleImplication: ProductActions.removeProductRuleImplication,
    })($scope);

    function init() {
        $scope.currentConditionId = null;
        $scope.currentImplicationId = null;

        $scope.criterionTypes = [
            {
                name: 'Standard',
                id: 0
            },
            {
                name: 'Berechneter Wert',
                id: 1
            }
        ];
        $scope.conditionCriterionType = {
            name: 'Standard',
            id: 0
        };
        $scope.implicationCriterionType = {
            name: 'Standard',
            id: 0
        };
        $scope.fetchDetail(ruleId);
        $scope.fetchSections(productId).then(() => {
            $scope.fetchConditions(ruleId);
            $scope.fetchImplications(ruleId);
        });

        $scope.selectableConditionProperties = null;
        $scope.selectableConditionOperators = $scope.operatorsActive;
        $scope.selectedConditionSection = null;
        $scope.selectedConditionElement = null;
        $scope.selectedConditionProperty = null;
        $scope.selectedConditionOperator = null;
        $scope.selectedConditionValue = '';
        $scope.selectedConditionComputedValue = null;

        $scope.selectableImplicationProperties = null;
        $scope.selectableImplicationOperators = $scope.operatorsActive;
        $scope.selectedImplicationSection = null;
        $scope.selectedImplicationElement = null;
        $scope.selectedImplicationProperty = null;
        $scope.selectedImplicationOperator = null;
        $scope.selectedImplicationValue = '';
        $scope.selectedImplicationComputedValue = null;
    }

    function onChangeSelectedConditionSection() {
        if ($scope.selectedConditionSection && $scope.selectedConditionSection.length === 0) {
            $scope.selectedConditionSection = null;
        }
        $scope.selectedConditionElement = null;
        $scope.selectedConditionProperty = null;
        $scope.selectedConditionOperator = null;
        $scope.selectableConditionProperties = null;
        $scope.selectableConditionOperators = $scope.operatorsActive;
    }

    function onChangeSelectedConditionElement() {

        if ($scope.selectedConditionElement && $scope.selectedConditionElement.length === 0) {
            $scope.selectedConditionElement = null;
        }
        $scope.selectedConditionProperty = null;
        $scope.selectedConditionOperator = null;
        if ($scope.selectedConditionElement && $scope.selectedConditionElement.length === 1) {
            $scope.selectableConditionProperties = getElementSelectableProperties($scope.selectedConditionElement[0].definition);
        }
        $scope.selectableConditionOperators = $scope.operatorsActive;
    }

    function onChangeSelectedConditionProperty() {
        if ($scope.selectedConditionProperty && $scope.selectedConditionProperty.length === 0) {
            $scope.selectedConditionProperty = null;
        }
        $scope.selectedConditionOperator = null;
        if ($scope.selectedConditionProperty !== null) {
            $scope.selectableConditionOperators = $scope.operatorsEqual;
        } else {
            $scope.selectableConditionOperators = $scope.operatorsActive;
        }
    }

    function onChangeSelectedConditionOperator() {
        if ($scope.selectedConditionOperator.id === 0 || $scope.selectedConditionOperator.id === 1) {
            $scope.selectedConditionValue = '';
        }
    }

    function onChangeSelectedImplicationSection() {
        if ($scope.selectedImplicationSection && $scope.selectedImplicationSection.length === 0) {
            $scope.selectedImplicationSection = null;
        }
        $scope.selectedImplicationElement = null;
        $scope.selectedImplicationProperty = null;
        $scope.selectedImplicationOperator = null;
        $scope.selectableImplicationProperties = null;
        $scope.selectableImplicationOperators = $scope.operatorsActive;
    }

    function onChangeSelectedImplicationElement() {
        if ($scope.selectedImplicationElement && $scope.selectedImplicationElement.length === 0) {
            $scope.selectedImplicationElement = null;
        }
        $scope.selectedImplicationProperty = null;
        $scope.selectedImplicationOperator = null;
        if ($scope.selectedImplicationElement && $scope.selectedImplicationElement.length === 1) {
            $scope.selectableImplicationProperties = getElementSelectableProperties($scope.selectedImplicationElement[0].definition);
        }
        $scope.selectableImplicationOperators = $scope.operatorsActive;
    }

    function onChangeSelectedImplicationProperty() {
        if ($scope.selectedImplicationProperty && $scope.selectedImplicationProperty.length === 0) {
            $scope.selectedImplicationProperty = null;
        }
        $scope.selectedImplicationOperator = null;
        if ($scope.selectedImplicationProperty !== null) {
            $scope.selectableImplicationOperators = $scope.operatorsEqual;
        } else {
            $scope.selectableImplicationOperators = $scope.operatorsActive;
        }
    }

    function onChangeSelectedImplicationOperator() {
        if ($scope.selectedImplicationOperator.id === 0 || $scope.selectedImplicationOperator.id === 1) {
            $scope.selectedImplicationValue = '';
        }
    }

    function addCondition() {
        const conditions = getValidConditions();
        if (false !== conditions) {
            let calledCommands = [];

            for (let i = 0; i < conditions.length; i++) {
                calledCommands.push($scope.addProductRuleCondition(productId, conditions[i]));
            }

            Promise.all(calledCommands).then((values) => {
                $scope.fetchConditions(ruleId);
            });

            resetSelectedCondition();
        }
        // @todo show error message
        return false;
    }

    function saveCondition() {
        const condition = {
            computedProductValueId: $scope.selectedConditionComputedValue ? $scope.selectedConditionComputedValue.id : null,
            elementId: $scope.selectedConditionElement ? $scope.selectedConditionElement[0].id : null,
            id: $scope.currentConditionId,
            operatorId: $scope.selectedConditionOperator ? $scope.selectedConditionOperator.id : null,
            property: $scope.selectedConditionProperty ? $scope.selectedConditionProperty[0] : null,
            sectionId: $scope.selectedConditionSection ? $scope.selectedConditionSection[0].id : null,
            typeId: $scope.conditionCriterionType.id,
            value: $scope.selectedConditionValue,
        }

        $scope.updateProductRuleCondition(productId, ruleId, condition).then(() => {
            $scope.fetchConditions(ruleId);
            resetSelectedCondition();
            $scope.currentConditionId = null;
        });
    }

    function updateCondition(conditionId) {

        $scope.currentConditionId = conditionId;

        $scope.fetchSections(productId).then(() => {
            $scope.conditions.forEach(condition => {
                if (condition.id === conditionId) {
                    // sections
                    if (condition.type === 0) {
                        // Type
                        $scope.conditionCriterionType = {
                            name: 'Standard',
                            id: 0
                        }
                        $scope.onChangeConditionCriterionType();

                        // section
                        $scope.selectedConditionSection = [getSection(condition.sectionId)];
                        onChangeSelectedConditionSection();

                        // element
                        const element = getElement(condition.sectionId, condition.elementId);
                        if (element) {
                            $scope.selectedConditionElement = [element];
                            onChangeSelectedConditionElement();
                        }
                    // berechnete werte
                    } else {
                        $scope.conditionCriterionType = {
                            name: 'Berechneter Wert',
                            id: 1
                        }
                        $scope.onChangeConditionCriterionType();

                        $scope.selectedConditionComputedValue = condition.computedProductValue[0];
                    }

                    // field
                    const property = condition.property;
                    if (property) {
                        $scope.selectedConditionProperty = [property];
                        onChangeSelectedConditionProperty();
                    }

                    // operator
                    $scope.selectedConditionOperator = getOperator(condition.operator);
                    onChangeSelectedConditionOperator();

                    // value
                    $scope.selectedConditionValue = condition.value;
                }
            });
        });
    }

    function copyCondition(conditionId) {
        $scope.copyProductRuleCondition(productId, ruleId, conditionId).then(() => {
            $scope.fetchConditions(ruleId);
        })
    }

    function removeCondition(conditionId) {
        $scope.removeProductRuleCondition(productId, ruleId, conditionId).then(() => {
            $scope.fetchConditions(ruleId);
        })
    }

    function getValidConditions() {
        if (isValidCondition()) {
            // init conditions array
            let conditions = [];

            // if computedValue
            if ($scope.conditionCriterionType.id === 1) {
                return [{
                    ruleId: ruleId,
                    type: $scope.conditionCriterionType.id,
                    operator: $scope.selectedConditionOperator.id,
                    computedValue: $scope.selectedConditionComputedValue.id,
                    value: $scope.selectedConditionValue
                }]
            }

            // if more then one section is selected only section conditions will be returned
            if ($scope.selectedConditionSection.length > 1) {
                for (let i = 0; i < $scope.selectedConditionSection.length; i++) {
                    const section = $scope.selectedConditionSection[i];

                    conditions.push({
                        ruleId: ruleId,
                        sectionId: section.id,
                        elementId: null,
                        property: null,
                        operator: $scope.selectedConditionOperator.id,
                        value: ''
                    });
                }

                return conditions;
            }

            // check if one section is selected
            if (!$scope.selectedConditionSection[0]) {
                return false;
            }

            // if only one section is selected this section is for all selected elements
            const section = $scope.selectedConditionSection[0];

            // if no element is selected add single section rule
            if(null === $scope.selectedConditionElement) {
                return [{
                    ruleId: ruleId,
                    sectionId: section.id,
                    elementId: null,
                    property: null,
                    operator: $scope.selectedConditionOperator.id,
                    value: $scope.selectedConditionValue
                }];
            }

            // if more then one element is selected only section->element conditions will be returned
            if ($scope.selectedConditionElement.length > 1) {
                for (let i = 0; i < $scope.selectedConditionElement.length; i++) {
                    const element = $scope.selectedConditionElement[i];

                    conditions.push({
                        ruleId: ruleId,
                        sectionId: section.id,
                        elementId: element.id,
                        property: null,
                        operator: $scope.selectedConditionOperator.id,
                        value: ''
                    });
                }

                return conditions;
            }

            // check if one element is selected
            if (!$scope.selectedConditionElement[0]) {
                return false;
            }

            // if only one element is selected this element is for all selected properties
            const element = $scope.selectedConditionElement[0];

            // if no property is selected add single section->element rule
            if(null === $scope.selectedConditionProperty) {
                return [{
                    ruleId: ruleId,
                    sectionId: section.id,
                    elementId: element.id,
                    property: null,
                    operator: $scope.selectedConditionOperator.id,
                    value: $scope.selectedConditionValue
                }];
            }

            // if more then one property is selected only section->element->property conditions will be returned
            if ($scope.selectedConditionProperty.length > 1) {
                for (let i = 0; i < $scope.selectedConditionProperty.length; i++) {
                    const property = $scope.selectedConditionProperty[i];

                    conditions.push({
                        ruleId: ruleId,
                        sectionId: section.id,
                        elementId: element.id,
                        property: property,
                        operator: $scope.selectedConditionOperator.id,
                        value: $scope.selectedConditionValue
                    });
                }

                return conditions;
            }

            // check if one property is selected
            if (!$scope.selectedConditionProperty[0]) {
                return false;
            }

            // add one single section->element->property condition
            const property = $scope.selectedConditionProperty[0];
            return [{
                ruleId: ruleId,
                sectionId: section.id,
                elementId: element.id,
                property: property,
                operator: $scope.selectedConditionOperator.id,
                value: $scope.selectedConditionValue
            }];
        }

        // return false if no condition can be created
        return false;
    }

    function isValidCondition() {
        if ($scope.conditionCriterionType.id === 0) {
            if (
                (null === $scope.selectedConditionElement && null !== $scope.selectedConditionProperty)
                || null === $scope.selectedConditionSection
                || null === $scope.selectedConditionOperator
            ) {
                return false;
            }
        }
        if ($scope.conditionCriterionType.id === 1) {
            if (
                null === $scope.selectedConditionComputedValue
                || null === $scope.selectedConditionOperator
                || '' === $scope.selectedConditionValue
            ) {
                return false;
            }
        }
        return true;
    }

    function resetSelectedCondition() {
        $scope.selectableConditionProperties = null;
        $scope.selectableConditionOperators = $scope.operatorsActive;
        $scope.selectedConditionSection = null;
        $scope.selectedConditionElement = null;
        $scope.selectedConditionProperty = null;
        $scope.selectedConditionOperator = null;
        $scope.selectedConditionComputedValue = null;
        $scope.selectedConditionValue = '';
        $scope.conditionCriterionType = {
            name: 'Standard',
            id: 0
        };
    }

    function resetConditionForm() {
        resetSelectedCondition();
        $scope.currentConditionId = null;
    }

    function addImplication() {
        const implications = getValidImplications();
        if (false !== implications) {
            let calledCommands = [];

            for (let i = 0; i < implications.length; i++) {
                calledCommands.push($scope.addProductRuleImplication(productId, implications[i]));
            }

            Promise.all(calledCommands).then((values) => {
                $scope.fetchImplications(ruleId);
            });

            resetSelectedImplication();
        }
        // @todo show error message
        return false;
    }


    function saveImplication(implicationId) {
        const implication = {
            id: $scope.currentImplicationId,
            typeId: $scope.implicationCriterionType.id,
            computedProductValueId: $scope.selectedImplicationComputedValue ? $scope.selectedImplicationComputedValue.id : null,
            sectionId: $scope.selectedImplicationSection ? $scope.selectedImplicationSection[0].id : null,
            elementId: $scope.selectedImplicationElement ? $scope.selectedImplicationElement[0].id : null,
            operatorId: $scope.selectedImplicationOperator ? $scope.selectedImplicationOperator.id : null,
            property: $scope.selectedImplicationProperty ? $scope.selectedImplicationProperty[0] : null,
            value: $scope.selectedImplicationValue,
        }

        $scope.updateProductRuleImplication(productId, ruleId, implication).then(() => {
            $scope.fetchImplications(ruleId);
            resetSelectedImplication();
            $scope.currentImplicationId = null;
        })
    }

    function updateImplication(implicationId) {

        $scope.currentImplicationId = implicationId;

        $scope.fetchSections(productId).then(() => {
            $scope.implications.forEach(implication => {

                if (implication.id === implicationId) {
                    if (implication.type === 0) { // sections
                        // Type
                        $scope.implicationCriterionType = {
                            name: 'Standard',
                            id: 0
                        }
                        $scope.onChangeImplicationCriterionType();

                        // section
                        $scope.selectedImplicationSection = [getSection(implication.sectionId)];
                        onChangeSelectedImplicationSection();

                        // element
                        const element = getElement(implication.sectionId, implication.elementId);
                        if (element) {
                            $scope.selectedImplicationElement = [element];
                            onChangeSelectedImplicationElement();
                        }
                    } else { // berechnete werte

                        $scope.implicationCriterionType = {
                            name: 'Berechneter Wert',
                            id: 1
                        }
                        $scope.onChangeImplicationCriterionType();

                        $scope.selectedImplicationComputedValue = implication.computedProductValue[0];
                    }

                    // field
                    const property = implication.property;
                    if (property) {
                        $scope.selectedImplicationProperty = [property];
                        onChangeSelectedImplicationProperty();
                    }

                    // operator
                    $scope.selectedImplicationOperator = getOperator(implication.operator);
                    onChangeSelectedImplicationOperator();

                    // value
                    $scope.selectedImplicationValue = implication.value;
                }
            });
        });
    }

    function copyImplication(implicationId) {
        $scope.copyProductRuleImplication(productId, ruleId, implicationId).then(() => {
            $scope.fetchImplications(ruleId);
        });
    }

    function removeImplication(implicationId) {
        $scope.removeProductRuleImplication(productId, ruleId, implicationId).then(() => {
            $scope.fetchImplications(ruleId);
        })
    }

    function getValidImplications() {
        if(isValidImplication()) {
            // init implications array
            let implications = [];

            // if computedValue
            if ($scope.implicationCriterionType.id === 1) {
                return [{
                    ruleId: ruleId,
                    type: $scope.implicationCriterionType.id,
                    operator: $scope.selectedImplicationOperator.id,
                    computedValue: $scope.selectedImplicationComputedValue.id,
                    value: $scope.selectedImplicationValue
                }]
            }

            // if more than one section is selected only section implications will be returned
            if ($scope.selectedImplicationSection.length > 1) {
                for (let i = 0; i < $scope.selectedImplicationSection.length; i++) {
                    const section = $scope.selectedImplicationSection[i];

                    implications.push({
                        ruleId: ruleId,
                        sectionId: section.id,
                        elementId: null,
                        property: null,
                        operator: $scope.selectedImplicationOperator.id,
                        value: ''
                    });
                }

                return implications;
            }

            // check if one section is selected
            if (!$scope.selectedImplicationSection[0]) {
                return false;
            }

            // if only one section is selected this section is for all selected elements
            const section = $scope.selectedImplicationSection[0];

            // if no element is selected add single section rule
            if(null === $scope.selectedImplicationElement) {
                return [{
                    ruleId: ruleId,
                    sectionId: section.id,
                    elementId: null,
                    property: null,
                    operator: $scope.selectedImplicationOperator.id,
                    value: $scope.selectedImplicationValue
                }];
            }

            // if more than one element is selected only section->element implications will be returned
            if ($scope.selectedImplicationElement.length > 1) {
                for (let i = 0; i < $scope.selectedImplicationElement.length; i++) {
                    const element = $scope.selectedImplicationElement[i];

                    implications.push({
                        ruleId: ruleId,
                        sectionId: section.id,
                        elementId: element.id,
                        property: null,
                        operator: $scope.selectedImplicationOperator.id,
                        value: ''
                    });
                }

                return implications;
            }

            // check if one element is selected
            if (!$scope.selectedImplicationElement[0]) {
                return false;
            }

            // if only one element is selected this element is for all selected properties
            const element = $scope.selectedImplicationElement[0];

            // if no property is selected add single section->element rule
            if(null === $scope.selectedImplicationProperty) {
                return [{
                    ruleId: ruleId,
                    sectionId: section.id,
                    elementId: element.id,
                    property: null,
                    operator: $scope.selectedImplicationOperator.id,
                    value: $scope.selectedImplicationValue
                }];
            }

            // if more than one property is selected only section->element->property implications will be returned
            if ($scope.selectedImplicationProperty.length > 1) {
                for (let i = 0; i < $scope.selectedImplicationProperty.length; i++) {
                    const property = $scope.selectedImplicationProperty[i];

                    implications.push({
                        ruleId: ruleId,
                        sectionId: section.id,
                        elementId: element.id,
                        property: property,
                        operator: $scope.selectedImplicationOperator.id,
                        value: $scope.selectedImplicationValue
                    });
                }

                return implications;
            }

            // check if one property is selected
            if (!$scope.selectedImplicationProperty[0]) {
                return false;
            }

            // add one single section->element->property implication
            const property = $scope.selectedImplicationProperty[0];
            return [{
                ruleId: ruleId,
                sectionId: section.id,
                elementId: element.id,
                property: property,
                operator: $scope.selectedImplicationOperator.id,
                value: $scope.selectedImplicationValue
            }];
        }
        return false;
    }

    function isValidImplication() {
        if ($scope.implicationCriterionType.id === 0) {
            if (
                (null === $scope.selectedImplicationElement && null !== $scope.selectedImplicationProperty)
                || null === $scope.selectedImplicationSection
                || null === $scope.selectedImplicationOperator
            ) {
                return false;
            }
        }
        if ($scope.implicationCriterionType.id === 1) {
            if (
                null === $scope.selectedImplicationComputedValue
                || null === $scope.selectedImplicationOperator
                || '' === $scope.selectedImplicationValue
            ) {
                return false;
            }
        }
        return true;
    }

    function resetSelectedImplication() {
        $scope.selectableImplicationProperties = null;
        $scope.selectableImplicationOperators = $scope.operatorsActive;
        $scope.selectedImplicationSection = null;
        $scope.selectedImplicationElement = null;
        $scope.selectedImplicationProperty = null;
        $scope.selectedImplicationOperator = null;
        $scope.selectedImplicationComputedValue = null;
        $scope.selectedImplicationValue = '';
        $scope.implicationCriterionType = {
            name: 'Standard',
            id: 0
        };
    }

    function resetImplicationForm() {
        resetSelectedImplication();
        $scope.currentImplicationId = null;
    }

    function getSection(sectionId) {
        for (let i = 0; i < $scope.sections.length; i++) {
            if ($scope.sections[i].id === sectionId) {
                return angular.copy($scope.sections[i]);
            }
        }
    }

    function getElement(sectionId, elementId) {
        const section = getSection(sectionId);
        for (let i = 0; i < section.elements.length; i++) {
            if (section.elements[i].id === elementId) {
                return angular.copy(section.elements[i]);
            }
        }
    }

    function getOperator(operatorId) {
        for (let i = 0; i < $scope.operatorsFull.length; i++) {
            if ($scope.operatorsFull[i].id === operatorId) {
                return angular.copy($scope.operatorsFull[i]);
            }
        }
    }

    function getSectionIdentifier(sectionId) {
        if (null === sectionId) {
            return null;
        }
        const section = getSection(sectionId);

        return section ? section.identifier : '';
    }

    function getElementIdentifier(sectionId, elementId) {
        if (null === elementId) {
            return null;
        }
        const element = getElement(sectionId, elementId);

        return element ? element.identifier : '';
    }

    function getOperatorName(operatorId) {
        const operator = getOperator(operatorId);
        return operator.name;
    }

    function getElementSelectableProperties(definitionClass) {
        if (!definitionClass.properties) {
            return null;
        }
        return Object.keys(definitionClass.properties);
    }

    function onChangeConditionCriterionType() {
        if ($scope.conditionCriterionType.id === 1) {
            $scope.selectableConditionOperators = $scope.operatorsEqual;
        }
    }

    function onChangeImplicationCriterionType() {
        if ($scope.implicationCriterionType.id === 1) {
            $scope.selectableImplicationOperators = $scope.operatorsEqual;
        }
    }

    function save(close) {
        $scope.updateProductRule(
            productId,
            $scope.detail.id,
            $scope.detail.name,
            $scope.detail.active,
            $scope.detail.errorMessage,
            $scope.detail.conditionsOperator,
            $scope.detail.implicationsOperator,
            $scope.detail.softRule,
            $scope.detail.description
        ).then(() => {
            $scope.fetchDetail(ruleId);
            $scope.fetchProductRules(productId);
            if (close === true) {
                this.close();
            }
        });
    }

    init();

    $scope.onChangeSelectedConditionSection = onChangeSelectedConditionSection;
    $scope.onChangeSelectedConditionElement = onChangeSelectedConditionElement;
    $scope.onChangeSelectedConditionProperty = onChangeSelectedConditionProperty;
    $scope.onChangeSelectedConditionOperator = onChangeSelectedConditionOperator;
    $scope.onChangeConditionCriterionType = onChangeConditionCriterionType;
    $scope.addCondition = addCondition;

    $scope.copyCondition = copyCondition;
    $scope.updateCondition = updateCondition;
    $scope.removeCondition = removeCondition;
    $scope.saveCondition = saveCondition;

    $scope.isValidCondition = isValidCondition;
    $scope.onChangeSelectedImplicationSection = onChangeSelectedImplicationSection;
    $scope.onChangeSelectedImplicationElement = onChangeSelectedImplicationElement;
    $scope.onChangeSelectedImplicationProperty = onChangeSelectedImplicationProperty;
    $scope.onChangeSelectedImplicationOperator = onChangeSelectedImplicationOperator;
    $scope.onChangeImplicationCriterionType = onChangeImplicationCriterionType;
    $scope.addImplication = addImplication;
    $scope.saveImplication = saveImplication;
    $scope.resetImplicationForm = resetImplicationForm;
    $scope.resetConditionForm = resetConditionForm;

    $scope.removeImplication = removeImplication;
    $scope.copyImplication = copyImplication;
    $scope.updateImplication = updateImplication;

    $scope.isValidImplication = isValidImplication;

    $scope.getSectionIdentifier = getSectionIdentifier;
    $scope.getElementIdentifier = getElementIdentifier;
    $scope.getOperatorName = getOperatorName;
    $scope.save = save;

    $scope.close = function () {
        $mdDialog.cancel().then(function () {
            $scope.resetStore();
        });
    };

    $scope.$on('$destroy', subscribedActions);
};

RuleDetailController.$inject = RuleDetailControllerInject;

export default RuleDetailController;
