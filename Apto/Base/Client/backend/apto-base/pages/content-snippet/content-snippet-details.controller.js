const ContentSnippetDetailsControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'ContentSnippetActions', 'CurrentUserFactory', 'targetEvent', 'showDetailsDialog', 'searchString', 'parentId'];
const ContentSnippetDetailsController = function($scope, $mdDialog, $ngRedux, ContentSnippetActions, CurrentUserFactory, targetEvent, showDetailsDialog, searchString, parentId) {
    $scope.mapStateToThis = function(state) {
        return {
            contentSnippetDetails: state.contentSnippet.contentSnippetDetails
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        contentSnippetTreeFetch: ContentSnippetActions.contentSnippetTreeFetch,
        contentSnippetDetailFetch: ContentSnippetActions.contentSnippetDetailFetch,
        contentSnippetDetailSave: ContentSnippetActions.contentSnippetDetailSave,
        contentSnippetDetailReset: ContentSnippetActions.contentSnippetDetailReset
    })((selectedState, actions) => {
        $scope.contentSnippetDetails = selectedState.contentSnippetDetails;
        $scope.contentSnippetTreeFetch = actions.contentSnippetTreeFetch;
        $scope.contentSnippetDetailFetch = actions.contentSnippetDetailFetch;
        $scope.contentSnippetDetailSave = actions.contentSnippetDetailSave;
        $scope.contentSnippetDetailReset = actions.contentSnippetDetailReset;

        // parentId is only set on add action, otherwise its null and we use the current parent from contentSnippetDetails
        if(null !== parentId) {
            $scope.contentSnippetDetails.parent = parentId;
        }
    });

    $scope.currentUserFactory = CurrentUserFactory;
    $scope.save = function (contentSnippetForm, close) {
        if(contentSnippetForm.$valid) {
            $scope.contentSnippetDetailSave($scope.contentSnippetDetails).then(function () {
                if (typeof close !== "undefined") {
                    $scope.close();
                } else if (!$scope.contentSnippetDetails.id){
                    $scope.contentSnippetDetailReset();
                    $scope.contentSnippetTreeFetch(
                        searchString
                    );
                    showDetailsDialog(targetEvent);
                }
            });
        }
    };

    $scope.close = function () {
        $scope.contentSnippetDetailReset();
        $scope.contentSnippetTreeFetch(
            searchString
        );
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

ContentSnippetDetailsController.$inject = ContentSnippetDetailsControllerInject;

export default ContentSnippetDetailsController;