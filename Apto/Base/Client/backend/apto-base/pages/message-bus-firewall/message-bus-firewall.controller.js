import MessageBusFirewallListTemplate from './message-bus-firewall-list.html';
import MessageBusFirewallDetailTemplate from './message-bus-firewall-detail.html';
import MessageBusFirewallDetailController from './message-bus-firewall-detail.controller';

const MessageBusFirewallControllerInject = ['$scope', '$mdDialog', '$templateCache', '$ngRedux', 'AclIsGrantedFactory', 'MessageBusFirewallActions', 'IndexActions', 'MessageBusFactory'];
const MessageBusFirewallController = function($scope, $mdDialog, $templateCache, $ngRedux, AclIsGrantedFactory, MessageBusFirewallActions, IndexActions, MessageBusFactory) {
    $templateCache.put('base/pages/message-bus-firewall/message-bus-firewall-list.html', MessageBusFirewallListTemplate);

    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.messageBusFirewall.pageHeaderConfig,
            messageBusMessages: state.messageBusFirewall.messageBusMessages,
            aclMessagesRequired: state.messageBusFirewall.aclMessagesRequired,
            aclEntriesByClass: state.messageBusFirewall.aclEntriesByClass
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        messageBusMessagesFetch: MessageBusFirewallActions.messageBusMessagesFetch,
        toggleSidebarRight: IndexActions.toggleSidebarRight
    })($scope);

    function showDetails(messageName, className, $event) {
        var parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: MessageBusFirewallDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                messageName: messageName,
                className: className
            },
            controller: MessageBusFirewallDetailController
        });
    }

    function getUserRolesByClass(messageClass) {
        if ($scope.aclEntriesByClass[messageClass]) {
            return $scope.aclEntriesByClass[messageClass];
        }
        return '';
    }

    $scope.pageHeaderActions = {
        toggleSideBarRight: {
            fnc: function () {
                $scope.toggleSidebarRight();
            }
        }
    };

    $scope.commandFilter = '';
    $scope.queryFilter = '';
    $scope.messageBusMessagesFetch(true);
    $scope.allGranted = AclIsGrantedFactory.allGranted;
    $scope.showDetails = showDetails;
    $scope.getUserRolesByClass = getUserRolesByClass;
    $scope.$on('$destroy', subscribedActions);
};

MessageBusFirewallController.$inject = MessageBusFirewallControllerInject;

export default ['MessageBusFirewallController', MessageBusFirewallController];