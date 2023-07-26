const RuleUsageDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'AptoPartsListPartActions', 'partId', 'ruleUsageId'];
const RuleUsageDetailController = function($scope, $mdDialog, $ngRedux, AptoPartsListPartActions, partId, ruleUsageId) {

    $scope.mapStateToThis = function(state) {
        return {
            ruleUsageDetails: state.aptoPartsListPart.ruleUsageDetails,
            products: state.aptoPartsListPart.productsSectionsElements,
            operatorsActive: state.aptoPartsListPart.operatorsActive,
            operatorsEqual: state.aptoPartsListPart.operatorsEqual,
            operatorsFull: state.aptoPartsListPart.operatorsFull,
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchRuleUsageDetails: AptoPartsListPartActions.fetchRuleUsageDetails,
        updateRuleUsage: AptoPartsListPartActions.updateRuleUsage,
        resetRuleUsageDetails: AptoPartsListPartActions.resetRuleUsageDetails,
        fetchRuleUsages: AptoPartsListPartActions.fetchRuleUsages,
        fetchProductsSectionsElements: AptoPartsListPartActions.fetchProductsSectionsElements,
        addRuleUsageCondition: AptoPartsListPartActions.addRuleUsageCondition,
        removeRuleUsageCondition: AptoPartsListPartActions.removeRuleUsageCondition,
        updateRuleUsageCondition: AptoPartsListPartActions.updateRuleUsageCondition

    })($scope);

    function init() {
        $scope.fetchRuleUsageDetails(ruleUsageId);
        $scope.fetchProductsSectionsElements();
        initNewCondition();

    }

    function save(close) {
        $scope.updateRuleUsage(partId, ruleUsageId, $scope.ruleUsageDetails.quantity, $scope.ruleUsageDetails.active, $scope.ruleUsageDetails.name, $scope.ruleUsageDetails.conditionsOperator).then(()=> {
            if (close) {
                $scope.close();
            }
        });
    }

    function initNewCondition() {
        $scope.newCondition = {};
        resetProduct();
    }

    function onChangeProduct() {
        $scope.newCondition.productId = $scope.currentProduct.id;
        resetSection();
        resetComputedValue();
        resetElement();
        resetProperty();
        resetOperator();
        resetValue();
    }

    function onChangeSection() {
        $scope.newCondition.sectionId = $scope.currentSection.id;
        resetElement();
        resetProperty();
        resetOperator();
        resetValue();
        resetComputedValue();
    }

    function onChangeComputedValue() {
        $scope.newCondition.computedValueId = $scope.currentComputedValue.id;
        resetSection();
        resetElement();
        resetProperty();
        resetOperator();
        resetValue();
        if ($scope.newCondition.computedValueId !== null) {
            $scope.availableOperators = $scope.operatorsEqual;
        }
    }

    function onChangeElement() {
        $scope.newCondition.elementId = $scope.currentElement.id;
        resetProperty();
        resetOperator();
        resetValue();
        checkForProperty();
    }

    function onChangeProperty() {
        $scope.newCondition.property = $scope.currentProperty;
        if ($scope.currentProperty !== null) {
            $scope.availableOperators = $scope.operatorsEqual;
        } else {
            $scope.availableOperators = $scope.operatorsActive;
        }
    }

    function onChangeOperator() {
        $scope.newCondition.operator = $scope.currentOperator.id;
        if ($scope.currentOperator.id < 2) {
            $scope.currentValue = '';
            $scope.newCondition.value = '';
        }
    }

    function resetProduct() {
        $scope.currentId = null;
        $scope.currentProduct = null;
        $scope.newCondition.productId = null;
        resetSection();
        resetComputedValue();
        resetElement();
        resetProperty();
        resetOperator();
        resetValue();
    }
    
    function getProduct(productId) {
        return $scope.products.find(x => x.id === productId);
    }
    
    function getSection(sectionId, product) {
        return product.sections.find(x => x.id === sectionId);
    }

    function getComputedValue(computedValueId, computedValues) {
        return computedValues.find(x => x.id === computedValueId)
    }
    
    function getElement(elementId, section) {
        return section.elements.find(x => x.id === elementId);
    }
    

    function isValidCondition() {
        return (
            (null !== $scope.currentProduct && null !== $scope.currentOperator)
            || (null !== $scope.currentProduct && null !== $scope.currentSection && null !== $scope.currentOperator)
            || (null !== $scope.currentProduct && null !== $scope.currentSection && null !== $scope.currentElement && null !== $scope.currentOperator)
        )
    }

    function addCondition() {
        $scope.newCondition.value = $scope.currentValue;
        if (isValidCondition()) {
            $scope.addRuleUsageCondition(partId, ruleUsageId, $scope.newCondition.productId, $scope.newCondition.operator, $scope.newCondition.value, $scope.newCondition.sectionId, $scope.newCondition.elementId, $scope.newCondition.property, $scope.newCondition.computedValueId).then(()=>{
                $scope.fetchRuleUsageDetails(ruleUsageId);
                resetProduct();
            });
        }
    }

    function saveCondition() {
        $scope.newCondition.value = $scope.currentValue;
        if (isValidCondition()) {
            $scope.updateRuleUsageCondition($scope.currentId, partId, ruleUsageId, $scope.newCondition.productId, $scope.newCondition.operator, $scope.newCondition.value, $scope.newCondition.sectionId, $scope.newCondition.elementId, $scope.newCondition.property, $scope.newCondition.computedValueId).then(()=>{
                $scope.fetchRuleUsageDetails(ruleUsageId);
                resetProduct();
            });
        }
    }

    function editCondition(event, index) {
        const condition = $scope.ruleUsageDetails.conditions[index];
        const product = getProduct(condition.productId);
        const section = getSection(condition.sectionId, product);
        const element = getElement(condition.elementId, section, product);
        $scope.currentProduct = product;
        onChangeProduct();
        $scope.currentSection = section;
        onChangeSection();
        $scope.currentElement = element;
        onChangeElement();
        $scope.currentProperty = condition.property;
        onChangeProperty();
        $scope.currentOperator = getOperator(condition.operator);
        onChangeOperator();
        $scope.currentValue = condition.value;
        $scope.currentId = condition.id;
    }

    function removeCondition(conditionId) {
        $scope.removeRuleUsageCondition(partId, ruleUsageId, conditionId).then(()=>{
            $scope.fetchRuleUsageDetails(ruleUsageId);
            resetProduct();
        });
    }

    function resetSection() {
        $scope.currentSection = null;
        $scope.newCondition.sectionId = null;
        $scope.availableProperties = null;
        $scope.availableOperators = $scope.operatorsActive;
    }

    function resetComputedValue() {
        $scope.currentComputedValue = null;
        $scope.newCondition.computedValueId = null;
        $scope.availableOperators = $scope.operatorsActive;
    }

    function resetElement() {
        $scope.currentElement = null;
        $scope.newCondition.elementId = null;
    }

    function resetProperty() {
        $scope.currentProperty = null;
        $scope.newCondition.property = null;
    }
    function resetOperator() {
        $scope.currentOperator = null;
        $scope.newCondition.operator = null;
    }
    function resetValue() {
        $scope.currentValue = '';
        $scope.newCondition.value = '';
    }

    function checkForProperty() {
        if ($scope.currentElement.definition.properties) {
            $scope.availableProperties = Object.keys($scope.currentElement.definition.properties)
        }
        else {
            $scope.availableProperties = null;
        }
    }

    function getOperator(operatorId) {
        for (let i = 0; i < $scope.operatorsFull.length; i++) {
            if ($scope.operatorsFull[i].id == operatorId) {
                return $scope.operatorsFull[i];
            }
        }
    }

    function getOperatorValue(operatorId) {
        return getOperator(operatorId).name;
    }

    function close() {
        $scope.fetchRuleUsages(partId);
        $scope.resetRuleUsageDetails();
        $mdDialog.cancel();
    }

    function getProductIdentifier(productId) {
        let product = getProduct(productId);
        if (typeof product === 'undefined') {
            return '';
        }
        return product.identifier;
    }

    function getSectionIdentifier(sectionId, productId) {
        let product = getProduct(productId);
        if (typeof product === 'undefined') {
            return '';
        }
        let section = getSection(sectionId, product);
        if (typeof section === 'undefined') {
            return '';
        }
       return section.identifier;
    }
    function getElementIdentifier(elementId, sectionId, productId) {
        let product = getProduct(productId);
        if (typeof product === 'undefined') {
            return '';
        }
        let section = getSection(sectionId, product);
        if (typeof section === 'undefined') {
            return '';
        }
        let element = getElement(elementId, section, product);
        if (typeof element === 'undefined') {
            return '';
        }
       return element.identifier;
    }

    function getPropertyName(condition) {
        const product = getProduct(condition.productId);
        if (!product) {
            return '';
        }
        if (condition.computedValueId) {

            const computedValue = getComputedValue(condition.computedValueId, product.computedProductValues);
            return computedValue.name;
        }
        return condition.property;
    }

    init();

    $scope.onChangeProduct = onChangeProduct;
    $scope.onChangeSection = onChangeSection;
    $scope.onChangeComputedValue = onChangeComputedValue;
    $scope.onChangeElement = onChangeElement;
    $scope.onChangeProperty = onChangeProperty;
    $scope.onChangeOperator = onChangeOperator;
    $scope.getProductIdentifier = getProductIdentifier;
    $scope.getSectionIdentifier = getSectionIdentifier;
    $scope.getElementIdentifier = getElementIdentifier;
    $scope.getPropertyName = getPropertyName;
    $scope.getOperatorValue = getOperatorValue;
    $scope.addCondition = addCondition;
    $scope.editCondition = editCondition;
    $scope.removeCondition = removeCondition;
    $scope.isValidCondition = isValidCondition;
    $scope.saveCondition = saveCondition;
    $scope.save = save;
    $scope.close = close;
    $scope.$on('$destroy', subscribedActions);
};

RuleUsageDetailController.$inject = RuleUsageDetailControllerInject;

export default RuleUsageDetailController;