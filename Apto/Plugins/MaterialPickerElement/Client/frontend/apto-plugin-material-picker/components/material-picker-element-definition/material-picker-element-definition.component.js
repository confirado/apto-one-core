import MaterialPickerTemplate from './material-picker-element-definition.component.html';
import PoolItemDetailsTemplate from './pool-item-details.html';
import Images from "./material-picker-images";
import SortOptions from "../../../../enums/sortOptions";

const MaterialPickerControllerInject = ['$location', '$ngRedux', 'ngDialog', 'LanguageFactory', 'ConfigurationService', 'MaterialPickerDefinitionActions', 'SnippetFactory', 'APTO_DIST_PATH_URL'];

class MaterialPickerController {
    static hasMaterialLightProperties(material) {
        return (null !== material.reflection || null !== material.transmission || null !== material.absorption);
    }

    static hasMaterialPropertyIcons(material) {
        return material.hasPropertyIcons;
    }

    static isFirstGalleryImageInHover(material) {
        return (
            !MaterialPickerController.hasMaterialLightProperties(material) &&
            !MaterialPickerController.hasMaterialPropertyIcons(material) &&
            material.galleryImages.length > 0
        );
    }

    static isPreviewImageInHover(material) {
        return (
            !MaterialPickerController.hasMaterialLightProperties(material) &&
            !MaterialPickerController.hasMaterialPropertyIcons(material) &&
            !MaterialPickerController.isFirstGalleryImageInHover(material)
        );
    }

    constructor($location, $ngRedux, ngDialog, LanguageFactory, ConfigurationService, MaterialPickerDefinitionActions, SnippetFactory, APTO_DIST_PATH_URL) {
        this.location = $location;
        this.reduxProps = {};
        this.reduxActions = {};
        this.filterColorRating = {
            hex: null
        };
        this.filterPriceGroup = null;
        this.filterPropertySingle = {};
        this.filterPropertyMultiple = {};

        this.searchTerm = '';
        this.sortOptions = SortOptions;
        this.orderBy = 'asc';

        this.steps = {
            primary: {
                materialId: '',
                materialName: '',
                priceGroup: '',
                materials: []
            },
            secondary: {
                materialId: '',
                materialName: '',
                priceGroup: '',
                materials: [],
                colorMixing: 'monochrome',
                colorArrangement: '',
                colorQuantity: ''
            },
            currentStep: 'primary',
            secondaryMaterialActive: false,
            allowMultiple: false,
            image: ''
        };

        this.ngRedux = $ngRedux;
        this.translate = LanguageFactory.translate;
        this.translateTrustAsHtml = LanguageFactory.translateTrustAsHtml;
        this.configurationService = ConfigurationService;
        this.materialPickerDefinitionActions = MaterialPickerDefinitionActions;
        this.elementIsSelected = this.configurationService.elementIsSelected;
        this.ngDialog = ngDialog;
        this.snippetFactory = SnippetFactory;
        this.hasMaterialLightProperties = MaterialPickerController.hasMaterialLightProperties;
        this.hasMaterialPropertyIcons = MaterialPickerController.hasMaterialPropertyIcons;
        this.isFirstGalleryImageInHover = MaterialPickerController.isFirstGalleryImageInHover;
        this.isPreviewImageInHover = MaterialPickerController.isPreviewImageInHover;
        this.initFilter();

        this.images = Images(APTO_DIST_PATH_URL);
    }

    initFilter() {
        this.filter = {
            colorRating: null,
            priceGroup: null,
            properties: [],
            searchString: this.searchTerm,
            orderBy: this.orderBy
        };
    }

    bindedItemOrderByName(name1, name2) {
        // @todo: Check why angular run this function with number
        if (name1.type === 'number' || name2.type === 'number') {
            return 0;
        }

        name1 = this.translate(name1.value);
        name2 = this.translate(name2.value);

        if (name1 < name2) {
            return -1;
        }
        if (name1 > name2) {
            return 1;
        }

        return 0;
    }

