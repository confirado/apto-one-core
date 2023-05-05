import MaterialPickerElementDefinitionTemplate from './material-picker-element-definition.component.html';
import SortOptions from "../../../../enums/sortOptions";

const MaterialPickerElementDefinitionControllerInject = ['$ngRedux', 'LanguageFactory', 'ElementActions', 'MaterialPickerDefinitionActions'];

class MaterialPickerElementDefinitionController {
    constructor($ngRedux, LanguageFactory, ElementActions, MaterialPickerDefinitionActions) {
        this.pool = null;
        this.poolSearchTerm = '';
        this.defaultMaterial = null;
        this.defaultMaterialSearchTerm = '';

        this.sortOptions = SortOptions;
        this.sortByOptions = [
            {
                id: this.sortOptions.CLICKS.id,
                label: this.sortOptions.CLICKS.label,
            },
            {
                id: this.sortOptions.POSITION.id,
                label: this.sortOptions.POSITION.label,
            },
            {
                id: this.sortOptions.PRICEGROUP.id,
                label: this.sortOptions.PRICEGROUP.label,
            }
        ];

        this.secondaryMaterial = {
            active: false,
            secondaryMaterialAdditionalCharge: 1500,
            monochromeImage: '',
            multicoloredImageAlternately: '',
            multicoloredImageInput: ''
        };
        this.searchboxActive = false;
        this.allowMultiple = false;
        this.altColorSelect = true;
        this.colorSectionActive = true;
        this.priceGroupActive = true;
        this.sortByPosition = this.sortOptions.CLICKS.id;
        this.showPriceGroupInMaterialName = false;
        this.sortOrderActive = false;

        this.values = {
            poolId: '',
            defaultMaterialId: '',
            defaultMaterialPoolId: '',
            secondaryMaterialActive: false,
            secondaryMaterialAdditionalCharge: 1500,
            monochromeImage: '',
            multicoloredImageAlternately: '',
            multicoloredImageInput: '',
            searchboxActive: false,
            allowMultiple: false,
            altColorSelect: false,
            colorSectionActive: true,
            priceGroupActive: true,
            sortByPosition: this.sortOptions.CLICKS.id,
            showPriceGroupInMaterialName: false,
            sortOrderActive: false
        };

        this.mapStateToThis = function (state) {
            return {
                pools: state.pluginMaterialPickerDefinition.pools,
                materials: state.pluginMaterialPickerDefinition.poolMaterials,
                detailDefinition: state.element.detail.definition,
                definitionValues: state.element.definition.values
            }
        };

        this.unSubscribeActions = $ngRedux.connect(this.mapStateToThis, {
            setDefinitionValues: ElementActions.setDefinitionValues,
            fetchAllMaterials: MaterialPickerDefinitionActions.fetchAllMaterials,
            fetchPools: MaterialPickerDefinitionActions.fetchPools,
        })(this);

        this.translate = LanguageFactory.translate;
    };

