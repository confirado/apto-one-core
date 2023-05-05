const LanguageDetailsControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'LanguageActions', 'targetEvent', 'showDetailsDialog', 'searchString'];
const LanguageDetailsController = function($scope, $mdDialog, $ngRedux, LanguageActions, targetEvent, showDetailsDialog, searchString) {
    $scope.mapStateToThis = function(state) {
        return {
            languageDetails: state.language.languageDetails
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        languagesFetch: LanguageActions.languagesFetch,
        languageDetailFetch: LanguageActions.languageDetailFetch,
        languageDetailSave: LanguageActions.languageDetailSave,
        languageDetailReset: LanguageActions.languageDetailReset
    })($scope);

    $scope.save = function (languageForm, close) {
        if(languageForm.$valid) {
            $scope.languageDetailSave($scope.languageDetails).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if (!$scope.languageDetails.id){
                    $scope.languageDetailReset();
                    $scope.languagesFetch(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    $scope.close = function () {
        $scope.languageDetailReset();
        $scope.languagesFetch(
            searchString
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

LanguageDetailsController.$inject = LanguageDetailsControllerInject;

export default LanguageDetailsController;