    buildFilter(onInit) {
        this.initFilter();

        // add color rating
        if (this.filterColorRating.hex) {
            this.filter.colorRating = this.filterColorRating.hex;
        }

        // add price group
        if (this.filterPriceGroup) {
            this.filter.priceGroup = this.filterPriceGroup;
        }

        // add properties from single filters
        for (let groupId in this.filterPropertySingle) {
            if (!this.filterPropertySingle.hasOwnProperty(groupId)) {
                continue;
            }

            if (this.filterPropertySingle[groupId]) {
                this.filter.properties.push(this.filterPropertySingle[groupId]);
            }
        }

        // add properties from multiple filters
        for (let propertyId in this.filterPropertyMultiple) {
            if (!this.filterPropertyMultiple.hasOwnProperty(propertyId)) {
                continue;
            }
            if (this.filterPropertyMultiple[propertyId]) {
                this.filter.properties.push(propertyId);
            }
        }

        if (onInit) {
            // add Defaults on Init
            for (let i = 0; i < this.reduxProps.propertyGroups.length; i++) {
                for (let n = 0; n < this.reduxProps.propertyGroups[i].properties.length; n++) {
                    if (this.reduxProps.propertyGroups[i].properties[n].isDefault === 1) {
                        let propertyId = this.reduxProps.propertyGroups[i].properties[n].id;
                        this.filter.properties.push(propertyId);
                        if (this.reduxProps.propertyGroups[i].allowMultiple) {
                            this.filterPropertyMultiple[propertyId] = true;
                        }
                        else {
                            this.filterPropertySingle[this.reduxProps.propertyGroups[i].id] = propertyId;
                        }
                    }
                }
            }
        }
    }

    mapReduxProps(state) {
        return {
            productId: state.product.productDetail.id,
            useStepByStep: state.product.productDetail.useStepByStep,
            poolItems: state.pluginMaterialPickerDefinition.poolItems,
            poolItemsPopular: state.pluginMaterialPickerDefinition.poolItemsPopular,
            priceGroups: state.pluginMaterialPickerDefinition.priceGroups,
            propertyGroups: state.pluginMaterialPickerDefinition.propertyGroups,
            numberOfMaterials: state.pluginMaterialPickerDefinition.numberOfMaterials,
            colors: state.pluginMaterialPickerDefinition.colors
        }
    }

    mapReduxActions() {
        return {
            fetchPoolItems: this.materialPickerDefinitionActions.fetchPoolItems,
            fetchPoolPriceGroups: this.materialPickerDefinitionActions.fetchPoolPriceGroups,
            fetchPoolPropertyGroups: this.materialPickerDefinitionActions.fetchPoolPropertyGroups,
            incrementMaterialClicks: this.materialPickerDefinitionActions.incrementMaterialClicks,
            fetchPoolColors: this.materialPickerDefinitionActions.fetchPoolColors
        }
    }

    reduxConnect() {
        return this.ngRedux.connect(
            this.mapReduxProps,
            this.mapReduxActions()
        )((selectedState, actions) => {
            this.reduxProps = selectedState;
            this.reduxActions = actions;
            this.onStateChange(selectedState);
        });
    }