    $onInit() {
        if (this.detailDefinition.class == 'Apto\\Plugins\\MaterialPickerElement\\Domain\\Core\\Model\\Product\\Element\\MaterialPickerElementDefinition') {
            this.values.poolId = this.detailDefinition.json.poolId;
            this.values.defaultMaterialId = this.detailDefinition.json.defaultMaterialId;
            this.values.defaultMaterialPoolId = this.detailDefinition.json.defaultMaterialPoolId;

            this.values.secondaryMaterialActive = this.detailDefinition.json.secondaryMaterialActive;
            this.secondaryMaterial.active = this.detailDefinition.json.secondaryMaterialActive;

            this.values.monochromeImage = this.detailDefinition.json.monochromeImage;
            this.secondaryMaterial.monochromeImage = this.detailDefinition.json.monochromeImage;

            this.values.multicoloredImageAlternately = this.detailDefinition.json.multicoloredImageAlternately;
            this.secondaryMaterial.multicoloredImageAlternately = this.detailDefinition.json.multicoloredImageAlternately;

            this.values.multicoloredImageInput = this.detailDefinition.json.multicoloredImageInput;
            this.secondaryMaterial.multicoloredImageInput = this.detailDefinition.json.multicoloredImageInput;

            if (!this.detailDefinition.json.searchboxActive) {
                this.detailDefinition.json.searchboxActive = false;
            }

            this.values.searchboxActive = this.detailDefinition.json.searchboxActive;
            this.searchboxActive = this.detailDefinition.json.searchboxActive;

            if (!this.detailDefinition.json.allowMultiple) {
                this.detailDefinition.json.allowMultiple = false;
            }

            if (!this.detailDefinition.json.altColorSelect) {
                this.detailDefinition.json.altColorSelect = false;
            }

            if (!this.detailDefinition.json.hasOwnProperty('colorSectionActive')) {
                this.detailDefinition.json.colorSectionActive = true;
            }

            if (!this.detailDefinition.json.hasOwnProperty('priceGroupActive')) {
                this.detailDefinition.json.priceGroupActive = true;
            }

            if (!this.detailDefinition.json.hasOwnProperty('sortByPosition')) {
                this.detailDefinition.json.sortByPosition = this.sortOptions.CLICKS.id;
            }

            this.values.allowMultiple = this.detailDefinition.json.allowMultiple;
            this.allowMultiple = this.detailDefinition.json.allowMultiple;

            this.values.altColorSelect = this.detailDefinition.json.altColorSelect;
            this.altColorSelect = this.detailDefinition.json.altColorSelect;

            this.values.colorSectionActive = this.detailDefinition.json.colorSectionActive;
            this.colorSectionActive = this.detailDefinition.json.colorSectionActive;

            this.values.priceGroupActive = this.detailDefinition.json.priceGroupActive;
            this.priceGroupActive = this.detailDefinition.json.priceGroupActive;

            if (this.detailDefinition.json.sortByPosition === true) {
                this.values.sortByPosition = this.sortOptions.POSITION.id;
                this.sortByPosition = this.sortOptions.POSITION.id
            } else if (this.detailDefinition.json.sortByPosition === false){
                this.values.sortByPosition = this.sortOptions.CLICKS.id;
                this.sortByPosition = this.sortOptions.CLICKS.id;
            } else {
                this.values.sortByPosition = this.detailDefinition.json.sortByPosition;
                this.sortByPosition = this.detailDefinition.json.sortByPosition;
            }

            if (this.detailDefinition.json.hasOwnProperty('showPriceGroupInMaterialName')) {
                this.values.showPriceGroupInMaterialName = this.detailDefinition.json.showPriceGroupInMaterialName;
                this.showPriceGroupInMaterialName = this.detailDefinition.json.showPriceGroupInMaterialName;
            }

            if (this.detailDefinition.json.hasOwnProperty('sortOrderActive')) {
                this.values.sortOrderActive = this.detailDefinition.json.sortOrderActive;
                this.sortOrderActive = this.detailDefinition.json.sortOrderActive;
            }

            if (this.detailDefinition.json.hasOwnProperty('secondaryMaterialAdditionalCharge')) {
                this.values.secondaryMaterialAdditionalCharge = this.detailDefinition.json.secondaryMaterialAdditionalCharge;
                this.secondaryMaterial.secondaryMaterialAdditionalCharge = this.detailDefinition.json.secondaryMaterialAdditionalCharge;
            }

            this.setDefinitionValues(this.values);
        }

        this.fetchPools().then(() => {
            if (this.detailDefinition.class == 'Apto\\Plugins\\MaterialPickerElement\\Domain\\Core\\Model\\Product\\Element\\MaterialPickerElementDefinition') {
                for (let i = 0; i < this.pools.length; i++) {
                    if (this.detailDefinition.json.poolId === this.pools[i].id) {
                        this.pool = angular.copy(this.pools[i]);
                    }
                }
            }
        });

        this.definitionValidation({
            definitionValidation: {
                validate: () => {
                    if (this.values.poolId === '') {
                        return false;
                    }
                    return true;
                }
            }
        });
    }

