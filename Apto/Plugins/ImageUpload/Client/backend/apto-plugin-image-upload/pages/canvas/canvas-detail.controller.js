import {Promise} from "es6-promise";
import ImageTab from './tabs/image-settings.html';
import MotiveTab from './tabs/motive-settings.html';
import TextTab from './tabs/text-settings.html';
import AreaTab from './tabs/area-settings.html';
import PriceTab from './tabs/price-settings.html';
import angular from 'angular';

const ControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'targetEvent', 'showDetailsDialog', 'canvasId', 'ImageUploadCanvasActions', 'ProductActions', 'SanitizerFactory'];
const Controller = function($scope, $templateCache, $mdDialog, $ngRedux, targetEvent, showDetailsDialog, canvasId, ImageUploadCanvasActions, ProductActions, SanitizerFactory) {
    $templateCache.put('plugins/image-upload/pages/canvas/tabs/image-settings.html', ImageTab);
    $templateCache.put('plugins/image-upload/pages/canvas/tabs/motive-settings.html', MotiveTab);
    $templateCache.put('plugins/image-upload/pages/canvas/tabs/text-settings.html', TextTab);
    $templateCache.put('plugins/image-upload/pages/canvas/tabs/area-settings.html', AreaTab);
    $templateCache.put('plugins/image-upload/pages/canvas/tabs/price-settings.html', PriceTab);

    $scope.mapStateToThis = function(state) {
        return {
            detail: state.pluginImageUploadCanvas.detail,
            availableCustomerGroups: state.product.availableCustomerGroups
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchCanvas: ImageUploadCanvasActions.fetchCanvas,
        saveCanvas: ImageUploadCanvasActions.saveCanvas,
        resetCanvas: ImageUploadCanvasActions.resetCanvas,
        availableCustomerGroupsFetch: ProductActions.availableCustomerGroupsFetch
    })($scope);

    function init() {
        $scope.availableCustomerGroupsFetch();
        initNewAllowedFileType();
        initNewFont();
        initNewPrice();
        initNewArea();
        initNewTextBox();

        if (typeof canvasId !== "undefined") {
            $scope.fetchCanvas(canvasId).then(() => {
                if (!$scope.detail.textSettings.boxes) {
                    $scope.detail.textSettings.boxes = [];
                } else {
                    $scope.detail.textSettings = {
                        active: $scope.detail.textSettings.active,
                        boxes: $scope.detail.textSettings.boxes
                    }
                }
            });
        }
    }

    function initNewAllowedFileType() {
        $scope.newAllowedFileType = {
            value: ''
        };
    }

    function initNewFont() {
        $scope.newFont = {
            threeD: false,
            file: null,
            name: '',
            isActive: true,
            isDefault: false
        };
    }

    function initNewPrice() {
        $scope.newPrice = {
            amount: 0,
            currencyCode: 'EUR',
            customerGroupId: null,
            externalId: null,
            type: 'Bild'
        };
    }

    function initNewArea() {
        $scope.newArea = {
            name: '',
            identifier: '',
            width: 0,
            height: 0,
            left: 0,
            top: 0,
            perspective: 'persp1',
            layer: '0'
        }
    }

    function initNewTextBox() {
        $scope.newTextBox = {
            area: null,
            name: '',
            identifier: '',
            default: 'Mein Text!',
            fontSize: 25,
            textAlign: 'center',
            fill: '#ffffff',
            multiline: false,
            left: 0,
            top: 0,
            radius: 0,
            locked: true
        }
    }

    function save(detailForm, closeForm) {
        if(detailForm.$valid) {
            $scope.saveCanvas($scope.detail).then(() => {
                if (typeof closeForm !== "undefined") {
                    close();
                } else if(typeof $scope.detail.id === "undefined") {
                    $scope.resetCanvas();
                    showDetailsDialog(targetEvent);
                }
            });
        }
    }

    function close() {
        $scope.resetCanvas();
        $mdDialog.cancel();
    }

    function onSelectNewFont(path) {
        $scope.newFont.file = path;
    }

    function addAllowedFileTypeValue() {
        $scope.detail.imageSettings.allowedFileTypes.push($scope.newAllowedFileType.value);
        initNewAllowedFileType();
    }

    function removeAllowedFileTypeValue(index) {
        $scope.detail.imageSettings.allowedFileTypes.splice(index, 1);
    }

    function allowedFileTypeIsDuplicate(fileType) {
        const allowedFileTypes = $scope.detail.imageSettings.allowedFileTypes;
        for (let i = 0; i < allowedFileTypes.length; i++) {
            if (fileType === allowedFileTypes[i]) {
                return true;
            }
        }

        return false;
    }

    function addNewFont() {
        // set first font to be added as default
        if ($scope.detail.textSettings.fonts.length === 0) {
            $scope.newFont.isDefault = true;
        }
        $scope.detail.textSettings.fonts.push($scope.newFont);
        initNewFont();
    }

    function newFontIsDuplicate() {
        for (let i = 0; i < $scope.detail.textSettings.fonts.length; i++) {
            if ($scope.newFont.name === $scope.detail.textSettings.fonts[i].name) {
                return true;
            }
        }
        return false;
    }

    function newFontFileTypeIsAllowed() {
        return ($scope.allowedFontFileExtensions.indexOf($scope.newFont.file.split('.').pop()) > -1);
    }

    function setFontIsActive(index, isActive) {
        if (!isActive && $scope.detail.textSettings.fonts[index].isDefault) {
            // cannot deactivate Default
            $scope.detail.textSettings.fonts[index].isActive = true;
            return;
        }
        $scope.detail.textSettings.fonts[index].isActive = isActive;
    }

    function setDefaultFont(index) {
        for (let i = 0; i < $scope.detail.textSettings.fonts.length; i++) {
            if ($scope.detail.textSettings.fonts[i].isDefault && index !== i) {
                $scope.detail.textSettings.fonts[i].isDefault = false;
            }
        }
        $scope.detail.textSettings.fonts[index].isDefault = true;
    }

    function setFontThreeD(index, threeD) {
        $scope.detail.textSettings.fonts[index].threeD = threeD;
    }

    function removeFont(index) {
        let defaultToBeDeleted = false;
        if ($scope.detail.textSettings.fonts[index].isDefault) {
            defaultToBeDeleted = true;
        }
        $scope.detail.textSettings.fonts.splice(index, 1);

        // if default gets delete and is not last font => set new Default
        if (defaultToBeDeleted && $scope.detail.textSettings.fonts.length !== 0) {
            $scope.detail.textSettings.fonts[0].isDefault = true;
        }
    }

    function addNewArea() {
        if (!$scope.newArea.identifier) {
            $scope.newArea.identifier = SanitizerFactory.sanitizeIdentifier($scope.newArea.name);
        }

        if ($scope.detail.areaSettings.indexOf($scope.newArea) === -1) {
            $scope.detail.areaSettings.push($scope.newArea);
        }

        initNewArea();
        $scope.newAreaEditMode = false;
    }

    function editArea(index) {
        $scope.newAreaEditMode = true;
        $scope.newArea = $scope.detail.areaSettings[index];
    }

    function cancelEditArea() {
        initNewArea();
        $scope.newAreaEditMode = false;
    }

    function removeArea(index) {
        $scope.detail.areaSettings.splice(index, 1);
    }

    function areaIdentifierExists() {
        for (let i = 0; i < $scope.detail.areaSettings.length; i++) {
            // skip currently processed area
            if ($scope.detail.areaSettings.indexOf($scope.newArea) === i) {
                continue;
            }

            let identifier = $scope.newArea.identifier;
            if (!identifier) {
                identifier = SanitizerFactory.sanitizeIdentifier($scope.newArea.name);
            }

            if ($scope.detail.areaSettings[i].identifier === identifier) {
                return true;
            }
        }
        return false;
    }

    function addNewTextBox() {
        if (!$scope.newTextBox.identifier) {
            $scope.newTextBox.identifier = SanitizerFactory.sanitizeIdentifier($scope.newTextBox.name);
        }

        if ($scope.detail.textSettings.boxes.indexOf($scope.newTextBox) === -1) {
            $scope.detail.textSettings.boxes.push($scope.newTextBox);
        }

        initNewTextBox();
        $scope.newTextBoxEditMode = false;
    }

    function editTextBox(index) {
        $scope.newTextBoxEditMode = true;
        $scope.newTextBox = $scope.detail.textSettings.boxes[index];
    }

    function cancelEditTextBox() {
        initNewTextBox();
        $scope.newTextBoxEditMode = false;
    }

    function removeTextBox(index) {
        $scope.detail.textSettings.boxes.splice(index, 1);
    }

    function textBoxIdentifierExists() {
        for (let i = 0; i < $scope.detail.textSettings.boxes.length; i++) {
            // skip currently processed area
            if ($scope.detail.textSettings.boxes.indexOf($scope.newTextBox) === i) {
                continue;
            }

            let identifier = $scope.newTextBox.identifier;
            if (!identifier) {
                identifier = SanitizerFactory.sanitizeIdentifier($scope.newTextBox.name);
            }

            if ($scope.detail.textSettings.boxes[i].identifier === identifier) {
                return true;
            }
        }
        return false;
    }

    function addSurchargePrice() {
        if (priceExist($scope.newPrice)) {
            return;
        }

        $scope.newPrice.externalId = $scope.availableCustomerGroups.find(x => x.id === $scope.newPrice.customerGroupId).externalId;
        $scope.detail.priceSettings.surchargePrices.push($scope.newPrice);

        initNewPrice();
    }

    function removePrice(index) {
        $scope.detail.priceSettings.surchargePrices.splice(index, 1);
    }

    function priceExist(newPrice) {
        for (let i = 0; i < $scope.detail.priceSettings.surchargePrices.length; i++) {
            if (
                newPrice.currencyCode === $scope.detail.priceSettings.surchargePrices[i].currencyCode &&
                newPrice.customerGroupId === $scope.detail.priceSettings.surchargePrices[i].customerGroupId &&
                newPrice.type === $scope.detail.priceSettings.surchargePrices[i].type
            ) {
                return true;
            }
        }
        return false;
    }

    $scope.allowedFontFileExtensions = ['ttf', 'otf', 'woff', 'woff2', 'svg', 'eot'];
    $scope.priceTypes = ['Bild', 'Text'];
    $scope.newAreaEditMode = false;
    $scope.newTextBoxEditMode = false;
    $scope.save = save;
    $scope.close = close;
    $scope.onSelectNewFont = onSelectNewFont;
    $scope.addAllowedFileTypeValue = addAllowedFileTypeValue;
    $scope.removeAllowedFileTypeValue = removeAllowedFileTypeValue;
    $scope.allowedFileTypeIsDuplicate = allowedFileTypeIsDuplicate;
    $scope.addNewFont = addNewFont;
    $scope.newFontIsDuplicate = newFontIsDuplicate;
    $scope.newFontFileTypeIsAllowed = newFontFileTypeIsAllowed;
    $scope.setFontIsActive = setFontIsActive;
    $scope.setDefaultFont = setDefaultFont;
    $scope.setFontThreeD = setFontThreeD;
    $scope.removeFont = removeFont;
    $scope.addNewArea = addNewArea;
    $scope.editArea = editArea;
    $scope.removeArea = removeArea;
    $scope.cancelEditArea = cancelEditArea;
    $scope.areaIdentifierExists = areaIdentifierExists;
    $scope.addNewTextBox = addNewTextBox;
    $scope.editTextBox = editTextBox;
    $scope.removeTextBox = removeTextBox;
    $scope.cancelEditTextBox = cancelEditTextBox;
    $scope.textBoxIdentifierExists = textBoxIdentifierExists;
    $scope.addSurchargePrice = addSurchargePrice;
    $scope.removePrice = removePrice;

    $scope.$on('$destroy', subscribedActions);

    init();
};

Controller.$inject = ControllerInject;

export default Controller;
