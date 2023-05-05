const MediaAddDirectoryControllerInject = ['$scope', '$mdDialog', 'AclIsGrantedFactory', 'targetEvent', 'folder', 'onRenameFolder'];
const RenameDialogController = function($scope, $mdDialog, AclIsGrantedFactory, targetEvent, folder, onRenameFolder) {

    $scope.aclAllGranted = AclIsGrantedFactory.allGranted;
    $scope.aclMessagesRequiredAddDirectory = {
        commands: ['RenameMediaFileDirectory'], queries: []
    };

    $scope.newName = angular.copy(folder.name);

    $scope.close = function () {
        $mdDialog.hide();
    };

    $scope.save = function () {
        onRenameFolder(folder, $scope.newName);
        $mdDialog.hide();
    }

};

RenameDialogController.$inject = MediaAddDirectoryControllerInject;

export default RenameDialogController;