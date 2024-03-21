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
        fetchProductConditions: ProductActions.fetchProductConditions,
        addProductCondition: ProductActions.addProductCondition,
        updateProductCondition: ProductActions.updateProductCondition,
        copyProductCondition: ProductActions.copyProductCondition,
        removeProductCondition: ProductActions.removeProductCondition,
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
            $scope.fetchProductConditions(productId);
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

        $scope.currentProductConditionIdentifier = null;
        $scope.selectableProductConditionProperties = null;
        $scope.selectableProductConditionOperators = $scope.operatorsActive;
        $scope.selectedProductConditionSection = null;
        $scope.selectedProductConditionElement = null;
        $scope.selectedProductConditionProperty = null;
        $scope.selectedProductConditionOperator = null;
        $scope.selectedProductConditionValue = '';
        $scope.selectedProductConditionComputedValue = null;
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

    function onChangeProductConditionType() {
        if ($scope.conditionTypes.id === 1) {
            $scope.selectableProductConditionOperators = $scope.operatorsEqual;
        }
    }

    function onChangeSelectedProductConditionSection() {
        if ($scope.selectedProductConditionSection && $scope.selectedProductConditionSection.length === 0) {
            $scope.selectedProductConditionSection = null;
        }
        $scope.selectedProductConditionElement = null;
        $scope.selectedProductConditionProperty = null;
        $scope.selectedProductConditionOperator = null;
        $scope.selectableProductConditionProperties = null;
        $scope.selectableProductConditionOperators = $scope.operatorsActive;
    }

    function onChangeSelectedProductConditionElement() {
        if ($scope.selectedProductConditionElement && $scope.selectedProductConditionElement.length === 0) {
            $scope.selectedProductConditionElement = null;
        }
        $scope.selectedProductConditionProperty = null;
        $scope.selectedProductConditionOperator = null;
        if ($scope.selectedProductConditionElement && $scope.selectedProductConditionElement.length === 1) {
            $scope.selectableProductConditionProperties = getElementSelectableProperties($scope.selectedProductConditionElement[0].definition);
        }
        $scope.selectableProductConditionOperators = $scope.operatorsActive;
    }

    function onChangeSelectedProductConditionProperty() {
        if ($scope.selectedProductConditionProperty && $scope.selectedProductConditionProperty.length === 0) {
            $scope.selectedProductConditionProperty = null;
        }
        $scope.selectedProductConditionOperator = null;
        if ($scope.selectedProductConditionProperty !== null) {
            $scope.selectableProductConditionOperators = $scope.operatorsEqual;
        } else {
            $scope.selectableProductConditionOperators = $scope.operatorsActive;
        }
    }

    function onChangeSelectedProductConditionOperator() {
        if ($scope.selectedProductConditionOperator.id === 0 || $scope.selectedProductConditionOperator.id === 1) {
            $scope.selectedProductConditionValue = '';
        }
    }

    function getElementSelectableProperties(definitionClass) {
        if (!definitionClass.properties) {
            return null;
        }
        return Object.keys(definitionClass.properties);
    }

    function addProductCondition() {
        const conditions = getValidProductConditions();

        if (false !== conditions) {
            let calledCommands = [];

            for (let i = 0; i < conditions.length; i++) {
                calledCommands.push($scope.addProductCondition(productId, conditions[i]));
            }

            Promise.all(calledCommands).then((values) => {
                $scope.fetchConditions(productId);
            });

            resetSelectedProductCondition();
        }
        // @todo show error message
        return false;
    }

    function saveCondition() {
        const condition = {
            computedProductValueId: $scope.selectedProductConditionComputedValue ? $scope.selectedProductConditionComputedValue.id : null,
            elementId: $scope.selectedProductConditionElement ? $scope.selectedProductConditionElement[0].id : null,
            identifier: $scope.currentProductConditionIdentifier,
            operatorId: $scope.selectedProductConditionOperator ? $scope.selectedProductConditionOperator.id : null,
            property: $scope.selectedProductConditionProperty ? $scope.selectedProductConditionProperty[0] : null,
            sectionId: $scope.selectedProductConditionSection ? $scope.selectedProductConditionSection[0].id : null,
            typeId: $scope.conditionType.id,
            value: $scope.selectedProductConditionValue,
        }

        $scope.updateProductCondition(productId, condition).then(() => {
            $scope.fetchConditions(productId);
            resetSelectedProductCondition();
            $scope.currentProductConditionId = null;
        });
    }

    function updateCondition(conditionId) {

        $scope.currentProductConditionIdentifier = conditionId;

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
                        $scope.onChangeProductConditionType();

                        // section
                        $scope.selectedProductConditionSection = [getSection(condition.sectionId)];
                        onChangeSelectedProductConditionSection();

                        // element
                        const element = getElement(condition.sectionId, condition.elementId);
                        if (element) {
                            $scope.selectedProductConditionElement = [element];
                            onChangeSelectedProductConditionElement();
                        }
                        // berechnete werte
                    } else {
                        $scope.conditionType = {
                            name: 'Berechneter Wert',
                            id: 1
                        }
                        $scope.onChangeProductConditionType();

                        $scope.selectedProductConditionComputedValue = condition.computedProductValue[0];
                    }

                    // field
                    const property = condition.property;
                    if (property) {
                        $scope.selectedProductConditionProperty = [property];
                        onChangeSelectedProductConditionProperty();
                    }

                    // operator
                    $scope.selectedProductConditionOperator = getOperator(condition.operator);
                    onChangeSelectedProductConditionOperator();

                    // value
                    $scope.selectedProductConditionValue = condition.value;
                }
            });
        });
    }

    function copyCondition(conditionId) {
        $scope.copyProductCondition(productId, conditionId).then(() => {
            $scope.fetchProductConditions(productId);
        })
    }

    function removeCondition(conditionId) {
        $scope.removeProductCondition(productId, conditionId).then(() => {
            $scope.fetchProductConditions(productId);
        })
    }

    function getValidProductConditions() {
        if (isValidProductCondition()) {
            // init conditions array
            let conditions = [];

            // if computedValue
            if ($scope.conditionType.id === 1) {
                return [{
                    conditionId: $scope.condition.id,
                    identifier: $scope.currentProductConditionIdentifier,
                    type: $scope.conditionType.id,
                    operator: $scope.selectedProductConditionOperator.id,
                    computedValue: $scope.selectedProductConditionComputedValue.id,
                    value: $scope.selectedProductConditionValue
                }]
            }

            // if more then one section is selected only section conditions will be returned
            if ($scope.selectedProductConditionSection.length > 1) {
                for (let i = 0; i < $scope.selectedProductConditionSection.length; i++) {
                    const section = $scope.selectedProductConditionSection[i];

                    conditions.push({
                        identifier: $scope.currentProductConditionIdentifier,
                        sectionId: section.id,
                        elementId: null,
                        property: null,
                        operator: $scope.selectedProductConditionOperator.id,
                        value: ''
                    });
                }

                return conditions;
            }

            // check if one section is selected
            if (!$scope.selectedProductConditionSection[0]) {
                return false;
            }

            // if only one section is selected this section is for all selected elements
            const section = $scope.selectedProductConditionSection[0];

            // if no element is selected add single section
            if(null === $scope.selectedProductConditionElement) {
                return [{
                    conditionId: $scope.condition.id,
                    identifier: $scope.currentProductConditionIdentifier,
                    sectionId: section.id,
                    elementId: null,
                    property: null,
                    operator: $scope.selectedProductConditionOperator.id,
                    value: $scope.selectedProductConditionValue
                }];
            }

            // if more then one element is selected only section->element conditions will be returned
            if ($scope.selectedProductConditionElement.length > 1) {
                for (let i = 0; i < $scope.selectedProductConditionElement.length; i++) {
                    const element = $scope.selectedProductConditionElement[i];

                    conditions.push({
                        conditionId: $scope.condition.id,
                        identifier: $scope.currentProductConditionIdentifier,
                        sectionId: section.id,
                        elementId: element.id,
                        property: null,
                        operator: $scope.selectedProductConditionOperator.id,
                        value: ''
                    });
                }

                return conditions;
            }

            // check if one element is selected
            if (!$scope.selectedProductConditionElement[0]) {
                return false;
            }

            // if only one element is selected this element is for all selected properties
            const element = $scope.selectedProductConditionElement[0];

            // if no property is selected add single section->element
            if(null === $scope.selectedProductConditionProperty) {
                return [{
                    conditionId: $scope.condition.id,
                    identifier: $scope.currentProductConditionIdentifier,
                    sectionId: section.id,
                    elementId: element.id,
                    property: null,
                    operator: $scope.selectedProductConditionOperator.id,
                    value: $scope.selectedProductConditionValue
                }];
            }

            // if more then one property is selected only section->element->property conditions will be returned
            if ($scope.selectedProductConditionProperty.length > 1) {
                for (let i = 0; i < $scope.selectedProductConditionProperty.length; i++) {
                    const property = $scope.selectedProductConditionProperty[i];

                    conditions.push({
                        conditionId: $scope.condition.id,
                        identifier: $scope.currentProductConditionIdentifier,
                        sectionId: section.id,
                        elementId: element.id,
                        property: property,
                        operator: $scope.selectedProductConditionOperator.id,
                        value: $scope.selectedProductConditionValue
                    });
                }

                return conditions;
            }

            // check if one property is selected
            if (!$scope.selectedProductConditionProperty[0]) {
                return false;
            }

            // add one single section->element->property condition
            const property = $scope.selectedProductConditionProperty[0];
            return [{
                conditionId: $scope.condition.id,
                identifier: $scope.currentProductConditionIdentifier,
                sectionId: section.id,
                elementId: element.id,
                property: property,
                operator: $scope.selectedProductConditionOperator.id,
                value: $scope.selectedProductConditionValue
            }];
        }

        // return false if no condition can be created
        return false;
    }

    function isValidProductCondition() {
        if ($scope.conditionType.id === 0) {
            if (
                (null === $scope.selectedConditionElement && null !== $scope.selectedConditionProperty)
                || null === $scope.selectedP
                || null === $scope.selectedConditionSection
                || null === $scope.selectedConditionOperator
            ) {
                return false;
            }
        }
        if ($scope.conditionType.id === 1) {
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

    function resetSelectedProductCondition() {
        $scope.currentProductConditionIdentifier = null;
        $scope.selectableProductConditionProperties = null;
        $scope.selectableProductConditionOperators = $scope.operatorsActive;
        $scope.selectedProductConditionSection = null;
        $scope.selectedProductConditionElement = null;
        $scope.selectedProductConditionProperty = null;
        $scope.selectedProductConditionOperator = null;
        $scope.selectedProductConditionComputedValue = null;
        $scope.selectedProductConditionValue = '';
        $scope.conditionType = {
            name: 'Standard',
            id: 0
        };
    }

    function resetProductConditionForm() {
        resetSelectedProductCondition();
        $scope.currentConditionId = null;
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
    $scope.newComputedValue = {
        name: ''
    };
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

    $scope.onChangeProductConditionType = onChangeProductConditionType;
    $scope.onChangeSelectedProductConditionSection = onChangeSelectedProductConditionSection;
    $scope.onChangeSelectedProductConditionElement = onChangeSelectedProductConditionElement;
    $scope.onChangeSelectedProductConditionProperty = onChangeSelectedProductConditionProperty;
    $scope.onChangeSelectedProductConditionOperator = onChangeSelectedProductConditionOperator;

    $scope.addProductCondition = addProductCondition;
    $scope.copyCondition = copyCondition;
    $scope.updateCondition = updateCondition;
    $scope.removeCondition = removeCondition;
    $scope.saveCondition = saveCondition;
    $scope.isValidProductCondition = isValidProductCondition;
    $scope.resetProductConditionForm = resetProductConditionForm;

    $scope.close = close;

    $scope.$on('$destroy', subscribedActions);
};

ProductDetailController.$inject = ProductDetailControllerInject;

export default ProductDetailController;
