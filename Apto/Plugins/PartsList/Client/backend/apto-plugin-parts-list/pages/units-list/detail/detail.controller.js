import UnitTab from './tabs/unit.html';

const ControllerInject = ['$scope', '$mdDialog', '$ngRedux', '$templateCache', 'LanguageFactory', 'AptoPartsListUnitActions', 'id', 'onClose'];
const Controller = function($scope, $mdDialog, $ngRedux, $templateCache, LanguageFactory, AptoPartsListUnitActions, id, onClose) {
    $templateCache.put('apto-plugin-parts-list/pages/units-list/detail/tabs/unit.html', UnitTab);

    $scope.mapStateToThis = function(state) {
        return {
            details: state.aptoPartsListUnit.details,
            availableProducts: state.aptoPartsListUnit.availableProducts,
            availableSections: state.aptoPartsListUnit.availableSections,
            availableElements: state.aptoPartsListUnit.availableElements
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchDetails: AptoPartsListUnitActions.fetchDetails,
        saveDetails: AptoPartsListUnitActions.saveDetails,
        resetDetails: AptoPartsListUnitActions.resetDetails
    })($scope);

    function init() {
        if (typeof id !== 'undefined') {
            $scope.fetchDetails(id);
        }
    }

    function save(detailsForm, close) {
        if (detailsForm.$valid) {
            $scope.saveDetails($scope.details).then(function () {
                if (typeof close !== 'undefined') {
                    $scope.close(false);
                } else if (typeof $scope.details.id === 'undefined') {
                    $scope.close(true);
                }
            });
        }
    }

    function close(reopen) {
        $mdDialog.cancel();
        $scope.resetDetails();
        if (typeof onClose === 'function') {
            onClose(reopen);
        }
    }

    init();

    $scope.translate = LanguageFactory.translate;
    $scope.save = save;
    $scope.close = close;

    $scope.$on('$destroy', subscribedActions);
};

Controller.$inject = ControllerInject;

export default Controller;