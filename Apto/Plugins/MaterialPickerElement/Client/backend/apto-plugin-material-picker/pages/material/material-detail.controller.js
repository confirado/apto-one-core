import MaterialTab from './material-tab.html';
import PricesTab from './price-tab.html';
import GalleryImagesTab from './gallery-images-tab.html';
import PropertiesTab from './properties-tab.html';
import ColorRatingsTab from './color-ratings-tab.html';
import PoolsTab from './pools-tab.html';
import RenderImageTab from './render-images-tab.html';

const MaterialDetailControllerInject = ['$scope', '$templateCache', '$mdDialog', '$ngRedux', 'targetEvent', 'showDetailsDialog', 'materialId', 'LanguageFactory', 'MaterialPickerMaterialActions', 'MaterialPickerPoolActions'];
const MaterialDetailController = function($scope, $templateCache, $mdDialog, $ngRedux, targetEvent, showDetailsDialog, materialId, LanguageFactory, MaterialPickerMaterialActions, MaterialPickerPoolActions) {
    $templateCache.put('plugins/material-picker/pages/material/material-tab.html', MaterialTab);
    $templateCache.put('plugins/material-picker/pages/material/price-tab.html', PricesTab);
    $templateCache.put('plugins/material-picker/pages/material/gallery-images-tab.html', GalleryImagesTab);
    $templateCache.put('plugins/material-picker/pages/material/properties-tab.html', PropertiesTab);
    $templateCache.put('plugins/material-picker/pages/material/color-ratings-tab.html', ColorRatingsTab);
    $templateCache.put('plugins/material-picker/pages/material/pools-tab.html', PoolsTab);
    $templateCache.put('plugins/material-picker/pages/material/render-images-tab.html', RenderImageTab);

    $scope.mapStateToThis = function(state) {
        return {
            material: state.pluginMaterialPickerMaterial.material,
            galleryImages: state.pluginMaterialPickerMaterial.galleryImages,
            properties: state.pluginMaterialPickerMaterial.properties,
            notAssignedProperties: state.pluginMaterialPickerMaterial.notAssignedProperties,
            colorRatings: state.pluginMaterialPickerMaterial.colorRatings,
            poolItems: state.pluginMaterialPickerMaterial.poolItems,
            notAssignedPools: state.pluginMaterialPickerMaterial.notAssignedPools,
            priceGroups: state.pluginMaterialPickerPool.priceGroups,
            renderImages: state.pluginMaterialPickerMaterial.renderImages,
            pools: state.pluginMaterialPickerMaterial.pools,
            prices: state.pluginMaterialPickerMaterial.prices,
            availableCustomerGroups: state.pluginMaterialPickerMaterial.availableCustomerGroups
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchMaterial: MaterialPickerMaterialActions.fetchMaterial,
        fetchGalleryImages: MaterialPickerMaterialActions.fetchGalleryImages,
        fetchProperties: MaterialPickerMaterialActions.fetchProperties,
        fetchNotAssignedProperties: MaterialPickerMaterialActions.fetchNotAssignedProperties,
        fetchPoolItems: MaterialPickerMaterialActions.fetchPoolItems,
        fetchNotAssignedPools: MaterialPickerMaterialActions.fetchNotAssignedPools,
        saveMaterial: MaterialPickerMaterialActions.saveMaterial,
        resetMaterial: MaterialPickerMaterialActions.resetMaterial,
        addGalleryImage: MaterialPickerMaterialActions.addGalleryImage,
        removeGalleryImage: MaterialPickerMaterialActions.removeGalleryImage,
        addProperty: MaterialPickerMaterialActions.addProperty,
        removeProperty: MaterialPickerMaterialActions.removeProperty,
        fetchColorRatings: MaterialPickerMaterialActions.fetchColorRatings,
        addColorRating: MaterialPickerMaterialActions.addColorRating,
        removeColorRating: MaterialPickerMaterialActions.removeColorRating,
        fetchPriceGroups: MaterialPickerPoolActions.fetchPriceGroups,
        addPoolItem: MaterialPickerPoolActions.addPoolItem,
        removePoolItem: MaterialPickerPoolActions.removePoolItem,
        addMaterialRenderImage: MaterialPickerMaterialActions.addMaterialRenderImage,
        removeMaterialRenderImage: MaterialPickerMaterialActions.removeMaterialRenderImage,
        fetchMaterialRenderImages: MaterialPickerMaterialActions.fetchMaterialRenderImages,
        fetchPools: MaterialPickerMaterialActions.fetchPools,
        fetchPrices: MaterialPickerMaterialActions.fetchPrices,
        addPrice: MaterialPickerMaterialActions.addPrice,
        removePrice: MaterialPickerMaterialActions.removePrice,
    })($scope);

    function init() {
        if (typeof materialId !== "undefined") {
            $scope.materialId = materialId;
            $scope.fetchMaterial(materialId);
            $scope.fetchGalleryImages(materialId);
            $scope.fetchProperties(materialId);
            $scope.fetchNotAssignedProperties(materialId);
            $scope.fetchPoolItems(materialId);
            $scope.fetchNotAssignedPools(materialId);
            $scope.fetchColorRatings(materialId);
            $scope.fetchMaterialRenderImages(materialId);
            $scope.fetchPriceGroups();
            $scope.fetchPools();
            $scope.fetchPrices(materialId);
        }
        initNewPrice();
        initNewGalleryImage();
        initNewProperty();
        initNewColorRating();
        initNewPoolItem();
        initNewRenderImageItem();
        $scope.translate = LanguageFactory.translate;
        $scope.colors = [{
            name: 'Schwarz',
            hex: '#000000'
        }, {
            name: 'Rot',
            hex: '#ff0000'
        }, {
            name: 'Gelb',
            hex: '#ffff00'
        }, {
            name: 'Grün',
            hex: '#00ff00'
        }, {
            name: 'Blau',
            hex: '#0000ff'
        }, {
            name: 'Orange',
            hex: '#ffa500'
        }, {
            name: 'Weiß',
            hex: '#ffffff'
        }, {
            name: 'Grau',
            hex: '#888888'
        }, {
            name: 'Beige',
            hex: '#f5f5dc'
        }, {
            name: 'Braun',
            hex: '#b47d49'
        }, {
            name: 'Türkis',
            hex: '#3f888f'
        }, {
            name: 'Violett',
            hex: '#8800ff'
        }];
    }

    function initNewPrice() {
        $scope.newPrice = {
            amount: '',
            currencyCode: 'EUR',
            customerGroupId: ''
        };
    }

    function initNewGalleryImage() {
        $scope.newGalleryImage = {
            path: ''
        }
    }

    function initNewProperty() {
        $scope.newProperty = {
            property: null,
            propertySearch: ''
        };
    }

    function initNewColorRating() {
        $scope.newColorRating = {
            color: null,
            rating: null
        }
    }

    function initNewPoolItem() {
        $scope.newPoolItem = {
            pool: null,
            priceGroup: null,
            poolSearch: '',
            priceGroupSearch: ''
        }
    }

    function initNewRenderImageItem() {
        $scope.newRenderImage = {
            pool: null,
            poolSearch: '',
            file: '',
            layer: '',
            perspective: '',
            offsetX: 0,
            offsetY: 0
        }
    }

    function save(materialForm, closeForm) {
        if(materialForm.$valid) {
            $scope.saveMaterial($scope.material).then(() => {
                if (typeof closeForm !== "undefined") {
                    close();
                } else if(typeof $scope.material.id === "undefined") {
                    $scope.resetMaterial();
                    showDetailsDialog(targetEvent);
                }
            });
        }
    }

    function onAddPrice() {
        $scope.addPrice(materialId, $scope.newPrice.amount, $scope.newPrice.currencyCode, $scope.newPrice.customerGroupId).then(() => {
            initNewPrice();
            $scope.fetchPrices(materialId);
        });
    }

    function onRemovePrice(priceId) {
        $scope.removePrice(materialId, priceId).then(() => {
            $scope.fetchPrices(materialId);
        });
    }

    function onSelectPreviewImage(path) {
        $scope.material.previewImage.path = path;
    }

    function onSelectGalleryImage(path) {
        $scope.newGalleryImage.path = path;
    }

    function onAddGalleryImage() {
        $scope.addGalleryImage(materialId, $scope.newGalleryImage.path).then(() => {
            $scope.fetchGalleryImages(materialId);
        });
        initNewGalleryImage();
    }

    function onRemoveGalleryImage(galleryImageId) {
        $scope.removeGalleryImage(materialId, galleryImageId).then(() => {
            $scope.fetchGalleryImages(materialId);
        });
    }

    function onAddProperty() {
        $scope.addProperty(materialId, $scope.newProperty.property.id).then(() => {
            $scope.fetchProperties(materialId);
            $scope.fetchNotAssignedProperties(materialId);
            initNewProperty();
        });
    }

    function onRemoveProperty(propertyId) {
        $scope.removeProperty(materialId, propertyId).then(() => {
            $scope.fetchProperties(materialId);
            $scope.fetchNotAssignedProperties(materialId);
        });
    }

    function onAddColorRating() {
        $scope.addColorRating(materialId, $scope.newColorRating.color, $scope.newColorRating.rating).then(() => {
            $scope.fetchColorRatings(materialId);
        });
        initNewColorRating();
    }

    function onRemoveColorRating(colorRatingId) {
        $scope.removeColorRating(materialId, colorRatingId).then(() => {
            $scope.fetchColorRatings(materialId);
        });
    }

    function onAddPoolItem() {
        $scope.addPoolItem(
            $scope.newPoolItem.pool.id,
            materialId,
            $scope.newPoolItem.priceGroup.id
        ).then(()=>{
            $scope.newPoolItem.pool = null;
            $scope.newPoolItem.poolSearch = '';
            $scope.fetchPoolItems(materialId);
            $scope.fetchNotAssignedPools(materialId);
        });
    }

    function onRemovePoolItem(poolId, poolItemId) {
        $scope.removePoolItem(poolId, poolItemId).then(()=>{
            $scope.fetchPoolItems(materialId);
            $scope.fetchNotAssignedPools(materialId);
        });
    }

    function addRenderImage() {
        $scope.addMaterialRenderImage(
            materialId,
            $scope.newRenderImage.pool.id,
            $scope.newRenderImage.layer,
            $scope.newRenderImage.perspective,
            $scope.newRenderImage.file,
            $scope.newRenderImage.offsetX,
            $scope.newRenderImage.offsetY
        ).then(() => {
            initNewRenderImageItem();
            $scope.fetchMaterialRenderImages(materialId);
        });
    }

    function removeRenderImage(renderImageId) {
        $scope.removeMaterialRenderImage(materialId, renderImageId).then(() => {
            $scope.fetchMaterialRenderImages(materialId);
        });
    }

    function onSelectRenderImageFile(path) {
        $scope.newRenderImage.file = path;
    }

    function getColorName(hex) {
        for (let i = 0; i < $scope.colors.length; i++) {
            if (hex.toLowerCase() === $scope.colors[i].hex.toLowerCase()) {
                return $scope.colors[i].name;
            }
        }

        return hex;
    }

    function close() {
        $scope.resetMaterial();
        $mdDialog.cancel();
    }

    init();

    $scope.save = save;
    $scope.onAddPrice = onAddPrice;
    $scope.onRemovePrice = onRemovePrice;
    $scope.onSelectPreviewImage = onSelectPreviewImage;
    $scope.onSelectGalleryImage = onSelectGalleryImage;
    $scope.onAddGalleryImage = onAddGalleryImage;
    $scope.onRemoveGalleryImage = onRemoveGalleryImage;
    $scope.onAddProperty = onAddProperty;
    $scope.onRemoveProperty = onRemoveProperty;
    $scope.onAddColorRating = onAddColorRating;
    $scope.onRemoveColorRating = onRemoveColorRating;
    $scope.onAddPoolItem = onAddPoolItem;
    $scope.onRemovePoolItem = onRemovePoolItem;
    $scope.addRenderImage = addRenderImage;
    $scope.removeRenderImage = removeRenderImage;
    $scope.onSelectRenderImageFile = onSelectRenderImageFile;
    $scope.getColorName = getColorName;
    $scope.close = close;
    $scope.$on('$destroy', subscribedActions);
};

MaterialDetailController.$inject = MaterialDetailControllerInject;

export default MaterialDetailController;