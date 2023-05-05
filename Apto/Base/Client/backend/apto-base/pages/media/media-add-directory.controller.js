const MediaAddDirectoryControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'MediaActions', 'targetEvent'];
const MediaAddDirectoryController = function($scope, $mdDialog, $ngRedux, MediaActions, targetEvent) {
    $scope.mapStateToThis = function(state) {
        return {
            addDirectoryDetails: state.media.addDirectoryDetails,
            currentDirectory: state.media.currentDirectory
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchMediaFiles: MediaActions.fetchMediaFiles,
        addDirectoryDetailSave: MediaActions.addDirectoryDetailSave,
        addDirectoryDetailReset: MediaActions.addDirectoryDetailReset
    })($scope);

    $scope.save = function (mediaAddDirectoryForm) {
        if(mediaAddDirectoryForm.$valid) {
            $scope.addDirectoryDetailSave(
                $scope.currentDirectory,
                $scope.addDirectoryDetails.name
            ).then(function () {
                $scope.close();
                $scope.addDirectoryDetailReset();
                $scope.fetchMediaFiles(
                    $scope.currentDirectory
                );
            });
        }
    };

    $scope.close = function () {
        $scope.addDirectoryDetailReset();
        $scope.fetchMediaFiles(
            $scope.currentDirectory
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

MediaAddDirectoryController.$inject = MediaAddDirectoryControllerInject;

export default MediaAddDirectoryController;