    onChangePool(pool) {
        this.values.poolId = '';

        if (typeof pool !== "undefined") {
            this.values.poolId = pool.id;
            this.fetchAllMaterials(pool.id).then(() => {
                this.defaultMaterial = this.materials.find((m) => m.material.id === this.detailDefinition.json.defaultMaterialId )
            });
        } else {
            this.values.defaultMaterialId = '';
            this.values.defaultMaterialPoolId = '';
        }

        if (this.values.poolId !== this.values.defaultMaterialPoolId) {
            this.values.defaultMaterialId = '';
            this.values.defaultMaterialPoolId = '';
            this.defaultMaterial = null;
        }

        this.setDefinitionValues(this.values);
    }

    onChangeDefaultMaterial(defaultMaterial) {
        this.values.defaultMaterialId = '';
        this.values.defaultMaterialPoolId = '';

        if (typeof defaultMaterial !== "undefined") {
            this.values.defaultMaterialId = defaultMaterial.material.id;
            this.values.defaultMaterialPoolId = defaultMaterial.pool.id;
        }

        this.setDefinitionValues(this.values);
    }

    onChangeSecondaryMaterialActive() {
        this.values.secondaryMaterialActive = this.secondaryMaterial.active;
        this.setDefinitionValues(this.values);
    }

    onChangeSecondaryMaterialAdditionalCharge() {
        this.values.secondaryMaterialAdditionalCharge = this.secondaryMaterial.secondaryMaterialAdditionalCharge;
        if (null === this.values.secondaryMaterialAdditionalCharge) {
            this.values.secondaryMaterialAdditionalCharge = 0;
        }
        this.setDefinitionValues(this.values);
    }

    onChangeSearchboxActive() {
        this.values.searchboxActive = this.searchboxActive;
        this.setDefinitionValues(this.values);
    }

    onChangeAllowMultiple() {
        this.values.allowMultiple = this.allowMultiple;
        this.setDefinitionValues(this.values);
    }

    onChangeAltColorSelect() {
        this.values.altColorSelect = this.altColorSelect;
        this.setDefinitionValues(this.values);
    }

    onChangeColorSectionActive() {
        this.values.colorSectionActive = this.colorSectionActive;
        this.setDefinitionValues(this.values);
    }

    onChangePriceGroupActive() {
        this.values.priceGroupActive = this.priceGroupActive;
        this.setDefinitionValues(this.values);
    }

    onChangeSecondaryMaterialImage() {
        this.values.monochromeImage = this.secondaryMaterial.monochromeImage;
        this.values.multicoloredImageAlternately = this.secondaryMaterial.multicoloredImageAlternately;
        this.values.multicoloredImageInput = this.secondaryMaterial.multicoloredImageInput;
        this.setDefinitionValues(this.values);
    }

    onChangeSortColorsByPosition() {
        this.values.sortByPosition = this.sortByPosition;
        this.setDefinitionValues(this.values);
    }

    onChangeShowPriceGroupInMaterialName() {
        this.values.showPriceGroupInMaterialName = this.showPriceGroupInMaterialName;
        this.setDefinitionValues(this.values);
    }

    onChangeSortOrderActive() {
        this.values.sortOrderActive = this.sortOrderActive;
        this.setDefinitionValues(this.values);
    }

    $onDestroy() {
        this.unSubscribeActions();
    };
}

MaterialPickerElementDefinitionController.$inject = MaterialPickerElementDefinitionControllerInject;

const MaterialPickerElementDefinitionComponent = {
    bindings: {
        definitionValidation: '&'
    },
    template: MaterialPickerElementDefinitionTemplate,
    controller: MaterialPickerElementDefinitionController
};

export default ['materialPickerElementDefinition', MaterialPickerElementDefinitionComponent];
