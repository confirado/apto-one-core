import LanguageDetailsTemplate from './language-details.controller.html';
import LanguageDetailsController from './language-details.controller';

const LanguageControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'LanguageActions', 'IndexActions'];
const LanguageController = function($scope, $mdDialog, $ngRedux, LanguageActions, IndexActions) {
    $scope.mapStateToThis = function(state) {
        return {
            pageHeaderConfig: state.language.pageHeaderConfig,
            dataListConfig: state.language.dataListConfig,
            languages: state.language.languages
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        languagesFetch: LanguageActions.languagesFetch,
        setListTemplate: LanguageActions.setListTemplate,
        languageDetailFetch: LanguageActions.languageDetailFetch,
        setSelected: LanguageActions.setSelected,
        languageRemove: LanguageActions.languageRemove,
        toggleSidebarRight: IndexActions.toggleSidebarRight
    })($scope);

    $scope.languagesFetch(
        $scope.pageHeaderConfig.search.searchString
    );

    $scope.pageHeaderActions = {
        add: {
            fnc: showDetailsDialog
        },
        search: {
            fnc: $scope.languagesFetch
        },
        listStyle: {
            fnc: $scope.setListTemplate
        },
        toggleSideBarRight: {
            fnc: $scope.toggleSidebarRight
        }
    };

    $scope.dataListActions = {
        select: {
            fnc: $scope.setSelected
        },
        edit: {
            fnc: showDetailsDialog
        },
        remove: {
            fnc: function ($event, id) {
                $scope.languageRemove(id).then(function () {
                    $scope.languagesFetch(
                        $scope.pageHeaderConfig.search.searchString
                    );
                });
            }
        }
    };

    function showDetailsDialog($event, id) {
        const parentEl = angular.element(document.body);
        if(typeof id !== "undefined") {
            $scope.languageDetailFetch(id);
        }
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: LanguageDetailsTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showDetailsDialog,
                searchString: $scope.pageHeaderConfig.search.searchString
            },
            controller: LanguageDetailsController
        });
    }

    $scope.$on('$destroy', subscribedActions);
};

LanguageController.$inject = LanguageControllerInject;

export default ['LanguageController', LanguageController];