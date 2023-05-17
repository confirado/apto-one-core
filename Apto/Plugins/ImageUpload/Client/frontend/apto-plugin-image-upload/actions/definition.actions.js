const DefinitionActionsInject = ['MessageBusFactory'];
const DefinitionActions = function (MessageBusFactory) {
    const TYPE_NS = 'PLUGIN_IMAGE_UPLOAD_DEFINITION_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function setUserImageElement(element) {
        return {
            type: getType('SET_USER_IMAGE_ELEMENT'),
            payload: element
        }
    }

    function setUserImageEditable(editable, elementId) {
        return {
            type: getType('SET_USER_IMAGE_EDITABLE'),
            payload: {
                editable: editable,
                elementId: elementId
            }
        }
    }

    function addUserUpload(image) {
        return {
            type: getType('ADD_USER_UPLOAD'),
            payload: image
        }
    }

    function setOrigPerspective(perspective, elementId) {
        return {
            type: getType('SET_ORIG_PERSPECTIVE'),
            payload: {
                perspective: perspective,
                elementId: elementId
            }
        }
    }

    function setBackground(background, elementId) {
        return {
            type: getType('SET_BACKGROUND'),
            payload: {
                background: background,
                elementId: elementId
            }
        }
    }

    function setTimestamp(timestamp, elementId) {
        return {
            type: getType('SET_TIMESTAMP'),
            payload: {
                timestamp: timestamp,
                elementId: elementId
            }
        }
    }

    function uploadUserImageLoading(loading, elementId) {
        return {
            type: getType('UPLOAD_USER_IMAGE_LOADING'),
            payload: {
                loading: loading,
                elementId: elementId
            }
        }
    }

    function uploadUserImageProgress(progress, elementId) {
        return {
            type: getType('UPLOAD_USER_IMAGE_PROGRESS'),
            payload: {
                progress: progress,
                elementId: elementId
            }
        }
    }

    function uploadUserImageError(error, elementId) {
        return {
            type: getType('UPLOAD_USER_IMAGE_ERROR'),
            payload: {
                error: error,
                elementId: elementId
            }
        }
    }

    function setUserImageUploadErrors(userImageUploadErrors, elementId) {
        return {
            type: getType('SET_USER_IMAGE_UPLOAD_ERRORS'),
            payload: {
                userImageUploadErrors: userImageUploadErrors,
                elementId: elementId
            }
        }
    }

    function setItemOnCanvas(elementId, fabricItemInput, fabricItemOptions) {
        return {
            type: getType('SET_ITEM_ON_CANVAS'),
            payload: {
                elementId: elementId,
                itemType: fabricItemOptions.itemType,
                fabricItemInput: fabricItemInput,
                fabricItemOptions: fabricItemOptions
            }
        }
    }

    function removeItemOnCanvas(elementId, fabricItemId, type) {
        return {
            type: getType('REMOVE_ITEM_ON_CANVAS'),
            payload: {
                elementId: elementId,
                itemType: type,
                fabricItemId: fabricItemId,
            }
        }
    }

    function setItemsOnCanvas(elementId, fabricItems) {
        return {
            type: getType('SET_ITEMS_ON_CANVAS'),
            payload: {
                elementId: elementId,
                fabricItems: fabricItems
            }
        }
    }

    function setRenderImage(renderImage, elementId) {
        return {
            type: getType('SET_RENDER_IMAGE'),
            payload: {
                renderImage: renderImage,
                elementId: elementId
            }
        }
    }

    function setCurrentItem(fabricItemId) {
        return {
            type: getType('SET_CURRENT_FABRIC_ITEM'),
            payload: {
                currentFabricItemId: fabricItemId
            }
        }
    }

    function fetchPoolItems(poolId, filter) {
        return {
            type: getType('FETCH_POOL_ITEMS'),
            payload: MessageBusFactory.query('FindMaterialPickerPoolItemsFiltered', [poolId, filter])
        }
    }

    function reset(elementId) {
        return {
            type: getType('RESET'),
            payload: elementId
        }
    }

    return {
        setUserImageElement: setUserImageElement,
        setUserImageEditable: setUserImageEditable,
        setOrigPerspective: setOrigPerspective,
        setBackground: setBackground,
        setTimestamp: setTimestamp,
        uploadUserImageLoading: uploadUserImageLoading,
        uploadUserImageProgress: uploadUserImageProgress,
        uploadUserImageError: uploadUserImageError,
        setUserImageUploadErrors: setUserImageUploadErrors,
        setRenderImage: setRenderImage,
        reset: reset,
        setCurrentItem: setCurrentItem,
        setItemOnCanvas: setItemOnCanvas,
        removeItemOnCanvas: removeItemOnCanvas,
        setItemsOnCanvas: setItemsOnCanvas,
        fetchPoolItems: fetchPoolItems,
        addUserUpload: addUserUpload
    };
};

DefinitionActions.$inject = DefinitionActionsInject;

export default ['ImageUploadDefinitionActions', DefinitionActions];