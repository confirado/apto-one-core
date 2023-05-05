import GroupTab from './group-tab.html';
import PropertiesTab from './group-properties-tab.html';
import PropertyDetailsTemplate from './property-detail.controller.html';
import PropertyDetailsController from './property-detail.controller';

const GroupDetailControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'targetEvent', 'showDetailsDialog', 'groupId', 'LanguageFactory', 'MaterialPickerPropertyActions'];
const GroupDetailController = function($scope, $templateCache, $mdDialog, $ngRedux, targetEvent, showDetailsDialog, groupId, LanguageFactory, MaterialPickerPropertyActions) {
    $templateCache.put('plugins/material-picker/pages/property/group-tab.html', GroupTab);
    $templateCache.put('plugins/material-picker/pages/property/group-properties-tab.html', PropertiesTab);

    $scope.mapStateToThis = function(state) {
        return {
            group: state.pluginMaterialPickerProperty.group,
            properties: state.pluginMaterialPickerProperty.properties
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchGroup: MaterialPickerPropertyActions.fetchGroup,
        saveGroup: MaterialPickerPropertyActions.saveGroup,
        resetGroup: MaterialPickerPropertyActions.resetGroup,
        fetchGroupProperties: MaterialPickerPropertyActions.fetchGroupProperties,
        addGroupProperty: MaterialPickerPropertyActions.addGroupProperty,
        removeGroupProperty: MaterialPickerPropertyActions.removeGroupProperty,
        setGroupPropertyIsDefault: MaterialPickerPropertyActions.setGroupPropertyIsDefault
    })($scope);

    function init() {
        if (typeof groupId !== "undefined") {
            $scope.groupId = groupId;
            $scope.fetchGroup(groupId);
            $scope.fetchGroupProperties(groupId);
            initNewProperty();
        }
        $scope.translate = LanguageFactory.translate;
    }

    function initNewProperty() {
        $scope.newProperty = {
            name: {}
        };
    }

    function newPropertyNameValid() {
        for (let language in $scope.newProperty.name) {
            if (!$scope.newProperty.name.hasOwnProperty(language)) {
                continue;
            }

            if ($scope.newProperty.name[language]) {
                return true;
            }
        }
        return false;
    }

    function addProperty() {
        $scope.addGroupProperty(groupId, $scope.newProperty.name).then(() => {
            $scope.fetchGroupProperties(groupId);
        });
        initNewProperty();
    }

    function removeProperty(propertyId) {
        $scope.removeGroupProperty(groupId, propertyId).then(() => {
            $scope.fetchGroupProperties(groupId);
        });
    }

    function setPropertyIsDefault(propertyId, propertyIsDefault) {
        $scope.setGroupPropertyIsDefault(groupId, propertyId, propertyIsDefault).then(() => {
            $scope.fetchGroupProperties(groupId);
        });
    }

    function save(groupForm, closeForm) {
        if(groupForm.$valid) {
            $scope.saveGroup($scope.group).then(() => {
                if (typeof closeForm !== "undefined") {
                    close();
                } else if(typeof $scope.group.id === "undefined") {
                    $scope.resetGroup();
                    showDetailsDialog(targetEvent);
                }
                $scope.fetchGroupProperties(groupId);
            });
        }
    }

    function showPropertyDetails($event, id) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: PropertyDetailsTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            multiple: true,
            locals: {
                targetEvent: $event,
                showDetailsDialog: showPropertyDetails,
                propertyId: id
            },
            controller: PropertyDetailsController
        }).then(() => {
            $scope.fetchGroupProperties(groupId);
        }, () => {
            $scope.fetchGroupProperties(groupId);
        });
    }

    function close() {
        $scope.resetGroup();
        $mdDialog.cancel();
    }

    init();

    $scope.save = save;
    $scope.close = close;
    $scope.newPropertyNameValid = newPropertyNameValid;
    $scope.addProperty = addProperty;
    $scope.removeProperty = removeProperty;
    $scope.setPropertyIsDefault = setPropertyIsDefault;
    $scope.showPropertyDetails = showPropertyDetails;
    $scope.$on('$destroy', subscribedActions);
};

GroupDetailController.$inject = GroupDetailControllerInject;

export default GroupDetailController;