import SectionTab from './section-tab.html';
import PriceTab from './price-tab.html';
import ElementTab from './element-tab.html';
import DiscountTab from './discount-tab.html';
import CustomPropertiesTab from './custom-properties-tab.html';

import ElementDetailTemplate from '../element/element-detail.controller.html';
import ElementDetailController from '../element/element-detail.controller';

const SectionDetailControllerInject = ['$scope', '$document', '$templateCache', '$mdDialog', '$ngRedux', 'LanguageFactory', 'ProductActions', 'SectionActions', 'targetEvent', 'productId', 'sectionId', 'sections', 'editSection'];
const SectionDetailController = function($scope, $document, $templateCache, $mdDialog, $ngRedux, LanguageFactory, ProductActions, SectionActions, targetEvent, productId, sectionId, sections, editSection) {
    $templateCache.put('catalog/pages/product/section/section-tab.html', SectionTab);
    $templateCache.put('catalog/pages/product/section/price-tab.html', PriceTab);
    $templateCache.put('catalog/pages/product/section/element-tab.html', ElementTab);
    $templateCache.put('catalog/pages/product/section/discount-tab.html', DiscountTab);
    $templateCache.put('catalog/pages/product/section/custom-properties-tab.html', CustomPropertiesTab);

    const subscribedActions = $ngRedux.connect(mapState, {
        fetchDetail: SectionActions.fetchDetail,
        fetchElements: SectionActions.fetchElements,
        fetchPrices: SectionActions.fetchPrices,
        fetchDiscounts: SectionActions.fetchDiscounts,
        fetchGroups: SectionActions.fetchGroups,
        resetStore: SectionActions.reset,
        updateProductSection: ProductActions.updateProductSection,
        addProductElement: ProductActions.addProductElement,
        copyProductElement: ProductActions.copyProductElement,
        removeProductElement: ProductActions.removeProductElement,
        addProductSectionPrice: ProductActions.addProductSectionPrice,
        removeProductSectionPrice: ProductActions.removeProductSectionPrice,
        addProductSectionDiscount: ProductActions.addProductSectionDiscount,
        removeProductSectionDiscount: ProductActions.removeProductSectionDiscount,
        fetchProductSections: ProductActions.fetchSections,
        setProductElementIsDefault: ProductActions.setProductElementIsDefault,
        setProductElementIsActive: ProductActions.setProductElementIsActive,
        setProductElementIsMandatory: ProductActions.setProductElementIsMandatory,
        setProductSectionAllowMulti: ProductActions.setProductSectionAllowMulti,
        addProductSectionCustomProperty: ProductActions.addProductSectionCustomProperty,
        removeProductSectionCustomProperty: ProductActions.removeProductSectionCustomProperty,
        fetchCustomProperties: SectionActions.fetchCustomProperties,
        setDetailValue: SectionActions.setDetailValue,
        fetchConditions: ProductActions.fetchConditions,
    })($scope);

    function mapState(state) {
        return {
            availableCustomerGroups: state.product.availableCustomerGroups,
            detail: state.section.detail,
            section: state.section,
            productDetail: state.product.productDetail,
            computedValues: state.product.computedValues,
            elements: state.section.elements,
            prices: state.section.prices,
            discounts: state.section.discounts,
            groups: state.section.groups,
            customProperties: state.section.customProperties,
            conditions: state.product.conditions,
        }
    }

    function init() {
        $scope.languageFactory = LanguageFactory;
        $scope.fetchDetail(sectionId).then(() => {
            $scope.fetchGroups();
            if ($scope.detail.group.length > 0) {
                $scope.selectedGroup = $scope.detail.group[0].id;
            }
        });
        $scope.fetchElements(sectionId);
        $scope.fetchPrices(sectionId);
        $scope.fetchDiscounts(sectionId);
        $scope.fetchCustomProperties(sectionId);
        $scope.fetchConditions(productId);
        $scope.sectionListOpen = false;

        $scope.productId = productId;
    }

    $scope.getConditionName = function (id) {
        const condition = $scope.conditions.find((c) => c.id === id);

        return condition ? condition.identifier : null;
    }

    function addElement() {
        $scope.addProductElement(productId, sectionId, $scope.newElement.value).then(() => {
            $scope.newElement = {
                value: []
            };
            $scope.fetchElements(sectionId);
        });
    }

    function editElement($event, elementId) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            multiple: true,
            parent: parentEl,
            targetEvent: $event,
            template: ElementDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                productId: productId,
                sectionId: sectionId,
                elementId: elementId,
                closeSection: closeSection,
                elements: $scope.elements,
                editElement: editElement
            },
            controller: ElementDetailController
        });
    }

    function copyElement(elementId) {
        $scope.copyProductElement(productId, sectionId, elementId).then(() => {
            $scope.fetchElements(sectionId);
        });
    }

    function removeElement(elementId) {
        $scope.removeProductElement(productId, sectionId, elementId).then(() => {
            $scope.fetchElements(sectionId);
        });
    }

    function setElementIsDefault(elementId, elementIsDefault) {
        $scope.setProductElementIsDefault(productId, sectionId, elementId, elementIsDefault).then(() => {
            $scope.fetchElements(sectionId);
        });
    }

    function setElementIsActive(elementId, elementIsActive) {
        $scope.setProductElementIsActive(productId, sectionId, elementId, elementIsActive).then(() => {
            $scope.fetchElements(sectionId);
        });
    }

    function setElementIsMandatory(elementId, elementIsMandatory) {
        $scope.setProductElementIsMandatory(productId, sectionId, elementId, elementIsMandatory).then(() => {
            $scope.fetchElements(sectionId);
        });
    }

    function addPrice() {
        $scope.addProductSectionPrice(
            productId,
            sectionId,
            $scope.newPrice.amount,
            $scope.newPrice.currencyCode,
            $scope.newPrice.customerGroupId,
            $scope.newPrice.productConditionId
        ).then(() => {
            $scope.newPrice = {
                amount: '',
                currencyCode: 'EUR',
                customerGroupId: '',
                productConditionId: null
            };
            $scope.fetchPrices(sectionId);
        });
    }

    function removePrice(priceId) {
        $scope.removeProductSectionPrice(
            productId,
            sectionId,
            priceId
        ).then(() => {
            $scope.fetchPrices(sectionId);
        });
    }

    function addDiscount() {
        $scope.addProductSectionDiscount(
            productId,
            sectionId,
            $scope.newDiscount.discount,
            $scope.newDiscount.customerGroupId,
            $scope.newDiscount.name
        ).then(() => {
            $scope.newDiscount = {
                discount: '',
                customerGroupId: '',
                name: null
            };
            $scope.fetchDiscounts(sectionId);
        });
    }

    function removeDiscount(discountId) {
        $scope.removeProductSectionDiscount(
            productId,
            sectionId,
            discountId
        ).then(() => {
            $scope.fetchDiscounts(sectionId);
        });
    }

    function moreThanOneDefaultElement() {
        let counter = 0;
        for(let i = 0; i < $scope.elements.length; i++) {
            if($scope.elements[i].isDefault) {
                counter++;
            }
        }

        return counter > 1;
    }

    function setAllowMultiple(allowMultiple) {
        $scope.setProductSectionAllowMulti(productId, sectionId, allowMultiple).then(() => {
            $scope.fetchDetail(sectionId);
        });
    }

    function clearSearchTerm() {
        $scope.groupSearchTerm = '';
    }

    function addSectionCustomProperty(key, value, translatable) {
        $scope.addProductSectionCustomProperty(productId, sectionId, key, value, translatable).then(() => {
            $scope.fetchCustomProperties(sectionId);
        });
    }

    function removeSectionCustomProperty(key) {
        $scope.removeProductSectionCustomProperty(productId, sectionId, key).then(() => {
            $scope.fetchCustomProperties(sectionId);
        });
    }

    function onSelectPreviewImage(path) {
        $scope.setDetailValue('previewImage', path);
    }

    function save(sectionForm) {
        if(sectionForm.$valid && ($scope.detail.identifier || !$scope.languageFactory.isEmpty($scope.detail.name))) {
            $scope.updateProductSection(productId, sectionId, $scope.detail, $scope.selectedGroup).then(() => {
                $scope.fetchProductSections(productId);
                $scope.close();
            });
        }
    }

    function close() {
        $scope.resetStore();
        $mdDialog.cancel();
    }

    function closeAndEditSection($event, sectionId) {
        editSection($event, sectionId);
        close();
    }

    function showSectionList() {
        $scope.sectionListOpen = !$scope.sectionListOpen;
    }

    function getSectionListPosition() {
        const productTitleHeader = angular.element('.product-title-header')
        return productTitleHeader.outerWidth() + "px";
    }

    function getName() {
        if(typeof $scope.detail !== "undefined") {
            if(Object.keys($scope.detail.name).length !== 0) {
                return $scope.languageFactory.translate( $scope.detail.name )
            }
            return $scope.detail.identifier;
        }
        return '';
    }

    function getSectionName(section) {
        if(typeof section === "undefined") {
            return
        }

        if(section.name) {
            return $scope.languageFactory.translate( section.name )
        }

        return section.identifier;
    }

    function closeSection() {
        close();
    }

    init();

    $scope.repeatable = {
        types: ['Statisch', 'Wiederholbar'],
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

    $scope.newElement = {
        value: []
    };
    $scope.selectedGroup = '';
    $scope.groupSearchTerm = '';

    $scope.addElement = addElement;
    $scope.editElement = editElement;
    $scope.copyElement = copyElement;
    $scope.removeElement = removeElement;
    $scope.addPrice = addPrice;
    $scope.removePrice = removePrice;
    $scope.addDiscount = addDiscount;
    $scope.removeDiscount = removeDiscount;
    $scope.setElementIsDefault = setElementIsDefault;
    $scope.setElementIsActive = setElementIsActive;
    $scope.setElementIsMandatory = setElementIsMandatory;
    $scope.moreThanOneDefaultElement = moreThanOneDefaultElement;
    $scope.setAllowMultiple = setAllowMultiple;
    $scope.clearSearchTerm = clearSearchTerm;
    $scope.addSectionCustomProperty = addSectionCustomProperty;
    $scope.removeSectionCustomProperty = removeSectionCustomProperty;
    $scope.closeSection = closeSection;
    $scope.save = save;
    $scope.close = close;
    $scope.getName = getName;
    $scope.getSectionName = getSectionName;
    $scope.sections = sections;
    $scope.sectionId = sectionId;
    $scope.onSelectPreviewImage = onSelectPreviewImage;
    $scope.editSection = closeAndEditSection;
    $scope.showSectionList = showSectionList;
    $scope.getSectionListPosition = getSectionListPosition;

    $scope.$on('$destroy', subscribedActions);
};

SectionDetailController.$inject = SectionDetailControllerInject;

export default SectionDetailController;
