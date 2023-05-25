import {Promise} from "es6-promise";
import ImageTab from './tabs/image-settings.html';
import MotiveTab from './tabs/motive-settings.html';
import TextTab from './tabs/text-settings.html';
import AreaTab from './tabs/area-settings.html';
import PriceTab from './tabs/price-settings.html';

const ControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'targetEvent', 'showDetailsDialog', 'canvasId', 'ImageUploadCanvasActions', 'ProductActions'];
const Controller = function($scope, $templateCache, $mdDialog, $ngRedux, targetEvent, showDetailsDialog, canvasId, ImageUploadCanvasActions, ProductActions) {
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

        if (typeof canvasId !== "undefined") {
            $scope.fetchCanvas(canvasId);
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

    function onSelectPreviewImage(path) {
        $scope.detail.areaSettings.image = path;
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
    $scope.priceTypes = ['Bild', 'Text']
    $scope.save = save;
    $scope.close = close;
    $scope.onSelectPreviewImage = onSelectPreviewImage;
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
    $scope.addSurchargePrice = addSurchargePrice;
    $scope.removePrice = removePrice;

    $scope.$on('$destroy', subscribedActions);

    init();
};

Controller.$inject = ControllerInject;

export default Controller;
