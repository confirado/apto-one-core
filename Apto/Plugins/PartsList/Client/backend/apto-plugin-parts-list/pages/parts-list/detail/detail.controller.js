import PartTab from './tabs/part.html';
import ProductsTab from './tabs/products.html';
import SectionsTab from './tabs/sections.html';
import ElementsTab from './tabs/elements.html';
import RulesTab from './tabs/rules.html';
import PriceTab from './tabs/price.html';
import CustomPropertiesTab from './tabs/custom-properties.html';

import ElementUsageDetailTemplate from './element-usage/element-usage-detail.html';
import ElementUsageDetailController from './element-usage/element-usage-detail';
import RuleUsageDetailTemplate from './rule-usage/rule-usage-detail.html';
import RuleUsageDetailController from './rule-usage/rule-usage-detail';

const ControllerInject = ['$scope', '$mdDialog', '$ngRedux', '$templateCache', '$mdEditDialog', 'LanguageFactory', 'AptoPartsListPartActions', 'id', 'onClose'];
const Controller = function($scope, $mdDialog, $ngRedux, $templateCache, $mdEditDialog, LanguageFactory, AptoPartsListPartActions, id, onClose) {
    $templateCache.put('apto-plugin-parts-list/pages/parts-list/detail/tabs/part.html', PartTab);
    $templateCache.put('apto-plugin-parts-list/pages/parts-list/detail/tabs/products.html', ProductsTab);
    $templateCache.put('apto-plugin-parts-list/pages/parts-list/detail/tabs/sections.html', SectionsTab);
    $templateCache.put('apto-plugin-parts-list/pages/parts-list/detail/tabs/elements.html', ElementsTab);
    $templateCache.put('apto-plugin-parts-list/pages/parts-list/detail/tabs/rules.html', RulesTab);
    $templateCache.put('apto-plugin-parts-list/pages/parts-list/detail/tabs/price.html', PriceTab);
    $templateCache.put('apto-plugin-parts-list/pages/parts-list/detail/tabs/custom-properties.html', CustomPropertiesTab);

    $scope.mapStateToThis = function(state) {
        return {
            details: state.aptoPartsListPart.details,
            availableUnits: state.aptoPartsListPart.availableUnits,
            availableProducts: state.aptoPartsListPart.availableProducts,
            availableSections: state.aptoPartsListPart.availableSections,
            availableElements: state.aptoPartsListPart.availableElements,
            productUsages: state.aptoPartsListPart.productUsages,
            sectionUsages: state.aptoPartsListPart.sectionUsages,
            elementUsages: state.aptoPartsListPart.elementUsages,
            ruleUsages: state.aptoPartsListPart.ruleUsages,
            customProperties: state.aptoPartsListPart.customProperties,
            prices: state.aptoPartsListPart.prices,
            availableCustomerGroups: state.aptoPartsListPart.availableCustomerGroups,
            categories: state.aptoPartsListPart.categories,
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchDetails: AptoPartsListPartActions.fetchDetails,
        saveDetails: AptoPartsListPartActions.saveDetails,
        resetDetails: AptoPartsListPartActions.resetDetails,
        fetchAvailableUnits: AptoPartsListPartActions.fetchAvailableUnits,
        fetchAvailableProducts: AptoPartsListPartActions.fetchAvailableProducts,
        fetchAvailableSections: AptoPartsListPartActions.fetchAvailableSections,
        fetchAvailableElements: AptoPartsListPartActions.fetchAvailableElements,
        addPartProductUsage: AptoPartsListPartActions.addProductUsage,
        addPartSectionUsage: AptoPartsListPartActions.addSectionUsage,
        addPartElementUsage: AptoPartsListPartActions.addElementUsage,
        addPartRuleUsage: AptoPartsListPartActions.addRuleUsage,
        updatePartProductUsageQuantity: AptoPartsListPartActions.updateProductUsageQuantity,
        updatePartSectionUsageQuantity: AptoPartsListPartActions.updateSectionUsageQuantity,
        removePartProductUsage: AptoPartsListPartActions.removeProductUsage,
        removePartSectionUsage: AptoPartsListPartActions.removeSectionUsage,
        removePartElementUsage: AptoPartsListPartActions.removeElementUsage,
        removePartRuleUsage: AptoPartsListPartActions.removeRuleUsage,
        fetchProductUsages: AptoPartsListPartActions.fetchProductUsages,
        fetchSectionUsages: AptoPartsListPartActions.fetchSectionUsages,
        fetchElementUsages: AptoPartsListPartActions.fetchElementUsages,
        fetchRuleUsages: AptoPartsListPartActions.fetchRuleUsages,
        fetchCustomProperties: AptoPartsListPartActions.fetchCustomProperties,
        addPartCustomProperty: AptoPartsListPartActions.addCustomProperty,
        removePartCustomProperty: AptoPartsListPartActions.removeCustomProperty,
        fetchPrices: AptoPartsListPartActions.fetchPrices,
        addPartPrice: AptoPartsListPartActions.addPartPrice,
        removePartPrice: AptoPartsListPartActions.removePartPrice
    })($scope);

    function init() {
        $scope.editDialog = null;
        if (typeof id !== 'undefined') {
            initNewProductUsage();
            initNewSectionUsage();
            initNewElementUsage();
            initNewRuleUsage();

            $scope.fetchDetails(id).then(() => {
                if ($scope.details.unit) {
                    $scope.selectedUnitId = $scope.details.unit.id;
                }
            });
            $scope.fetchPrices(id);
            $scope.fetchProductUsages(id);
            $scope.fetchSectionUsages(id);
            $scope.fetchElementUsages(id);
            $scope.fetchRuleUsages(id);
            $scope.fetchCustomProperties(id)
        }
        $scope.fetchAvailableUnits();
        $scope.fetchAvailableProducts();
        $scope.fetchAvailableSections();
        $scope.fetchAvailableElements();
    }

    function closeEditDialogAndShow(dialog) {
        if (null !== $scope.editDialog) {
            $scope.editDialog.then((controller) => {
                controller.dismiss();
                $scope.editDialog = $mdEditDialog.large(dialog);
            }, () => {});
        } else {
            $scope.editDialog = $mdEditDialog.large(dialog);
        }
    }

    function closeEditDialog() {
        if (null !== $scope.editDialog) {
            $scope.editDialog.then((controller) => {
                controller.dismiss();
            }, () => {});
        }
    }

    function initNewProductUsage() {
        $scope.newProductUsage = {
            usedForUuid: null,
            quantity: null
        };
    }

    function initNewSectionUsage() {
        $scope.newSectionUsage = {
            usedForUuid: null,
            quantity: null,
            productId: null
        };
    }

    function initNewElementUsage() {
        $scope.newElementUsage = {
            usedForUuid: null,
            quantity: null,
            productId: null
        };
    }

    function initNewRuleUsage() {
        $scope.newRuleUsage = {
            name: null,
            quantity: null
        };
    }

    function clearUnitSearchTerm() {
        $scope.unitSearchTerm = '';
    }

    function addProductUsage() {
        $scope.addPartProductUsage(id, $scope.newProductUsage.usedForUuid, $scope.newProductUsage.quantity).then(() => {
            initNewProductUsage();
            clearProductSearchTerm();
            $scope.fetchProductUsages(id);
        });
    }

    function addSectionUsage() {
        $scope.addPartSectionUsage(id, $scope.newSectionUsage.usedForUuid, $scope.newSectionUsage.quantity, $scope.newSectionUsage.productId).then(() => {
            initNewSectionUsage();
            clearSectionSearchTerm();
            $scope.fetchSectionUsages(id);
        });
    }

    function addElementUsage() {
        $scope.addPartElementUsage(id, $scope.newElementUsage.usedForUuid, $scope.newElementUsage.quantity, $scope.newElementUsage.productId).then(() => {
            initNewElementUsage();
            clearElementSearchTerm();
            $scope.fetchElementUsages(id);
        });
    }

    function addRuleUsage() {
        $scope.addPartRuleUsage(id, $scope.newRuleUsage.name, $scope.newRuleUsage.quantity).then(() => {
            initNewRuleUsage();
            $scope.fetchRuleUsages(id);
        });
    }

    function addCustomProperty(key, value, translatable) {
        if (translatable === null || translatable === undefined) {
            translatable = false;
        }
        $scope.addPartCustomProperty(id, key, value, translatable).then(() => {
            $scope.fetchCustomProperties(id);
        });
    }

    function removeCustomProperty(key) {
        $scope.removePartCustomProperty(id, key).then(() => {
            $scope.fetchCustomProperties(id);
        });
    }

    function updateProductUsageQuantity($event, usage) {
        $event.stopPropagation();
        closeEditDialogAndShow({
            modelValue: usage.quantity,
            save: function (input) {
                $scope.updatePartProductUsageQuantity(id, usage.id, input.$modelValue).then(() => {
                    $scope.fetchProductUsages(id);
                });
            },
            targetEvent: $event,
            cancel: 'Abbrechen',
            ok: 'Speichern',
            title: 'Anzahl:'
        });
    }

    function updateSectionUsageQuantity($event, usage) {
        $event.stopPropagation();
        closeEditDialogAndShow({
            modelValue: usage.quantity,
            save: function (input) {
                $scope.updatePartSectionUsageQuantity(id, usage.id, input.$modelValue).then(() => {
                    $scope.fetchSectionUsages(id);
                });
            },
            targetEvent: $event,
            cancel: 'Abbrechen',
            ok: 'Speichern',
            title: 'Anzahl:'
        });
    }

    function showElementUsageDetails($event, elementUsageId) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            clickOutsideToClose: false,
            fullscreen: true,
            multiple: true,
            locals: {
                partId: id,
                elementUsageId: elementUsageId,
                onClose: function() {
                    $scope.fetchElementUsages(id);
                }
            },
            template: ElementUsageDetailTemplate,
            controller: ElementUsageDetailController
        });
    }

    function showRuleUsageDetails($event, ruleUsageId) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            clickOutsideToClose: false,
            fullscreen: true,
            multiple: true,
            locals: {
                partId: id,
                ruleUsageId: ruleUsageId,
                onClose: function() {
                    $scope.fetchRuleUsages(id);
                }
            },
            template: RuleUsageDetailTemplate,
            controller: RuleUsageDetailController
        });
    }

    function removeProductUsage(usageId) {
        $scope.removePartProductUsage(id, usageId).then(() => {
            $scope.fetchProductUsages(id);
        });
    }

    function removeSectionUsage(usageId) {
        $scope.removePartSectionUsage(id, usageId).then(() => {
            $scope.fetchSectionUsages(id);
        });
    }

    function removeElementUsage(usageId) {
        $scope.removePartElementUsage(id, usageId).then(() => {
            $scope.fetchElementUsages(id);
        });
    }

    function removeRuleUsage(usageId) {
        $scope.removePartRuleUsage(id, usageId).then(() => {
            $scope.fetchRuleUsages(id);
        });
    }

    function clearProductSearchTerm() {
        $scope.productSearchTerm = '';
    }

    function clearSectionSearchTerm() {
        $scope.sectionSearchTerm = '';
    }

    function clearElementSearchTerm() {
        $scope.elementSearchTerm = '';
    }

    function getUsageIdentifierByUsageForUuid(usageForUuid, type) {
        let listProperty = null;

        switch (type) {
            case 'product': {
                listProperty = 'availableProducts';
                break;
            }
            case 'section': {
                listProperty = 'availableSections';
                break;
            }
            case 'element': {
                listProperty = 'availableElements';
                break;
            }
        }

        for (let i = 0; i < $scope[listProperty].length; i++) {
            if ($scope[listProperty][i].id === usageForUuid) {
                return $scope[listProperty][i].identifier;
            }
        }

        return usageForUuid;
    }

    function addPrice() {
        $scope.addPartPrice(id, $scope.newPrice.amount, $scope.newPrice.currencyCode, $scope.newPrice.customerGroupId).then(() => {
            $scope.newPrice = {
                amount: '',
                currencyCode: 'EUR',
                customerGroupId: ''
            };
            $scope.fetchPrices(id);
        });
    }

    function removePrice(priceId) {
        $scope.removePartPrice(id, priceId).then(() => {
            $scope.fetchPrices(id);
        });
    }

    function onSectionChange(section) {
        $scope.newSectionUsage.usedForUuid = section.id;
        $scope.newSectionUsage.productId = section.productId;
    }

    function onElementChange(element) {
        $scope.newElementUsage.usedForUuid = element.id;
        $scope.newElementUsage.productId = element.productId;
    }

    function save(detailsForm, close) {
        if (detailsForm.$valid) {
            $scope.saveDetails($scope.details, $scope.selectedUnitId).then(function () {
                if (typeof close !== 'undefined') {
                    $scope.close(false);
                } else if (typeof $scope.details.id === 'undefined') {
                    $scope.close(true);
                }
            });
        }
    }

    function close(reopen) {
        closeEditDialog();
        $mdDialog.cancel();
        $scope.resetDetails();
        if (typeof onClose === 'function') {
            onClose(reopen);
        }
    }

    init();

    $scope.newPrice = {
        amount: '',
        currencyCode: 'EUR',
        customerGroupId: ''
    };

    $scope.selectedUnitId = null;
    $scope.translate = LanguageFactory.translate;

    $scope.addProductUsage = addProductUsage;
    $scope.addSectionUsage = addSectionUsage;
    $scope.addElementUsage = addElementUsage;
    $scope.addRuleUsage = addRuleUsage;
    $scope.addCustomProperty = addCustomProperty;

    $scope.updateProductUsageQuantity = updateProductUsageQuantity;
    $scope.updateSectionUsageQuantity = updateSectionUsageQuantity;
    $scope.showElementUsageDetails = showElementUsageDetails;
    $scope.showRuleUsageDetails = showRuleUsageDetails;

    $scope.removeProductUsage = removeProductUsage;
    $scope.removeSectionUsage = removeSectionUsage;
    $scope.removeElementUsage = removeElementUsage;
    $scope.removeRuleUsage = removeRuleUsage;
    $scope.removeCustomProperty = removeCustomProperty;

    $scope.addPrice = addPrice;
    $scope.removePrice = removePrice;

    $scope.unitSearchTerm = '';
    $scope.productSearchTerm = '';
    $scope.sectionSearchTerm = '';
    $scope.elementSearchTerm = '';

    $scope.clearUnitSearchTerm = clearUnitSearchTerm;
    $scope.clearProductSearchTerm = clearProductSearchTerm;
    $scope.clearSectionSearchTerm = clearSectionSearchTerm;
    $scope.clearElementSearchTerm = clearElementSearchTerm;

    $scope.onSectionChange = onSectionChange;
    $scope.onElementChange = onElementChange;

    $scope.getUsageIdentifierByUsageForUuid = getUsageIdentifierByUsageForUuid;

    $scope.save = save;
    $scope.close = close;
    $scope.closeEditDialog = closeEditDialog;

    $scope.$on('$destroy', subscribedActions);
};

Controller.$inject = ControllerInject;

export default Controller;
