const ComputedValueDetailControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'ProductActions', 'targetEvent', 'productId', 'computedValueId'];
const ComputedValueDetailController = function($scope, $templateCache, $mdDialog, $ngRedux, ProductActions, targetEvent, productId, computedValueId) {

    $scope.mapStateToThis = function(state) {
        return {
            detail: state.product.computedValueDetail,
            sections: state.product.sectionsElements
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchDetail: ProductActions.fetchComputedValueDetail,
        fetchSectionsElements: ProductActions.fetchSectionsElements,
        addComputedValueAlias: ProductActions.addComputedProductValueAlias,
        removeComputedValueAlias: ProductActions.removeComputedProductValueAlias,
        updateComputedProductValue: ProductActions.updateComputedProductValue,
        fetchComputedProductValues: ProductActions.fetchComputedProductValues
    })($scope);

    function init() {
        $scope.fetchDetail(computedValueId);
        $scope.fetchSectionsElements(productId);

        reset();
    }

    function reset() {
        $scope.selectableProperties = null;
        $scope.selectedSection = null;
        $scope.selectedElement = null;
        $scope.selectedProperty = '';
        $scope.newAlias = '';
        $scope.inheritPorpertiesLength = 0;
    }

    function save(close) {
        if (!$scope.detail.name && $scope.detail.name !== '0') {
            return;
        }

        $scope.updateComputedProductValue(
            productId,
            computedValueId,
            $scope.detail.name,
            $scope.detail.formula
        ).then(() => {
            if (typeof close !== "undefined") {
                $scope.close();
            } else {
                reload(true);
            }
        });
    }

    function onChangeSelectedSection() {
        $scope.selectedElement = null;
        $scope.selectedProperty = '';
        $scope.selectableProperties = null;
        $scope.inheritPorpertiesLength = 0;
        $scope.selectableProperties = getSelectedSectionSelectableCustomProperties();
    }

    function onChangeSelectedElement() {
        $scope.selectedProperty = '';
        $scope.inheritPorpertiesLength = 0;

        if ($scope.selectedElement === null) {
            $scope.selectableProperties = getSelectedSectionSelectableCustomProperties();
        } else {
            $scope.selectableProperties = getElementSelectableProperties();
        }
    }

    function isValidAlias() {
        return (
            $scope.newAlias &&
            $scope.newAlias !== 'e' &&
            $scope.newAlias !== 'i' &&
            $scope.newAlias !== 'p' &&
            (
                $scope.selectedSection && $scope.selectedProperty ||
                $scope.selectedSection && $scope.selectedElement
            )
        );
    }

    function onChangeSelectedProperty() {
    }

    function isCustomProperty() {
        if (!$scope.selectableProperties) {
            return false;
        }
        for (let i = 0; i < $scope.selectableProperties.length; i++) {
            if ($scope.selectedProperty === $scope.selectableProperties[i]) {
                if (i + 1 > $scope.inheritPorpertiesLength) {
                    return true;
                }
            }
        }
        return false;
    }

    function removeAlias(id) {
        $scope.removeComputedValueAlias(productId, computedValueId, id).then(() => {
            reload(false);
        })
    }

    function getElementSelectableProperties() {
        if ($scope.selectedElement === null) {
            return null;
        }

        const definitionClass = $scope.selectedElement.definition;
        const customProperties = $scope.selectedElement.customProperties

        if (!definitionClass.properties && customProperties.length < 1) {
            return null;
        }

        let properties = definitionClass.properties ? Object.keys(definitionClass.properties) : [];
        $scope.inheritPorpertiesLength = properties.length;

        for (let i = 0; i < customProperties.length; i++) {
            // only custom properties without condition are supported
            if (customProperties[i].productConditionId !== null) {
                continue;
            }
            properties.push(customProperties[i].key);
        }
        return properties;
    }

    function getSelectedSectionSelectableCustomProperties() {
        if ($scope.selectedSection === null) {
            return null;
        }

        let customProperties = [];

        for (let i = 0; i < $scope.selectedSection.elements.length; i++) {
            const element = $scope.selectedSection.elements[i];

            for(let j = 0; j < element.customProperties.length; j++) {
                let key = element.customProperties[j].key;

                // only custom properties without condition are supported
                if (customProperties.includes(key) || element.customProperties[j].productConditionId !== null) {
                    continue;
                }

                customProperties.push(key);
            }
        }

        return customProperties.length > 0 ? customProperties : null;
    }

    function getSectionIdentifier(sectionId) {
        if ($scope.sections.length === 0) {
            return null;
        }
        const section = getSection(sectionId);
        return section.identifier;
    }

    function getElementIdentifier(sectionId, elementId) {
        if ($scope.sections.length === 0) {
            return null;
        }
        if (null === elementId) {
            return null;
        }
        const element = getElement(sectionId, elementId);
        return element.identifier;
    }

    function getSection(sectionId) {
        for (let i = 0; i < $scope.sections.length; i++) {
            if ($scope.sections[i].id === sectionId) {
                return angular.copy($scope.sections[i]);
            }
        }
    }

    function getElement(sectionId, elementId) {
        const section = getSection(sectionId);
        for (let i = 0; i < section.elements.length; i++) {
            if (section.elements[i].id === elementId) {
                return angular.copy(section.elements[i]);
            }
        }
    }

    function addAlias() {
        $scope.addComputedValueAlias(
            productId,
            computedValueId,
            $scope.selectedSection.id,
            $scope.selectedElement ? $scope.selectedElement.id : null,
            $scope.newAlias,
            $scope.selectedProperty,
            isCustomProperty()
        ).then(() => {
            reload(true);
        });
    }

    function reload(resetInput) {
        $scope.fetchComputedProductValues(productId).then(() => {
            $scope.fetchDetail(computedValueId);
        });

        if (resetInput) {
            reset();
        }
    }

    init();

    $scope.onChangeSelectedSection = onChangeSelectedSection;
    $scope.onChangeSelectedElement = onChangeSelectedElement;
    $scope.onChangeSelectedProperty = onChangeSelectedProperty;
    $scope.getSectionIdentifier = getSectionIdentifier;
    $scope.getElementIdentifier = getElementIdentifier;
    $scope.isValidAlias = isValidAlias;
    $scope.addAlias = addAlias;
    $scope.removeAlias = removeAlias;
    $scope.save = save;

    $scope.close = function () {
        $mdDialog.cancel();
    };

    $scope.$on('$destroy', subscribedActions);
};

ComputedValueDetailController.$inject = ComputedValueDetailControllerInject;

export default ComputedValueDetailController;
