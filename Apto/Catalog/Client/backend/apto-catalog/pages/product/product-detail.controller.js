import ProductTab from './product-tab.html';
import PriceTab from './price-tab.html';
import ShopTab from './shop-tab.html';
import CategoryTab from './category-tab.html';
import SectionTab from './section-tab.html';
import RuleTab from './rule-tab.html';
import ComputedValuesTab from './computed-values-tab.html';
import DiscountTab from './discount-tab.html';
import CustomPropertiesTab from './custom-properties-tab.html';
import FilterPropertyTab from './filter-properties-tab.html';
import ConditionTab from './condition-tab.html'

import SectionDetailTemplate from './section/section-detail.controller.html';
import SectionDetailController from './section/section-detail.controller';

import RuleDetailTemplate from './rule/rule-detail.controller.html';
import RuleDetailController from './rule/rule-detail.controller';

import ComputedValueDetailTemplate from './computedValue/computed-value-detail.controller.html';
import ComputedValueDetailController from './computedValue/computed-value-detail.controller';

const ProductDetailControllerInject = ['$scope', '$document', '$mdDialog', '$mdEditDialog', '$ngRedux', '$templateCache', 'ProductActions', 'targetEvent', 'showDetailsDialog', 'searchString', 'pageNumber', 'recordsPerPage', 'LanguageFactory', 'productId'];
const ProductDetailController = function($scope, $document, $mdDialog, $mdEditDialog, $ngRedux, $templateCache, ProductActions, targetEvent, showDetailsDialog, searchString, pageNumber, recordsPerPage, LanguageFactory, productId) {
    $templateCache.put('catalog/pages/product/product-tab.html', ProductTab);
    $templateCache.put('catalog/pages/product/price-tab.html', PriceTab);
    $templateCache.put('catalog/pages/product/shop-tab.html', ShopTab);
    $templateCache.put('catalog/pages/product/category-tab.html', CategoryTab);
    $templateCache.put('catalog/pages/product/section-tab.html', SectionTab);
    $templateCache.put('catalog/pages/product/rule-tab.html', RuleTab);
    $templateCache.put('catalog/pages/product/computed-values-tab.html', ComputedValuesTab);
    $templateCache.put('catalog/pages/product/discount-tab.html', DiscountTab);
    $templateCache.put('catalog/pages/product/custom-properties-tab.html', CustomPropertiesTab);
    $templateCache.put('catalog/pages/product/filter-properties-tab.html', FilterPropertyTab);
    $templateCache.put('catalog/pages/product/condition-tab.html', ConditionTab);

    const subscribedActions = $ngRedux.connect(mapState, {
        productsFetch: ProductActions.productsFetch,
        productDetailFetch: ProductActions.productDetailFetch,
        productDetailSave: ProductActions.productDetailSave,
        productDetailReset: ProductActions.productDetailReset,
        productDetailAssignShops: ProductActions.productDetailAssignShops,
        productDetailAssignCategories: ProductActions.productDetailAssignCategories,
        addProductSection: ProductActions.addProductSection,
        copyProductSection: ProductActions.copyProductSection,
        setProductSectionIsActive: ProductActions.setProductSectionIsActive,
        setProductSectionAllowMulti: ProductActions.setProductSectionAllowMulti,
        setProductSectionIsMandatory: ProductActions.setProductSectionIsMandatory,
        removeProductSection: ProductActions.removeProductSection,
        addProductCustomProperty: ProductActions.addProductCustomProperty,
        removeProductCustomProperty: ProductActions.removeProductCustomProperty,
        fetchSections: ProductActions.fetchSections,
        fetchSectionsElements: ProductActions.fetchSectionsElements,
        fetchRules: ProductActions.fetchRules,
        fetchPrices: ProductActions.fetchPrices,
        fetchDiscounts: ProductActions.fetchDiscounts,
        fetchCustomProperties: ProductActions.fetchCustomProperties,
        fetchCategories: ProductActions.fetchCategories,
        addProductPrice: ProductActions.addProductPrice,
        removeProductPrice: ProductActions.removeProductPrice,
        addProductDiscount: ProductActions.addProductDiscount,
        removeProductDiscount: ProductActions.removeProductDiscount,
        addProductRule: ProductActions.addProductRule,
        updateProductRule: ProductActions.updateProductRule,
        removeProductRule: ProductActions.removeProductRule,
        copyProductRule: ProductActions.copyProductRule,
        setDetailValue: ProductActions.setDetailValue,
        availableShopsFetch: ProductActions.availableShopsFetch,
        getNextPosition: ProductActions.getNextPosition,
        productDetailAssignProperties: ProductActions.productDetailAssignProperties,
        addComputedProductValueAlias: ProductActions.addComputedProductValueAlias,
        addComputedProductValue: ProductActions.addComputedProductValue,
        fetchComputedProductValues: ProductActions.fetchComputedProductValues,
        removeComputedProductValue: ProductActions.removeComputedProductValue,
        fetchConditions: ProductActions.fetchConditions,
        addCondition: ProductActions.addCondition,
        updateCondition: ProductActions.updateCondition,
        copyCondition: ProductActions.copyCondition,
        removeCondition: ProductActions.removeCondition,
    })($scope);

    function mapState(state) {
        return {
            productDetail: state.product.productDetail,
            availableCategories: state.product.availableCategories,
            availableShops: state.product.availableShops,
            availableCustomerGroups: state.product.availableCustomerGroups,
            availablePriceCalculators: state.product.availablePriceCalculators,
            sections: state.product.sections,
            sectionsElements: state.product.sectionsElements,
            rules: state.product.rules,
            conditions: state.product.conditions,
            operatorsActive: state.rule.operatorsActive,
            operatorsEqual: state.rule.operatorsEqual,
            operatorsFull: state.rule.operatorsFull,
            computedValues: state.product.computedValues,
            prices: state.product.prices,
            discounts: state.product.discounts,
            customProperties: state.product.customProperties,
            nextPosition: state.product.nextPosition,
            filterProperties: state.filterProperty.filterProperties
        }
    }

    function init() {
        if(typeof productId !== "undefined") {
            $scope.availableShopsFetch();
            $scope.productDetailFetch(productId).then(() => {
                initDomainPropsMultiplierHints();
            });
            $scope.fetchSections(productId);
            $scope.fetchSectionsElements(productId);
            $scope.fetchRules(productId);
            $scope.fetchConditions(productId);
            $scope.fetchComputedProductValues(productId);
            $scope.fetchPrices(productId);
            $scope.fetchDiscounts(productId);
            $scope.fetchCustomProperties(productId);
        } else {
            $scope.availableShopsFetch().then(() => {
                assignDefaultShop();
            });

            $scope.getNextPosition().then(() => {
                $scope.productDetail.position = angular.copy($scope.nextPosition);
            });
        }

        $scope.conditionTypes = [
            {
                name: 'Standard',
                id: 0
            },
            {
                name: 'Berechneter Wert',
                id: 1
            }
        ];

        $scope.conditionType = {
            name: 'Standard',
            id: 0
        };

        $scope.selectedConditionIdentifier = null;
        $scope.selectableConditionProperties = null;
        $scope.selectableConditionOperators = $scope.operatorsActive;
        $scope.selectedConditionSection = null;
        $scope.selectedConditionElement = null;
        $scope.selectedConditionProperty = null;
        $scope.selectedConditionOperator = null;
        $scope.selectedConditionValue = '';
        $scope.selectedConditionComputedValue = null;
    }

    function assignDefaultShop() {
        if ($scope.productDetail.shops.length === 0 && $scope.availableShops.length > 0) {
            let autoAssignedShop = [$scope.availableShops[0]];
            onToggleShop(autoAssignedShop);
        }
    }

    function onToggleShop(shops) {
        $scope.productDetailAssignShops(shops);
    }

    function onToggleCategory(categories) {
        $scope.productDetailAssignCategories(categories);
    }

    $scope.onToggleFilterProperty = function (filterProperties) {
        $scope.productDetailAssignProperties(filterProperties);
    }

    function addSection() {
        $scope.addProductSection(productId, $scope.newSection).then(() => {
            $scope.newSection = {
                value: [],
                addDefaultElement: false,
            };
            $scope.fetchSections(productId);
        });
    }

    function editSection($event, sectionId) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            multiple: true,
            parent: parentEl,
            targetEvent: $event,
            template: SectionDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                productId: productId,
                sectionId: sectionId,
                sections: $scope.sections,
                editSection: editSection
            },
            controller: SectionDetailController
        });
    }

    function copySection(sectionId) {
        $scope.copyProductSection(productId, sectionId).then(() => {
            $scope.fetchSections(productId);
        });
    }

    function setSectionIsActive(sectionId, sectionIsActive) {
        $scope.setProductSectionIsActive(productId, sectionId, sectionIsActive).then(() => {
            $scope.fetchSections(productId);
        });
    }

    function setSectionIsMandatory(sectionId, sectionIsMandatory) {
        $scope.setProductSectionIsMandatory(productId, sectionId, sectionIsMandatory).then(() => {
            $scope.fetchSections(productId);
        });
    }

    function removeSection(sectionId) {
        $scope.removeProductSection(productId, sectionId).then(() => {
            $scope.fetchSections(productId);
        });
    }

    function addRule() {
        $scope.addProductRule(productId, $scope.newRuleName.value).then(() => {
            $scope.newRuleName = {value: ''};
            $scope.fetchRules(productId);
        });
    }

    function editRule($event, ruleId) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            multiple: true,
            parent: parentEl,
            targetEvent: $event,
            template: RuleDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                productId: productId,
                ruleId: ruleId
            },
            controller: RuleDetailController
        });
    }

    function copyRule($event, ruleId) {
        $scope.copyProductRule(productId, ruleId).then(() => {
            $scope.fetchRules(productId);
        });
    }

    function removeRule(ruleId) {
        $scope.removeProductRule(productId, ruleId).then(() => {
            $scope.fetchRules(productId);
        });
    }

    function addComputedValue() {
        $scope.addComputedProductValue(productId, $scope.newComputedValue.name).then(() => {
            $scope.newComputedValue = {name: ''};
            $scope.fetchComputedProductValues(productId);
        });
    }

    function editComputedValue($event, computedValueId) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            multiple: true,
            parent: parentEl,
            targetEvent: $event,
            template: ComputedValueDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                productId: productId,
                computedValueId: computedValueId
            },
            controller: ComputedValueDetailController
        });
    }

    function removeComputedValue(computedValueId) {
        $scope.removeComputedProductValue(productId, computedValueId).then(() => {
            $scope.fetchComputedProductValues(productId);
        });
    }

    function editRuleName($event, rule) {
        $event.stopPropagation();
        var editRuleNameDialog = {
            modelValue: rule.name,
            save: function (input) {
                $scope.updateProductRule(productId, rule.id, input.$modelValue, rule.active, rule.errorMessage, rule.conditionsOperator, rule.implicationsOperator, rule.softRule, rule.description, rule.position).then(() => {
                    $scope.fetchRules(productId);
                });
            },
            targetEvent: $event,
            title: 'Regelname:'
        };

        $mdEditDialog.large(editRuleNameDialog);
    }

    function setRuleActive(rule) {
        $scope.updateProductRule(productId, rule.id, rule.name, rule.active, rule.errorMessage, rule.conditionsOperator, rule.implicationsOperator, rule.softRule, rule.description, rule.position).then(() => {
            $scope.fetchRules(productId);
        });
    }

    function addPrice() {
        $scope.addProductPrice(productId, $scope.newPrice.amount, $scope.newPrice.currencyCode, $scope.newPrice.customerGroupId).then(() => {
            $scope.newPrice = {
                amount: '',
                currencyCode: 'EUR',
                customerGroupId: ''
            };
            $scope.fetchPrices(productId);
        });
    }

    function removePrice(priceId) {
        $scope.removeProductPrice(productId, priceId).then(() => {
            $scope.fetchPrices(productId);
        });
    }

    function addDiscount() {
        $scope.addProductDiscount(productId, $scope.newDiscount.discount, $scope.newDiscount.customerGroupId, $scope.newDiscount.name).then(() => {
            $scope.newDiscount = {
                discount: '',
                customerGroupId: '',
                name: null
            };
            $scope.fetchDiscounts(productId);
        });
    }

    function removeDiscount(discountId) {
        $scope.removeProductDiscount(productId, discountId).then(() => {
            $scope.fetchDiscounts(productId);
        });
    }

    function addCustomProperty(key, value, translatable) {
        $scope.addProductCustomProperty(productId, key, value, translatable).then(() => {
            $scope.fetchCustomProperties(productId);
        });
    }

    function removeCustomProperty(key) {
        $scope.removeProductCustomProperty(productId, key).then(() => {
            $scope.fetchCustomProperties(productId);
        });
    }

    function onSelectPreviewImage(path) {
        $scope.setDetailValue('previewImage', path);
    }

    function onSelectDomainPreviewImage(index, path) {
        let domainProperties = angular.copy($scope.productDetail.domainProperties);
        domainProperties[index].previewImage = path;
        $scope.setDetailValue('domainProperties', domainProperties);
    }

    function save(productForm, close) {
        if(productForm.$valid) {
            $scope.productDetailSave($scope.productDetail).then(() => {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if(typeof $scope.productDetail.id === "undefined") {
                    $scope.productDetailReset();
                    $scope.productsFetch(
                        pageNumber,
                        recordsPerPage,
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                } else {
                    $scope.productDetailFetch($scope.productDetail.id);
                }
            });
        }
    }

    function updateMultiplierHint($index) {
        $scope.domainPorpsMultiplierHint[$index] = '';

        let multiplier = parseFloat($scope.productDetail.domainProperties[$index].priceModifier);

        if (multiplier >= 100) {
            $scope.domainPorpsMultiplierHint[$index] = "Die Preise werden um " + Math.round((multiplier - 100) * 100) / 100 + " % erhöht.";
            return;
        }

        if (multiplier > 0 && multiplier < 100) {
            $scope.domainPorpsMultiplierHint[$index] = "Die Preise werden um " + Math.round(((multiplier - 100) * (-1)) * 100) / 100 + " % verringert";
            return;
        }

        if (multiplier === 0) {
            $scope.domainPorpsMultiplierHint[$index] = "Die Preise werden auf 0 gesetzt";
        }
        else {
            $scope.domainPorpsMultiplierHint[$index] = "Multiplikator entweder negativ oder falsches Format (Dezimalzahl größer 0 mit bis zu 2 Nachkommastellen)";
        }
    }

    function getMultiplierHint($index) {
        return $scope.domainPorpsMultiplierHint[$index];
    }

    function initDomainPropsMultiplierHints() {
        for (let i=0; i < $scope.productDetail.domainProperties.length; i++) {
            updateMultiplierHint(i);
        }
    }

    function onChangeConditionType() {
        if ($scope.conditionTypes.id === 1) {
            $scope.selectableConditionOperators = $scope.operatorsEqual;
        }
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

    function getElementSelectableProperties(definitionClass) {
        if (!definitionClass.properties) {
            return null;
        }
        return Object.keys(definitionClass.properties);
    }

    function addCondition() {
        const conditions = getValidConditions();
        if (false !== conditions) {
            let calledCommands = [];

            for (let i = 0; i < conditions.length; i++) {
                calledCommands.push($scope.addCondition(productId, conditions[i]));
            }

            Promise.all(calledCommands).then((values) => {
                $scope.fetchConditions(productId);
            });

            resetSelectedCondition();
        }
        // @todo show error message
        return false;
    }

    function saveCondition() {
        const condition = {
            identifier: $scope.selectedConditionIdentifier,
            id: $scope.currentConditionId,
            typeId: $scope.conditionType.id,
            computedProductValueId: $scope.selectedConditionComputedValue ? $scope.selectedConditionComputedValue.id : null,
            sectionId: $scope.selectedConditionSection ? $scope.selectedConditionSection[0].id : null,
            elementId: $scope.selectedConditionElement ? $scope.selectedConditionElement[0].id : null,
            property: $scope.selectedConditionProperty ? $scope.selectedConditionProperty[0] : null,
            operatorId: $scope.selectedConditionOperator ? $scope.selectedConditionOperator.id : null,
            value: $scope.selectedConditionValue,
        }

        $scope.updateCondition(productId, condition).then(() => {
            $scope.fetchConditions(productId);
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
                        $scope.conditionType = {
                            name: 'Standard',
                            id: 0
                        }
                        $scope.onChangeConditionType();

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
                        $scope.conditionType = {
                            name: 'Berechneter Wert',
                            id: 1
                        }
                        $scope.onChangeConditionType();

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
        $scope.copyCondition(productId, conditionId).then(() => {
            $scope.fetchConditions(productId);
        })
    }

    function removeCondition(conditionId) {
        $scope.removeCondition(productId, conditionId).then(() => {
            $scope.fetchConditions(productId);
        })
    }

    function getValidConditions() {
        if (isValidCondition()) {
            // init conditions array
            let conditions = [];

            // if computedValue
            if ($scope.conditionType.id === 1) {
                return [{
                    productId: productId,
                    identifier: $scope.selectedConditionIdentifier,
                    type: $scope.conditionType.id,
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
                        productId: productId,
                        identifier: $scope.selectedConditionIdentifier,
                        type: $scope.conditionType.id,
                        operator: $scope.selectedConditionOperator.id,
                        value: '',
                        sectionId: section.id,
                        elementId: null,
                        property: null,
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

            // if no element is selected add single section
            if(null === $scope.selectedConditionElement) {
                return [{
                    productId: productId,
                    identifier: $scope.selectedConditionIdentifier,
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
                        productId: productId,
                        identifier: $scope.selectedConditionIdentifier,
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

            // if no property is selected add single section->element
            if(null === $scope.selectedConditionProperty) {
                return [{
                    productId: productId,
                    identifier: $scope.selectedConditionIdentifier,
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
                        productId: productId,
                        identifier: $scope.selectedConditionIdentifier,
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
                productId: productId,
                identifier: $scope.selectedConditionIdentifier,
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
        if ($scope.conditionType.id === 0) {
            if (
                (null === $scope.selectedConditionElement && null !== $scope.selectedConditionProperty)
                || null === $scope.selectedConditionIdentifier
                || null === $scope.selectedConditionSection
                || null === $scope.selectedConditionOperator
            ) {
                return false;
            }
        }
        if ($scope.conditionType.id === 1) {
            if (
                null === $scope.selectedConditionComputedValue
                || null === $scope.selectedConditionIdentifier
                || null === $scope.selectedConditionOperator
                || '' === $scope.selectedConditionValue
            ) {
                return false;
            }
        }
        return true;
    }

    function resetSelectedCondition() {
        $scope.selectedConditionIdentifier = null;
        $scope.selectableConditionProperties = null;
        $scope.selectableConditionOperators = $scope.operatorsActive;
        $scope.selectedConditionSection = null;
        $scope.selectedConditionElement = null;
        $scope.selectedConditionProperty = null;
        $scope.selectedConditionOperator = null;
        $scope.selectedConditionComputedValue = null;
        $scope.selectedConditionValue = '';
        $scope.conditionType = {
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

    function close() {
        $scope.productDetailReset();
        $scope.productsFetch(
            pageNumber,
            recordsPerPage,
            searchString
        );
        $scope.fetchCategories();
        $mdDialog.cancel();
    }

    init();

    $scope.domainPorpsMultiplierHint = [];

    $scope.newSection = {
        value: [],
        addDefaultElement: false,
    };
    $scope.newRuleName = {
        value: ''
    };
    $scope.newConditionIdentifier = {
        value: ''
    }
    $scope.newPrice = {
        amount: '',
        currencyCode: 'EUR',
        customerGroupId: ''
    };
    $scope.newDiscount = {
        discount: '',
        customerGroupId: '',
        name: null
    };
    $scope.languageFactory = LanguageFactory;

    $scope.configuratorModes = [
        {name: 'OnePage', value: false},
        {name: 'StepByStep', value: true}
    ];

    // when going back in configurator previous steps can be reset or not
    $scope.configuratorKeepSectionOrder = [
        { name: 'Ja', value: true },
        { name: 'Nein', value: false },
    ];

    $scope.onToggleShop = onToggleShop;
    $scope.onToggleCategory = onToggleCategory;
    $scope.addSection = addSection;
    $scope.editSection = editSection;
    $scope.copySection = copySection;
    $scope.removeSection = removeSection;
    $scope.addRule = addRule;
    $scope.addComputedValue = addComputedValue;
    $scope.addPrice = addPrice;
    $scope.removePrice = removePrice;
    $scope.addDiscount = addDiscount;
    $scope.removeDiscount = removeDiscount;
    $scope.editRule = editRule;
    $scope.editComputedValue = editComputedValue;
    $scope.editRuleName = editRuleName;
    $scope.setRuleActive = setRuleActive;
    $scope.removeRule = removeRule;
    $scope.copyRule = copyRule;
    $scope.removeComputedValue = removeComputedValue;
    $scope.setSectionIsActive = setSectionIsActive;
    $scope.setSectionIsMandatory = setSectionIsMandatory;
    $scope.addCustomProperty = addCustomProperty;
    $scope.removeCustomProperty = removeCustomProperty;
    $scope.onSelectPreviewImage = onSelectPreviewImage;
    $scope.getMultiplierHint = getMultiplierHint;
    $scope.updateMultiplierHint = updateMultiplierHint;
    $scope.onSelectDomainPreviewImage = onSelectDomainPreviewImage;
    $scope.save = save;

    $scope.onChangeConditionType = onChangeConditionType;
    $scope.onChangeSelectedConditionSection = onChangeSelectedConditionSection;
    $scope.onChangeSelectedConditionElement = onChangeSelectedConditionElement;
    $scope.onChangeSelectedConditionProperty = onChangeSelectedConditionProperty;
    $scope.onChangeSelectedConditionOperator = onChangeSelectedConditionOperator;

    $scope.addCondition = addCondition;
    $scope.copyCondition = copyCondition;
    $scope.updateCondition = updateCondition;
    $scope.removeCondition = removeCondition;
    $scope.saveCondition = saveCondition;
    $scope.isValidCondition = isValidCondition;
    $scope.resetConditionForm = resetConditionForm;

    $scope.close = close;

    $scope.$on('$destroy', subscribedActions);
};

ProductDetailController.$inject = ProductDetailControllerInject;

export default ProductDetailController;
