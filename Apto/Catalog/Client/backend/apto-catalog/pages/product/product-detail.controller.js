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
        setDetailValue: ProductActions.setDetailValue,
        availableShopsFetch: ProductActions.availableShopsFetch,
        getNextPosition: ProductActions.getNextPosition,
        productDetailAssignProperties: ProductActions.productDetailAssignProperties,
        addComputedProductValueAlias: ProductActions.addComputedProductValueAlias,
        addComputedProductValue: ProductActions.addComputedProductValue,
        fetchComputedProductValues: ProductActions.fetchComputedProductValues,
        removeComputedProductValue: ProductActions.removeComputedProductValue
    })($scope);

    function mapState(state) {
        return {
            productDetail: state.product.productDetail,
            availableCategories: state.product.availableCategories,
            availableShops: state.product.availableShops,
            availableCustomerGroups: state.product.availableCustomerGroups,
            availablePriceCalculators: state.product.availablePriceCalculators,
            sections: state.product.sections,
            rules: state.product.rules,
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
            $scope.fetchRules(productId);
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
                $scope.updateProductRule(productId, rule.id, input.$modelValue, rule.active, rule.errorMessage, rule.conditionsOperator, rule.implicationsOperator, rule.softRule).then(() => {
                    $scope.fetchRules(productId);
                });
            },
            targetEvent: $event,
            title: 'Regelname:'
        };

        $mdEditDialog.large(editRuleNameDialog);
    }

    function setRuleActive(rule) {
        $scope.updateProductRule(productId, rule.id, rule.name, rule.active, rule.errorMessage, rule.conditionsOperator, rule.implicationsOperator, rule.softRule).then(() => {
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
    $scope.close = close;

    $scope.$on('$destroy', subscribedActions);
};

ProductDetailController.$inject = ProductDetailControllerInject;

export default ProductDetailController;
