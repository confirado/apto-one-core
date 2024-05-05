import { Promise } from 'es6-promise';
import ConditionSetTab from './condition-set-tab.html';
import ConditionTab from './condition-tab.html';

const ConditionSetControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'ProductActions', 'ConditionSetActions', 'targetEvent', 'productId', 'conditionSetId'];
const ConditionSetController = function($scope, $templateCache, $mdDialog, $ngRedux, ProductActions, ConditionSetActions, targetEvent, productId, conditionSetId) {
    $templateCache.put('catalog/pages/product/condition-set/condition-set-tab.html', ConditionSetTab);
    $templateCache.put('catalog/pages/product/condition-set/condition-tab.html', ConditionTab);

    $scope.mapStateToThis = function(state) {
        return {
            detail: state.conditionSet.detail,
            operatorsActive: state.conditionSet.operatorsActive,
            operatorsEqual: state.conditionSet.operatorsEqual,
            operatorsFull: state.conditionSet.operatorsFull,
            sections: state.conditionSet.sections,
            conditions: state.conditionSet.conditions,
            computedValues: state.product.computedValues,
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchDetail: ConditionSetActions.fetchDetail,
        fetchSections: ConditionSetActions.fetchSections,
        fetchConditions: ConditionSetActions.fetchConditions,
        resetStore: ConditionSetActions.reset,
        updateProductConditionSet: ProductActions.updateProductConditionSet,
        fetchConditionSets: ProductActions.fetchConditionSets,

        addProductConditionSetCondition: ProductActions.addProductConditionSetCondition,
        updateProductConditionSetCondition: ProductActions.updateProductConditionSetCondition,
        copyProductConditionSetCondition: ProductActions.copyProductConditionSetCondition,
        removeProductConditionSetCondition: ProductActions.removeProductConditionSetCondition,
    })($scope);

    function init() {
        $scope.currentConditionId = null;

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

        $scope.fetchDetail(conditionSetId);
        $scope.fetchSections(productId).then(() => {
            $scope.fetchConditions(conditionSetId);
        });

        $scope.selectableConditionProperties = null;
        $scope.selectableConditionOperators = $scope.operatorsActive;
        $scope.selectedConditionSection = null;
        $scope.selectedConditionElement = null;
        $scope.selectedConditionProperty = null;
        $scope.selectedConditionOperator = null;
        $scope.selectedConditionValue = '';
        $scope.selectedConditionComputedValue = null;
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

    function addCondition() {
        const conditions = getValidConditions();

        if (false !== conditions) {
            let calledCommands = [];

            for (let i = 0; i < conditions.length; i++) {
                calledCommands.push($scope.addProductConditionSetCondition(productId, conditions[i]));
            }

            Promise.all(calledCommands).then((values) => {
                $scope.fetchConditions(conditionSetId);
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

        $scope.updateProductConditionSetCondition(productId, conditionSetId, condition).then(() => {
            $scope.fetchConditions(conditionSetId);
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
        $scope.copyProductConditionSetCondition(productId, conditionSetId, conditionId).then(() => {
            $scope.fetchConditions(conditionSetId);
        })
    }

    function removeCondition(conditionId) {
        $scope.removeProductConditionSetCondition(productId, conditionSetId, conditionId).then(() => {
            $scope.fetchConditions(conditionSetId);
        })
    }

    function getValidConditions() {
        if (isValidCondition()) {
            // init conditions array
            let conditions = [];

            // if computedValue
            if ($scope.conditionCriterionType.id === 1) {
                return [{
                    conditionSetId: conditionSetId,
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
                        conditionSetId: conditionSetId,
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

            // if no element is selected add single section conditionSet
            if(null === $scope.selectedConditionElement) {
                return [{
                    conditionSetId: conditionSetId,
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
                        conditionSetId: conditionSetId,
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

            // if no property is selected add single section->element conditionSet
            if(null === $scope.selectedConditionProperty) {
                return [{
                    conditionSetId: conditionSetId,
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
                        conditionSetId: conditionSetId,
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
                conditionSetId: conditionSetId,
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

    function save(close) {
        $scope.updateProductConditionSet(
            productId,
            $scope.detail.id,
            $scope.detail.identifier,
            $scope.detail.conditionsOperator,
        ).then(() => {
            $scope.fetchDetail(conditionSetId);
            $scope.fetchConditionSets(productId);
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
    $scope.resetConditionForm = resetConditionForm;

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
}

ConditionSetController.$inject = ConditionSetControllerInject;
export default ConditionSetController;