    $onInit() {
        this.element = this.elementInput;

        if (typeof this.sectionInput !== "undefined") {
            this.section = this.sectionInput;
        } else {
            this.section = this.sectionCtrlInput;
        }

        // connect redux
        this.reduxDisconnect = this.reduxConnect();

        // set static values
        this.steps.secondaryMaterialActive = this.element.definition.staticValues.secondaryMaterialActive;
        this.steps.searchboxActive = this.element.definition.staticValues.searchboxActive;
        this.steps.allowMultiple = this.element.definition.staticValues.allowMultiple;
        this.altColorSelect = this.element.definition.staticValues.altColorSelect;
        this.colorSectionActive = this.element.definition.staticValues.colorSectionActive;
        this.priceGroupActive = this.element.definition.staticValues.priceGroupActive;
        this.sortByPosition = this.element.definition.staticValues.sortByPosition;
        this.showPriceGroupInMaterialName = this.element.definition.staticValues.showPriceGroupInMaterialName;
        this.sortOrderActive = this.element.definition.staticValues.sortOrderActive;

        // set sorByPosition
        if (!this.sortByPosition) {
            this.sortByPosition = this.sortOptions.CLICKS.id;
        }
        else if (this.sortByPosition === true) {
            this.sortByPosition = this.sortOptions.POSITION.id;
        }
        else if (this.sortByPosition === false) {
            this.sortByPosition = this.sortOptions.CLICKS.id;
        }

        // fetch items
        this.reduxActions.fetchPoolPriceGroups(this.element.definition.staticValues.poolId);
        this.reduxActions.fetchPoolPropertyGroups(this.element.definition.staticValues.poolId).then(() => {
            this.onFilterChanged(true);
        });

        this.normalizeSecondaryStepValues();
        this.itemOrderByName = this.bindedItemOrderByName.bind(this);
        this.altColorSelect = true;
    }

    onStateChange(state) {
        this.state = state;
    }

    $onDestroy() {
        this.reduxDisconnect();
    }

    onFilterChanged(onInit) {
        if (typeof onInit === 'undefined') {
            onInit = false;
        }
        this.buildFilter(onInit);
        this.reduxActions.fetchPoolItems(
            this.element.definition.staticValues.poolId,
            this.filter,
            this.sortByPosition,
            this.orderBy
        );
        this.reduxActions.fetchPoolColors(this.element.definition.staticValues.poolId, this.filter);
    }

    changeOrderBy(order) {
        this.orderBy = order;
        this.onFilterChanged();
    }

    updateColorRatingFilter(colorHex, colorName) {
        this.filterColorRating.hex = colorHex;
        this.onFilterChanged();
    }

    showPropertiesToggle(index) {
        let container = angular.element('.multiple-filter-container[data-id=' + index + ']');
        if (container.hasClass('active')) {
            container.find('.block-group').fadeIn()
        } else {
            container.find('.block-group').fadeOut()
        }
        container.toggleClass('active');
    }

    isPoolItemSelected(poolItem) {
        let poolItems = [];

        switch (this.steps.currentStep) {
            case 'primary': {
                poolItems = this.steps.primary.materials;
                break;
            }
            case 'secondary': {
                poolItems = this.steps.secondary.materials;
                break;
            }
        }

        for (let i = 0; i < poolItems.length; i++) {
            if (poolItems[i].id === poolItem.material.id) {
                return true;
            }
        }

        return false;
    }

    setValues(materialId, materialName, priceGroup) {
        if ('primary' === this.steps.currentStep) {
            if (false === this.steps.allowMultiple) {
                this.steps.primary.materialId = materialId;
                this.steps.primary.materialName = materialName;
                this.steps.primary.priceGroup = priceGroup;
            } else {
                this.steps.primary.materials.push({id: materialId, name: materialName, priceGroup: priceGroup});
            }
        }

        if ('secondary' === this.steps.currentStep) {
            if (false === this.steps.allowMultiple) {
                this.steps.secondary.materialId = materialId;
                this.steps.secondary.materialName = materialName;
                this.steps.secondary.priceGroup = priceGroup;
            } else {
                this.steps.secondary.materials.push({id: materialId, name: materialName, priceGroup: priceGroup});
            }
        }

        if (false === this.steps.allowMultiple) {
            this.finishCurrentStep();
        }
    }

    removeMaterial(materialId) {
        let poolItems = [];

        switch (this.steps.currentStep) {
            case 'primary': {
                poolItems = this.steps.primary.materials;
                break;
            }
            case 'secondary': {
                poolItems = this.steps.secondary.materials;
                break;
            }
        }

        for (let i = 0; i < poolItems.length; i++) {
            if (poolItems[i].id === materialId) {
                poolItems.splice(i, 1);
                return;
            }
        }
    }

