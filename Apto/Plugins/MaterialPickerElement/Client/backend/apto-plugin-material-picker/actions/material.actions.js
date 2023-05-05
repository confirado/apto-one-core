const MaterialActionsInject = ['MessageBusFactory', 'PageHeaderActions'];
const MaterialActions = function (MessageBusFactory, PageHeaderActions) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_MATERIAL_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchMaterial(id) {
        return {
            type: getType('FETCH_MATERIAL'),
            payload: MessageBusFactory.query('FindMaterialPickerMaterial', [id])
        }
    }

    function fetchMaterials(searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_MATERIALS'),
            payload: MessageBusFactory.query('FindMaterialPickerMaterials', [searchString])
        }
    }

    function fetchMaterialsByPage(pageNumber, recordsPerPage, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_MATERIALS_BY_PAGE'),
            payload: MessageBusFactory.query('FindMaterialPickerMaterialsByPage', [pageNumber, recordsPerPage, searchString])
        };
    }

    function fetchGalleryImages(id) {
        return {
            type: getType('FETCH_GALLERY_IMAGES'),
            payload: MessageBusFactory.query('FindMaterialPickerMaterialGalleryImages', [id])
        }
    }

    function fetchProperties(id) {
        return {
            type: getType('FETCH_PROPERTIES'),
            payload: MessageBusFactory.query('FindMaterialPickerMaterialProperties', [id])
        }
    }

    function fetchNotAssignedProperties(id, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_NOT_ASSIGNED_PROPERTIES'),
            payload: MessageBusFactory.query('FindMaterialPickerNotAssignedMaterialProperties', [id, searchString])
        }
    }

    function fetchPoolItems(materialId) {
        return {
            type: getType('FETCH_POOL_ITEMS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolItemsByMaterial', [materialId])
        }
    }

    function fetchNotAssignedPools(materialId, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_NOT_ASSIGNED_POOLS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolsWithoutMaterial', [materialId, searchString])
        }
    }

    function saveMaterial(material) {
        return dispatch => {
            let commandArguments = [];

            if(typeof material.active === "undefined") {
                material.active = false;
            }

            if(typeof material.isNotAvailable === "undefined") {
                material.isNotAvailable = false;
            }

            if (typeof material.clicks === "undefined") {
                material.clicks = 0;
            }

            if (typeof material.previewImage === "undefined") {
                material.previewImage = {
                    path: ''
                };
            }

            if (typeof material.position === "undefined") {
                material.position = 0;
            }

            commandArguments.push(material.identifier);
            commandArguments.push(material.name);
            commandArguments.push(material.description);
            commandArguments.push(material.clicks);
            commandArguments.push(material.previewImage.path);
            commandArguments.push(material.reflection);
            commandArguments.push(material.transmission);
            commandArguments.push(material.absorption);
            commandArguments.push(material.active);
            commandArguments.push(material.isNotAvailable);
            commandArguments.push(material.position);

            if(typeof material.id !== "undefined") {
                commandArguments.unshift(material.id);
                return dispatch({
                    type: getType('UPDATE_MATERIAL'),
                    payload: MessageBusFactory.command('UpdateMaterialPickerMaterial', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_MATERIAL'),
                payload: MessageBusFactory.command('AddMaterialPickerMaterial', commandArguments)
            });
        }
    }

    function removeMaterial(id) {
        return {
            type: getType('REMOVE_MATERIAL'),
            payload: MessageBusFactory.command('RemoveMaterialPickerMaterial', [id])
        }
    }

    function resetMaterial() {
        return {
            type: getType('RESET_MATERIAL')
        }
    }

    function addGalleryImage(materialId, galleryImage) {
        return {
            type: getType('ADD_GALLERY_IMAGE'),
            payload: MessageBusFactory.command('AddMaterialPickerMaterialGalleryImage', [materialId, galleryImage])
        }
    }

    function removeGalleryImage(materialId, galleryImageId) {
        return {
            type: getType('REMOVE_GALLERY_IMAGE'),
            payload: MessageBusFactory.command('RemoveMaterialPickerMaterialGalleryImage', [materialId, galleryImageId])
        }
    }

    function addProperty(materialId, propertyId) {
        return {
            type: getType('ADD_PROPERTY'),
            payload: MessageBusFactory.command('AddMaterialPickerMaterialProperty', [materialId, propertyId])
        }
    }

    function removeProperty(materialId, propertyId) {
        return {
            type: getType('REMOVE_PROPERTY'),
            payload: MessageBusFactory.command('RemoveMaterialPickerMaterialProperty', [materialId, propertyId])
        }
    }

    function fetchColorRatings(id) {
        return {
            type: getType('FETCH_COLOR_RATINGS'),
            payload: MessageBusFactory.query('FindMaterialPickerMaterialColorRatings', [id])
        }
    }

    function addColorRating(materialId, color, rating) {
        return {
            type: getType('ADD_COLOR_RATING'),
            payload: MessageBusFactory.command('AddMaterialPickerMaterialColorRating', [materialId, color, rating])
        }
    }

    function removeColorRating(materialId, colorRatingId) {
        return {
            type: getType('REMOVE_COLOR_RATING'),
            payload: MessageBusFactory.command('RemoveMaterialPickerMaterialColorRating', [materialId, colorRatingId])
        }
    }

    function addMaterialRenderImage(materialId, poolId, layer, perspective, file, offsetX, offsetY) {
        return {
            type: getType('ADD_RENDER_IMAGE'),
            payload: MessageBusFactory.command('AddMaterialPickerMaterialRenderImage', [materialId, poolId, layer, perspective, file, offsetX, offsetY])
        }
    }

    function removeMaterialRenderImage(materialId, renderImageId) {
        return {
            type: getType('REMOVE_RENDER_IMAGE'),
            payload: MessageBusFactory.command('RemoveMaterialPickerMaterialRenderImage', [materialId, renderImageId])
        }
    }

    function fetchMaterialRenderImages(materialId) {
        return {
            type: getType('FETCH_RENDER_IMAGES'),
            payload: MessageBusFactory.query('FindMaterialPickerMaterialRenderImages', [materialId])
        }
    }

    function fetchPools() {
        return {
            type: getType('FETCH_POOLS'),
            payload: MessageBusFactory.query('FindMaterialPickerPools', [])
        }
    }

    function availableCustomerGroupsFetch() {
        return {
            type: getType('AVAILABLE_CUSTOMER_GROUPS_FETCH'),
            payload: MessageBusFactory.query('FindCustomerGroups', [''])
        }
    }

    function fetchPrices(materialId) {
        return {
            type: getType('FETCH_PRICES'),
            payload: MessageBusFactory.query('FindMaterialPickerMaterialPrices', [materialId])
        }
    }

    function addPrice(materialId, amount, currencyCode, customerGroupId) {
        return {
            type: getType('ADD_PRODUCT_PRICE'),
            payload: MessageBusFactory.command('AddMaterialPickerMaterialPrice', [materialId, amount, currencyCode, customerGroupId])
        }
    }

    function removePrice(materialId, priceId) {
        return {
            type: getType('REMOVE_PRODUCT_PRICE'),
            payload: MessageBusFactory.command('RemoveMaterialPickerMaterialPrice', [materialId, priceId])
        }
    }

    return {
        setPageNumber: PageHeaderActions.setPageNumber(TYPE_NS),
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        fetchMaterial: fetchMaterial,
        fetchMaterials: fetchMaterials,
        fetchMaterialsByPage: fetchMaterialsByPage,
        fetchGalleryImages: fetchGalleryImages,
        fetchProperties: fetchProperties,
        fetchNotAssignedProperties: fetchNotAssignedProperties,
        fetchPoolItems: fetchPoolItems,
        fetchNotAssignedPools: fetchNotAssignedPools,
        saveMaterial: saveMaterial,
        removeMaterial: removeMaterial,
        resetMaterial: resetMaterial,
        addGalleryImage: addGalleryImage,
        removeGalleryImage: removeGalleryImage,
        addProperty: addProperty,
        removeProperty: removeProperty,
        fetchColorRatings: fetchColorRatings,
        addColorRating: addColorRating,
        removeColorRating: removeColorRating,
        addMaterialRenderImage: addMaterialRenderImage,
        removeMaterialRenderImage: removeMaterialRenderImage,
        fetchMaterialRenderImages: fetchMaterialRenderImages,
        fetchPools: fetchPools,
        availableCustomerGroupsFetch: availableCustomerGroupsFetch,
        fetchPrices: fetchPrices,
        addPrice: addPrice,
        removePrice: removePrice
    };
};

MaterialActions.$inject = MaterialActionsInject;

export default ['MaterialPickerMaterialActions', MaterialActions];
