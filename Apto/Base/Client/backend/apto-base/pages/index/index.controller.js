import MessageLogDetailTemplate from './message-log-detail.controller.html';
import MessageLogDetailController from './message-log-detail.controller';

const AptoIndexControllerInject = ['$scope', '$mdSidenav', '$mdMedia', '$location', '$route', 'CurrentUserFactory', 'AclIsGrantedFactory', 'LanguageActions', '$ngRedux', '$mdDialog', 'IndexActions', 'MessageBusActions'];
const AptoIndexController = function($scope, $mdSidenav, $mdMedia, $location, $route, CurrentUserFactory, AclIsGrantedFactory, LanguageActions, $ngRedux, $mdDialog, IndexActions, MessageBusActions) {
    var self = this;

    self.mapStateToThis = function(state) {
        return {
            lockSidebarRight: state.index.lockSidebarRight,
            messageLog: state.index.messageLog
        }
    };

    const subscribedActions = $ngRedux.connect(self.mapStateToThis, {
        languagesFetch: IndexActions.languagesFetch,
        clearMessageLog: MessageBusActions.clearMessageLog
    })(self);

    self.languagesFetch();

    function showMessageLogDetail(message, $event) {
        if(message.error === false) {
            return;
        }

        let parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: MessageLogDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                message: message
            },
            controller: MessageLogDetailController
        });
    }

    function toggleSidebar(sidebar) {
        if(typeof sidebar === "undefined") {
            sidebar = 'left';
        }
        $mdSidenav(sidebar).toggle();
    }

    function toggleSidebarSmall() {
        if(self.sidebarClass == '') {
            self.sidebarClass = 'apto-sidebar-small'
        }
        else {
            self.sidebarClass = '';
        }
    }

    function getSidebarClass(mediaMatch) {
        if(!$mdMedia(mediaMatch)) {
            self.sidebarClass = '';
        }
        return self.sidebarClass;
    }

    function navigateToRoute(route) {
        if($location.path() == route) {
            $route.reload();
        }
        else {
            $location.path(route);
        }
    }

    self.showMessageLogDetail = showMessageLogDetail;

    self.allGranted = AclIsGrantedFactory.allGranted;
    self.oneGranted = AclIsGrantedFactory.oneGranted;
    self.showGrantedInfo = AclIsGrantedFactory.showGrantedInfo;
    self.sidebarClass = '';

    self.toggleSidebar = toggleSidebar;
    self.toggleSidebarSmall = toggleSidebarSmall;
    self.getSidebarClass = getSidebarClass;

    self.navigateToRoute = navigateToRoute;

    self.currentUserFactory = CurrentUserFactory;

    $scope.$on('$destroy', subscribedActions);
};

AptoIndexController.$inject = AptoIndexControllerInject;

export default ['AptoIndexController', AptoIndexController];