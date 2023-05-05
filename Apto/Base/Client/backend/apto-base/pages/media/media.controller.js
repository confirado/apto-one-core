import AddDirectoryTemplate from './media-add-directory.controller.html';
import AddDirectoryController from './media-add-directory.controller';

const ControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'IndexActions'];
const Controller = function($scope, $mdDialog, $ngRedux, IndexActions) {

    function mapReduxProps(state) {
        return {
            pageHeaderConfig: state.media.pageHeaderConfig
        };
    }

    function mapReduxActions() {
        return {
            toggleSidebarRight: IndexActions.toggleSidebarRight
        };
    }

    function showAddDirectoryDialog($event) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: AddDirectoryTemplate,
            clickOutsideToClose: true,
            locals: {
                targetEvent: $event
            },
            controller: AddDirectoryController
        });
    }

    const reduxConnect = $ngRedux.connect(mapReduxProps, mapReduxActions())($scope);

    $scope.pageHeaderActions = {
        toggleSideBarRight: {
            fnc: $scope.toggleSidebarRight
        },
        add: {
            fnc: showAddDirectoryDialog
        }
    };

    $scope.$on('$destroy', reduxConnect);
};

Controller.$inject = ControllerInject;

export default ['MediaController', Controller];