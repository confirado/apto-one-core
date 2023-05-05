import PropertyTab from './property-tab.html';
import CustomPropertiesTab from './property-custom-properties-tab.html';

const PropertyDetailControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'targetEvent', 'showDetailsDialog', 'propertyId', 'LanguageFactory', 'MaterialPickerPropertyActions'];
const PropertyDetailController = function($scope, $templateCache, $mdDialog, $ngRedux, targetEvent, showDetailsDialog, propertyId, LanguageFactory, MaterialPickerPropertyActions) {
    $templateCache.put('plugins/material-picker/pages/property/property-tab.html', PropertyTab);
    $templateCache.put('plugins/material-picker/pages/property/property-custom-properties-tab.html', CustomPropertiesTab);

    $scope.mapStateToThis = function(state) {
        return {
            property: state.pluginMaterialPickerProperty.property,
            propertyCustomProperties: state.pluginMaterialPickerProperty.propertyCustomProperties
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchProperty: MaterialPickerPropertyActions.fetchProperty,
        saveProperty: MaterialPickerPropertyActions.saveProperty,
        resetProperty: MaterialPickerPropertyActions.resetProperty,
        fetchPropertyCustomProperties: MaterialPickerPropertyActions.fetchPropertyCustomProperties,
        addPropertyCustomProperty: MaterialPickerPropertyActions.addPropertyCustomProperty,
        removePropertyCustomProperty: MaterialPickerPropertyActions.removePropertyCustomProperty
    })($scope);

    function init() {
        if (typeof propertyId !== "undefined") {
            $scope.propertyId = propertyId;
            $scope.fetchProperty(propertyId);
            $scope.fetchPropertyCustomProperties(propertyId);
            initNewProperty();
        }
        $scope.translate = LanguageFactory.translate;
    }

    function initNewProperty() {
        $scope.newProperty = {
            name: {}
        };
    }

    function addCustomProperty(key, value) {
        $scope.addPropertyCustomProperty(propertyId, key, value).then(() => {
            $scope.fetchPropertyCustomProperties(propertyId);
        });
        initNewProperty();
    }

    function removeCustomProperty(key) {
        $scope.removePropertyCustomProperty(propertyId, key).then(() => {
            $scope.fetchPropertyCustomProperties(propertyId);
        });
    }

    function save(propertyForm, closeForm) {
        if(propertyForm.$valid) {
            $scope.saveProperty($scope.property).then(() => {
                if (typeof closeForm !== "undefined") {
                    close();
                } else if(typeof $scope.property.id === "undefined") {
                    $scope.resetProperty();
                    showDetailsDialog(targetEvent);
                }
            });
        }
    }

    function close() {
        $scope.resetProperty();
        $mdDialog.cancel();
    }

    init();

    $scope.save = save;
    $scope.close = close;
    $scope.addCustomProperty = addCustomProperty;
    $scope.removeCustomProperty = removeCustomProperty;
    $scope.$on('$destroy', subscribedActions);
};

PropertyDetailController.$inject = PropertyDetailControllerInject;

export default PropertyDetailController;