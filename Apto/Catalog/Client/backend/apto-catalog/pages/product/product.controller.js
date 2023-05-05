import { Promise } from 'es6-promise';
import ProductDetailTemplate from './product-detail.controller.html';
import ProductDetailController from './product-detail.controller';
import ProductFilter from './product-filter.html';
import ProductBatchChange from './product-batch-change.html';

const ProductControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'ProductActions', 'IndexActions', 'LanguageFactory', 'BatchManipulationActions', 'MessageBusFactory', 'FilterPropertyActions'];
const ProductController = function($scope, $templateCache, $mdDialog, $ngRedux, ProductActions, IndexActions, LanguageFactory, BatchManipulationActions, MessageBusFactory, FilterPropertyActions) {
    $templateCache.put('catalog/pages/product/product-filter.html', ProductFilter);
    $templateCache.put('catalog/pages/product/product-batch-change.html', ProductBatchChange);

    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.product.pageHeaderConfig,
            dataListConfig: state.product.dataListConfig,
            products: state.product.products,
            categories: state.product.availableCategories,
            batchManipulationInProgress: state.batchManipulation.inProgress,
            batchManipulationBatchMessage: state.batchManipulation.batchMessage,
            batchManipulationProcessMessage: state.batchManipulation.processMessage,
            batchManipulationConflictMessage: state.batchManipulation.conflictMessage
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        setListTemplate: ProductActions.setListTemplate,
        toggleSidebarRight: IndexActions.toggleSidebarRight,
        productsFetch: ProductActions.productsFetch,
        productsFetchByFilter: ProductActions.productsFetchByFilter,
        fetchCategories: ProductActions.fetchCategories,
        availableCategoriesFetch: ProductActions.availableCategoriesFetch,
        availableCustomerGroupsFetch: ProductActions.availableCustomerGroupsFetch,
        availablePriceCalculatorsFetch: ProductActions.availablePriceCalculatorsFetch,
        productRemove: ProductActions.productRemove,
        productCopy: ProductActions.productCopy,
        setBatchPrices: BatchManipulationActions.setBatchPrices,
        setBatchPricesByFormula: BatchManipulationActions.setBatchPricesByFormula,
        setBatchManipulationInProgress: BatchManipulationActions.setInProgress,
        setBatchManipulationBatchMessage: BatchManipulationActions.setBatchMessage,
        setBatchManipulationProcessMessage: BatchManipulationActions.setProcessMessage,
        setBatchManipulationConflictMessage: BatchManipulationActions.setConflictMessage,
        fetchFilterProperties: FilterPropertyActions.fetchFilterProperties
    })($scope);

    $scope.pageHeaderActions = {
        pageChanged: {
            fnc: function (page) {
                $scope.productsFetchByFilter(
                    page,
                    $scope.pageHeaderConfig.pagination.recordsPerPage,
                    $scope.filter
                );
            }
        },
        add: {
            fnc: showDetailsDialog
        },
        listStyle: {
            fnc: $scope.setListTemplate
        },
        toggleSideBarRight: {
            fnc: $scope.toggleSidebarRight
        }
    };

    $scope.dataListActions = {
        edit: {
            fnc: showDetailsDialog
        },
        remove: {
            fnc: function ($event, id) {
                $scope.productRemove(id).then(function () {
                    $scope.productsFetchByFilter(
                        $scope.pageHeaderConfig.pagination.pageNumber,
                        $scope.pageHeaderConfig.pagination.recordsPerPage,
                        $scope.filter
                    );
                });
            }
        },
        copy: {
            fnc: function ($event, id) {
                $scope.productCopy(id).then(function () {
                    $scope.productsFetchByFilter(
                        $scope.pageHeaderConfig.pagination.pageNumber,
                        $scope.pageHeaderConfig.pagination.recordsPerPage,
                        $scope.filter
                    );
                });
            }
        }
    };

    $scope.batchManipulation = {
        multiplier: 100,
        multiplierHint: '',
        formula: 'x * 1',
        useFormula: false,
        filter: {
            'PriceMatrix': {
                'PriceMatrix': true,
            },
            'Product': {
                'Product': true,
                'Section': true,
                'Element': true
            },
            'FloatInputElement': {
                'FloatInputElement': true,
            },
            'PricePerUnit': {
                'PricePerUnit': true,
            },
            'SelectBoxElement': {
                'SelectBoxElement': true,
            }
        }
    }

    $scope.productsFetchByFilter(
        $scope.pageHeaderConfig.pagination.pageNumber,
        $scope.pageHeaderConfig.pagination.recordsPerPage,
        $scope.filter
    );
    $scope.fetchCategories();

    resetFilter();
    updateMultiplierHint();
    resetBatchManipulation();

    function setFilter() {
        resetBatchManipulation();

        $scope.productsFetchByFilter(
            $scope.pageHeaderConfig.pagination.pageNumber,
            $scope.pageHeaderConfig.pagination.recordsPerPage,
            $scope.filter
        );
    }

    function resetFilter() {
        $scope.categoriesSearch = '';
        $scope.filter = {
            searchString: '',
            categories: []
        }
    }

    function resetBatchManipulation() {
        $scope.setBatchManipulationInProgress(false);
        $scope.setBatchManipulationBatchMessage('');
        $scope.setBatchManipulationProcessMessage('');
        $scope.setBatchManipulationConflictMessage('');
    }

    function showDetailsDialog($event, productId) {
        $scope.fetchFilterProperties('');
        const parentEl = angular.element(document.body);
        $scope.availableCategoriesFetch();
        $scope.availableCustomerGroupsFetch();
        $scope.availablePriceCalculatorsFetch();
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: ProductDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                pageNumber: $scope.pageHeaderConfig.pagination.pageNumber,
                recordsPerPage: $scope.pageHeaderConfig.pagination.recordsPerPage,
                searchString: $scope.pageHeaderConfig.search.searchString,
                productId: productId
            },
            controller: ProductDetailController
        });
    }

    function cancelBatchManipulation() {
        $scope.setBatchManipulationInProgress(false);
        $scope.setBatchManipulationConflictMessage('');
    }

    function continueBatchManipulation($event) {
        $scope.setBatchManipulationConflictMessage('');
        prepareBatchPriceChange($event, true);
    }

    function prepareBatchPriceChange($event, skipConflictCheck = false) {
        $scope.setBatchManipulationInProgress(true);
        $scope.setBatchManipulationBatchMessage('');
        $scope.setBatchManipulationProcessMessage('');

        if ($scope.batchManipulation.filter.PriceMatrix.PriceMatrix === true) {
            checkForPriceMatrixConflicts().then((resolve) => {
                $scope.setBatchManipulationProcessMessage('');

                if (resolve.conflicts.length > 0 && false === skipConflictCheck) {
                    $scope.setBatchManipulationProcessMessage('');

                    // create conflicts list
                    let priceMatrixConflictsMessage = '<br />Betroffene Preismatrizen:<br /><ul>';
                    for (let i = 0; i < resolve.conflicts.length; i++) {
                        priceMatrixConflictsMessage += '<li>' + resolve.conflicts[i] + '</li>';
                    }
                    priceMatrixConflictsMessage += '</ul>';

                    // add conflict message
                    $scope.setBatchManipulationConflictMessage('Die Änderungen betreffen Preismatrizen, die auch bei nicht gefilterten Produkten Anwendung finden!' + priceMatrixConflictsMessage + 'Möchten Sie fortfahren?');
                } else {
                    // get pages
                    const productPages = paginateIds(resolve.productIds);
                    const priceMatrixIdPages = paginateIds(resolve.priceMatrixIds, 10);

                    // set filter
                    let productFilter = angular.copy($scope.batchManipulation.filter);
                    productFilter.PriceMatrix.PriceMatrix = false;
                    let priceMatrixFilter = {
                        'PriceMatrix': angular.copy($scope.batchManipulation.filter.PriceMatrix)
                    };

                    // apply product prices
                    applyBatchPriceChangePages(productPages, productFilter).then(() => {
                        // apply price matrix prices
                        applyBatchPriceChangePages(priceMatrixIdPages, priceMatrixFilter, 0, 'BatchManipulationFindPricesByPriceMatrixIds', 'Preismatrix: ').then(() => {
                            $scope.setBatchManipulationInProgress(false);
                        });
                    });
                }
            });
        } else {
            // only product batch manipulation
            fetchProductIdsByFilter().then((response) => {
                const productIdPages = paginateIds(response.data.result);
                applyBatchPriceChangePages(productIdPages, $scope.batchManipulation.filter).then(() => {
                    $scope.setBatchManipulationInProgress(false);
                });
            });
        }
    }

    function checkForPriceMatrixConflicts() {
        $scope.setBatchManipulationProcessMessage('Überprüfe auf Preismatrix Konflikte: 0%');
        return Promise.all([fetchProductIdsByFilter(), fetchAllProductIds()]).then((productIdValues) => {
            $scope.setBatchManipulationProcessMessage('Überprüfe auf Preismatrix Konflikte: 50%');
            const productIds = productIdValues[0].data.result;
            const otherProductIds = productIdValues[1].data.result.filter((productId) => {
                return !productIds.includes(productId);
            });

            return Promise.all([
                fetchPriceMatrixIdsByProductIds(productIds),
                fetchPriceMatrixIdsByProductIds(otherProductIds)
            ]).then((priceMatrixIdValues) => {
                $scope.setBatchManipulationProcessMessage('Überprüfe auf Preismatrix Konflikte: 100%');
                const priceMatrixIds = priceMatrixIdValues[0].data.result;
                const otherPriceMatrixIds = priceMatrixIdValues[1].data.result;
                let conflicts = [];

                for (let i = 0; i < priceMatrixIds.length; i++) {
                    if (otherPriceMatrixIds.includes(priceMatrixIds[i])) {
                        conflicts.push(priceMatrixIds[i]);
                    }
                }

                return Promise.resolve({
                    productIds: productIds,
                    priceMatrixIds: priceMatrixIds,
                    conflicts: conflicts
                });
            });
        });
    }

    function applyBatchPriceChangePages(pages, filter, page = 0, query = 'BatchManipulationFindPrices', messagePrefix = 'Produkte: ') {
        // all pages done
        if (page >= pages.length) {
            $scope.setBatchManipulationProcessMessage('');
            return Promise.resolve();
        }

        // set current process message
        $scope.setBatchManipulationProcessMessage(messagePrefix + 'Preisanpassung in Bearbeitung: ' + Math.round(100 / pages.length * page) + '%');

        // do price manipulation for current page
        if ($scope.batchManipulation.useFormula) {

            // do price manipulation by formula
            return $scope.setBatchPricesByFormula(pages[page], $scope.batchManipulation.formula, filter, query).then((response) => {
                if (page < pages.length) {
                    page++;
                    return applyBatchPriceChangePages(pages, filter, page, query, messagePrefix);
                }
            });
        } else {

            // do price manipulation by multiplier
            let multiplier = $scope.batchManipulation.multiplier / 100;
            if ($scope.batchManipulation.multiplier < 0) {
                multiplier = (100 + $scope.batchManipulation.multiplier) / 100
            }

            return $scope.setBatchPrices(pages[page], multiplier, filter, query).then((response) => {
                if (page < pages.length) {
                    page++;
                    return applyBatchPriceChangePages(pages, filter, page, query, messagePrefix);
                }
            });
        }
    }

    function fetchProductIdsByFilter() {
        return MessageBusFactory.query('FindProductIdsByFilter', [$scope.filter]);
    }

    function fetchAllProductIds() {
        return MessageBusFactory.query('FindProductIdsByFilter', [{
            searchString: '',
            categories: []
        }]);
    }

    function fetchPriceMatrixIdsByProductIds(productIds) {
        return MessageBusFactory.query('BatchManipulationFindPriceMatrixIdsByProductIds', [productIds]);
    }

    function paginateIds(productIds, pageSize = 100) {
        let pages = [];
        let page = -1;

        for (let i = 0; i < productIds.length; i++) {
            if (i % pageSize === 0) {
                page++;
                pages[page] = []
            }
            pages[page].push(productIds[i]);
        }

        return pages;
    }

    function updateMultiplierHint() {
        $scope.batchManipulation.multiplierHint = '';
        let multiplier = parseFloat($scope.batchManipulation.multiplier);

        if (multiplier >= 100) {
            $scope.batchManipulation.multiplierHint = "Die Preise werden um " + Math.round((multiplier - 100) * 100) / 100 + " % erhöht.";
            return;
        }

        if (multiplier > 0 && multiplier < 100) {
            $scope.batchManipulation.multiplierHint = "Die Preise werden um " + Math.round(((multiplier - 100) * (-1)) * 100) / 100 + " % verringert";
            return;
        }

        if (multiplier === 0) {
            $scope.batchManipulation.multiplierHint = "Die Preise werden auf 0 gesetzt";
        }
        else {
            $scope.batchManipulation.multiplierHint = "Multiplikator entweder negativ oder falsches Format (Dezimalzahl größer 0 mit bis zu 2 Nachkommastellen)";
        }
    }

    $scope.setFilter = setFilter;
    $scope.resetFilter = resetFilter;
    $scope.prepareBatchPriceChange = prepareBatchPriceChange;
    $scope.updateMultiplierHint = updateMultiplierHint;
    $scope.cancelBatchManipulation = cancelBatchManipulation;
    $scope.continueBatchManipulation = continueBatchManipulation;
    $scope.languageFactory = LanguageFactory;
    $scope.$on('$destroy', subscribedActions);
};

ProductController.$inject = ProductControllerInject;

export default ['ProductController', ProductController];