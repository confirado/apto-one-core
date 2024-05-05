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
        fetchConditionSets: ProductActions.fetchConditionSets,
        updateProductConditionSet: ProductActions.updateProductConditionSet,
        fetchSections: ConditionSetActions.fetchSections,
        fetchDetail: ConditionSetActions.fetchDetail,
        resetStore: ConditionSetActions.reset,
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
            //$scope.fetchConditions(conditionSetId);
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

    init();

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