    finishCurrentStep() {
        if ('primary' === this.steps.currentStep && this.steps.secondaryMaterialActive) {
            this.steps.currentStep = 'secondary';
        } else {
            this.setElementProperties();
        }
    }

    setElementProperties() {
        this.configurationService.setElementProperties(
            this.section.id,
            this.element.id,
            {
                aptoElementDefinitionId: this.element.definition.staticValues.aptoElementDefinitionId,
                poolId: this.element.definition.staticValues.poolId,
                productId: this.reduxProps.productId,
                materialId: this.steps.primary.materialId,
                materialName: this.steps.primary.materialName,
                priceGroup: this.steps.primary.priceGroup,
                materials: this.steps.primary.materials,
                materialIdSecondary: this.steps.secondary.materialId,
                materialNameSecondary: this.steps.secondary.materialName,
                priceGroupSecondary: this.steps.secondary.priceGroup,
                materialsSecondary: this.steps.secondary.materials,
                materialColorMixing: this.steps.secondary.colorMixing,
                materialColorArrangement: this.steps.secondary.colorArrangement,
                materialColorQuantity: '' + this.steps.secondary.colorQuantity
            },
            true,
            this.reduxProps.useStepByStep
        );
        this.reduxActions.incrementMaterialClicks(this.steps.primary.materialId);
        this.reduxActions.incrementMaterialClicks(this.steps.secondary.materialId);
    }

    removeValues() {
        this.steps.currentStep = 'primary';
        this.configurationService.removeElement(this.section.id, this.element.id);
    }

    showItemDetails(poolItem) {
        this.ngDialog.open({
            data: {
                images: this.images,
                poolItem: poolItem,
                poolItemImage: poolItem.material.previewImage.fileUrl,
                hasMaterialLightProperties: MaterialPickerController.hasMaterialLightProperties,
                hasMaterialPropertyIcons: MaterialPickerController.hasMaterialPropertyIcons,
                isPreviewImageInHover: MaterialPickerController.isPreviewImageInHover,
                additionalCharge: poolItem.priceGroup.additionalCharge,
                component: this,
                setValuesAndClose: (component, materialId, materialName, priceGroup) => {
                    component.setValues(materialId, materialName, priceGroup);
                    component.ngDialog.close();
                },
                removeMaterialAndClose: (component, materialId) => {
                    component.removeMaterial(materialId);
                    component.ngDialog.close();
                },
                setPoolItemImage: (ngDialogData, fileUrl) => {
                    ngDialogData.poolItemImage = fileUrl;
                }
            },
            template: PoolItemDetailsTemplate,
            plain: true,
            className: 'ngdialog-theme-default pool-items-detail-dialog',
            width: 970
        });
    }

    normalizeSecondaryStepValues() {
        if ('monochrome' === this.steps.secondary.colorMixing) {
            this.steps.secondary.colorArrangement = '';
            this.steps.secondary.colorQuantity = '';
            this.steps.image = this.element.definition.staticValues.monochromeImage;
        }

        if ('multicolored' === this.steps.secondary.colorMixing) {
            if ('' === this.steps.secondary.colorArrangement) {
                this.steps.secondary.colorArrangement = 'alternately';
            }

            if ('alternately' === this.steps.secondary.colorArrangement) {
                this.steps.secondary.colorQuantity = '';
                this.steps.image = this.element.definition.staticValues.multicoloredImageAlternately;
            }

            if ('input' === this.steps.secondary.colorArrangement) {
                this.steps.image = this.element.definition.staticValues.multicoloredImageInput;
            }
        }
    }

    snippet(path, trustAsHtml) {
        return this.snippetFactory.get('plugins.materialPickerElement.' + path, trustAsHtml);
    }
}

MaterialPickerController.$inject = MaterialPickerControllerInject;

const MaterialPickerComponent = {
    bindings: {
        elementInput: '<element',
        sectionInput: '<section',
        sectionCtrlInput: '<sectionCtrl'
    },
    template: MaterialPickerTemplate,
    controller: MaterialPickerController
};

export default ['materialPickerElementDefinition', MaterialPickerComponent];
