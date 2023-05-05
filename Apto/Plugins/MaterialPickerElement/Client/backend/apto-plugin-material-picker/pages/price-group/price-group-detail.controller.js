import {Promise} from "es6-promise";
import PriceGroupTab from './price-group-tab.html';
import PriceMatrixTab from './price-matrix-tab.html';

const PriceGroupDetailControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'targetEvent', 'showDetailsDialog', 'priceGroupId', 'MaterialPickerPriceGroupActions', 'LanguageFactory'];
const PriceGroupDetailController = function($scope, $templateCache, $mdDialog, $ngRedux, targetEvent, showDetailsDialog, priceGroupId, MaterialPickerPriceGroupActions, LanguageFactory) {
    $templateCache.put('plugins/material-picker/pages/price-group/price-group-tab.html', PriceGroupTab);
    $templateCache.put('plugins/material-picker/pages/price-group/price-matrix-tab.html', PriceMatrixTab);

    $scope.mapStateToThis = function(state) {
        return {
            priceGroup: state.pluginMaterialPickerPriceGroup.priceGroup,
            priceMatrices: state.pluginMaterialPickerPriceGroup.priceMatrices
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchPriceGroup: MaterialPickerPriceGroupActions.fetchPriceGroup,
        savePriceGroup: MaterialPickerPriceGroupActions.savePriceGroup,
        resetPriceGroup: MaterialPickerPriceGroupActions.resetPriceGroup,
        fetchPriceMatrices: MaterialPickerPriceGroupActions.fetchPriceMatrices
    })($scope);

    function init() {
        $scope.translate = LanguageFactory.translate;
        $scope.priceMatrix = {
            selected: null,
            searchTerm: ''
        };

        // init price matrix
        const fetchPriceMatrices = $scope.fetchPriceMatrices();
        if (typeof priceGroupId !== "undefined") {
            $scope.priceGroupId = priceGroupId;
            const fetchPriceGroup = $scope.fetchPriceGroup(priceGroupId);

            Promise.all([fetchPriceMatrices, fetchPriceGroup]).then(() => {
                for (let iPriceMatrix = 0; iPriceMatrix < $scope.priceMatrices.length; iPriceMatrix++) {
                    if ($scope.priceGroup.priceMatrix.id === $scope.priceMatrices[iPriceMatrix].id) {
                        $scope.priceMatrix.selected = angular.copy($scope.priceMatrices[iPriceMatrix]);
                    }
                }
            });
        }
    }

    function onChangePriceMatrix(priceMatrix) {
        if (typeof priceMatrix !== "undefined") {
            $scope.priceGroup.priceMatrix.id = priceMatrix.id;
        }
    }

    function save(priceGroupForm, closeForm) {
        if(priceGroupForm.$valid) {
            $scope.savePriceGroup($scope.priceGroup).then(() => {
                if (typeof closeForm !== "undefined") {
                    close();
                } else if(typeof $scope.priceGroup.id === "undefined") {
                    $scope.resetPriceGroup();
                    showDetailsDialog(targetEvent);
                }
            });
        }
    }

    function close() {
        $scope.resetPriceGroup();
        $mdDialog.cancel();
    }

    init();

    $scope.onChangePriceMatrix = onChangePriceMatrix;
    $scope.save = save;
    $scope.close = close;
    $scope.$on('$destroy', subscribedActions);
};

PriceGroupDetailController.$inject = PriceGroupDetailControllerInject;

export default PriceGroupDetailController;
