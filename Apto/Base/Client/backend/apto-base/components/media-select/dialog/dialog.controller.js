import AddDirectoryTemplate from "../../../pages/media/media-add-directory.controller.html";
import AddDirectoryController from "../../../pages/media/media-add-directory.controller";

const MediaAddDirectoryControllerInject = ['$scope', '$mdDialog', 'AclIsGrantedFactory', 'targetEvent', 'onSelectMediaFile'];
const DialogController = function($scope, $mdDialog, AclIsGrantedFactory, targetEvent, onSelectMediaFile) {

    $scope.aclAllGranted = AclIsGrantedFactory.allGranted;
    $scope.aclMessagesRequiredAddDirectory = {
        commands: ['AddMediaFileDirectory'], queries: ['ListMediaFiles']
    };

    $scope.showAddDirectoryDialog = function($event) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: AddDirectoryTemplate,
            clickOutsideToClose: true,
            multiple: true,
            locals: {
                targetEvent: $event
            },
            controller: AddDirectoryController
        });
    };

    $scope.close = function () {
        $mdDialog.hide();
    };

    $scope.onSelectMediaFile = function(path) {
        onSelectMediaFile(path);
        $scope.close();
    };
};

DialogController.$inject = MediaAddDirectoryControllerInject;

export default DialogController;