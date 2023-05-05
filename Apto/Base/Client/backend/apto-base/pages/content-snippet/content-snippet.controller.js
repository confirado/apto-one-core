import ContentSnippetDetailsTemplate from "./content-snippet-details.controller.html";
import ContentSnippetDetailsController from "./content-snippet-details.controller";

const ContentSnippetControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'IndexActions', 'ContentSnippetActions'];
const ContentSnippetController = function($scope, $mdDialog, $ngRedux, IndexActions, ContentSnippetActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.contentSnippet.pageHeaderConfig,
            contentSnippetTree: state.contentSnippet.contentSnippetTree,
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        contentSnippetTreeFetch: ContentSnippetActions.contentSnippetTreeFetch,
        contentSnippetDetailFetch: ContentSnippetActions.contentSnippetDetailFetch,
        contentSnippetRemove: ContentSnippetActions.contentSnippetRemove,
    })($scope);

    $scope.contentSnippetTreeFetch(
        $scope.pageHeaderConfig.search.searchString
    );

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        toggleSideBarRight: {
            fnc: $scope.toggleSidebarRight
        }
    };
    $scope.contentSnippetTreeActions = {
        edit: {
            fnc: showDetailsDialog
        },
        addBelow: {
            fnc: addBelow
        },
        remove: {
            fnc: function ($event, id) {
                $event.stopPropagation();
                $scope.contentSnippetRemove(id).then(function () {
                    $scope.contentSnippetTreeFetch(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    function addBelow($event, parentId) {
        $event.stopPropagation();

        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: ContentSnippetDetailsTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString,
                parentId: parentId
            },
            controller: ContentSnippetDetailsController
        });
    }
    function showDetailsDialog($event , id) {
        $event.stopPropagation();

        const parentEl = angular.element(document.body);
        if(typeof id !== "undefined") {
            $scope.contentSnippetDetailFetch(id);
        }
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: ContentSnippetDetailsTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString,
                parentId: null
            },
            controller: ContentSnippetDetailsController
        });

    }

    $scope.$on('$destroy', subscribedActions);
};

ContentSnippetController.$inject = ContentSnippetControllerInject;

export default ['ContentSnippetController', ContentSnippetController];