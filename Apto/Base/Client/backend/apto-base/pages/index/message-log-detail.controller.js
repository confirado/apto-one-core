const MessageLogDetailControllerInject = ['$scope', '$mdDialog', 'message'];
const MessageLogDetailController = function($scope, $mdDialog, message) {
    $scope.message = message;
    $scope.close = function () {
        $mdDialog.cancel();
    }
};

MessageLogDetailController.$inject = MessageLogDetailControllerInject;

export default MessageLogDetailController;