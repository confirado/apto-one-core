const MessageBusFirewallDetailControllerInject = ['$scope', '$mdDialog', 'messageName', 'className', '$ngRedux', 'UserRoleActions', 'MessageBusFirewallActions', 'IndexActions'];
const MessageBusFirewallDetailController = function($scope, $mdDialog, messageName, className, $ngRedux, UserRoleActions, MessageBusFirewallActions, IndexActions) {

    $scope.mapStateToThis = function(state) {
        return {
            userRoles: state.userRole.userRoles,
            aclEntries: state.messageBusFirewall.aclEntries
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        userRolesFetch: UserRoleActions.userRolesFetch,
        aclEntriesFetch: MessageBusFirewallActions.aclEntriesFetch,
        aclPermissionAdd: MessageBusFirewallActions.aclPermissionAdd,
        aclPermissionRemove: MessageBusFirewallActions.aclPermissionRemove,
        fetchAclEntriesByAclClass: MessageBusFirewallActions.fetchAclEntriesByAclClass,
        messageBusMessagesFetch: MessageBusFirewallActions.messageBusMessagesFetch,
        messagesGrantedFetch: IndexActions.messagesGrantedFetch,
    })($scope);

    $scope.selectedRecords = {};
    $scope.messageName = messageName;
    $scope.className = className;
    $scope.userRoleFilter = '';
    $scope.loadingProgress = true;
    $scope.userRolesFetch().then(function () {
        $scope.aclEntriesFetch($scope.className).then(function () {
            setSelectedRecords();
            $scope.loadingProgress = false;
        });
    });

    function setSelectedRecords() {
        for(let i = 0; i < $scope.userRoles.length; i++) {
            let roleIdentifier, aclEntry, roleId;

            roleIdentifier = $scope.userRoles[i].identifier;
            aclEntry = getAclEntryByRoleIdentifier(roleIdentifier);
            roleId = $scope.userRoles[i].id;

            if(aclEntry === null) {
                $scope.selectedRecords[roleId] = false;
            }
            else {
                $scope.selectedRecords[roleId] = true;
            }
        }
    }

    function getAclEntryByRoleIdentifier(identifier) {
        for (let i = 0; i < $scope.aclEntries.length; i++) {
            if ($scope.aclEntries[i].role == identifier && parseInt($scope.aclEntries[i].mask) > 15){
                return $scope.aclEntries[i];
            }
        }
        return null;
    }

    $scope.save = function () {
        var aclPermissionToHandle = $scope.userRoles.length;
        $scope.loadingProgress = true;
        for(let j = 0; j < $scope.userRoles.length; j++) {
            let roleId = $scope.userRoles[j].id;
            if(typeof $scope.selectedRecords[roleId] !== "undefined" && $scope.selectedRecords[roleId] === true) {
                $scope.aclPermissionAdd(
                    null, $scope.userRoles[j].identifier, $scope.className, null, null, ['execute']
                ).then(function () {
                    aclPermissionToHandle--;
                    broadcastTasksRemaining(aclPermissionToHandle);
                });
            }
            else {
                $scope.aclPermissionRemove(
                    null, $scope.userRoles[j].identifier, $scope.className, null, null, ['execute']
                ).then(function () {
                    aclPermissionToHandle--;
                    broadcastTasksRemaining(aclPermissionToHandle);
                });
            }
        }
    };

    function broadcastTasksRemaining(aclPermissionToHandle) {
        $scope.$broadcast('aclPermissionToHandle', {aclPermissionToHandle: aclPermissionToHandle});
    }

    $scope.$on('aclPermissionToHandle', function (event, data) {
        if(data.aclPermissionToHandle === 0) {
            $scope.loadingProgress = false;
            $scope.fetchAclEntriesByAclClass($scope.className);
            $scope.close();
        }
    });

    $scope.close = function () {
        $scope.messagesGrantedFetch();
        $scope.messageBusMessagesFetch();
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

MessageBusFirewallDetailController.$inject = MessageBusFirewallDetailControllerInject;

export default MessageBusFirewallDetailController;