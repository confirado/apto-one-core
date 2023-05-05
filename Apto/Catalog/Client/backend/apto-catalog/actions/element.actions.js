const ElementActionsInject = ['MessageBusFactory'];
const ElementActions = function (MessageBusFactory) {
    function getType(type) {
        return 'APTO_ELEMENT_' + type;
    }

    function fetchDetail (elementId) {
        return {
            type: getType('FETCH_DETAIL'),
            payload: MessageBusFactory.query('FindElement', [elementId])
        }
    }

    function fetchRegisteredDefinitions () {
        return {
            type: getType('FETCH_REGISTERED_DEFINITIONS'),
            payload: MessageBusFactory.query('FindRegisteredElementDefinitions', [])
        }
    }

    function fetchRenderImages(elementId) {
        return {
            type: getType('FETCH_RENDER_IMAGES'),
            payload: MessageBusFactory.query('FindElementRenderImages', [elementId])
        }
    }

    function fetchCustomProperties(elementId) {
        return {
            type: getType('FETCH_CUSTOM_PROPERTIES'),
            payload: MessageBusFactory.query('FindElementCustomProperties', [elementId])
        }
    }

    function fetchPrices(elementId) {
        return {
            type: getType('FETCH_PRICES'),
            payload: MessageBusFactory.query('FindElementPrices', [elementId])
        }
    }

    function fetchPriceFormulas(elementId) {
        return {
            type: getType('FETCH_PRICE_FORMULAS'),
            payload: MessageBusFactory.query('FindElementPriceFormulas', [elementId])
        }
    }

    function fetchDiscounts(elementId) {
        return {
            type: getType('FETCH_DISCOUNTS'),
            payload: MessageBusFactory.query('FindElementDiscounts', [elementId])
        }
    }

    function fetchSections(productId) {
        return {
            type: getType('FETCH_SECTIONS'),
            payload: MessageBusFactory.query('FindProductSectionsElements', [productId])
        }
    }

    function fetchAttachments(elementId) {
        return {
            type: getType('FETCH_ATTACHMENTS'),
            payload: MessageBusFactory.query('FindElementAttachments', [elementId])
        }
    }

    function fetchGallery(elementId) {
        return {
            type: getType('FETCH_GALLERY'),
            payload: MessageBusFactory.query('FindElementGallery', [elementId])
        }
    }

    function fetchAvailablePriceMatrices(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }

            return dispatch({
                type: getType('FETCH_PRICE_MATRICES'),
                payload: MessageBusFactory.query('FindPriceMatrices', [searchString])
            });
        };
    }

    function setDefinitionClassName(className) {
        return {
            type: getType('SET_DEFINITION_CLASS_NAME'),
            payload: className
        }
    }

    function setDefinitionValues(values) {
        return {
            type: getType('SET_DEFINITION_VALUES'),
            payload: values
        }
    }

    function setDetailValue(key, value) {
        return {
            type: getType('SET_DETAIL_VALUE'),
            payload: {
                key: key,
                value: value
            }
        }
    }

    function resetDefinitionValues() {
        return {
            type: getType('RESET_DEFINITION_VALUES')
        }
    }

    function reset () {
        return {
            type: getType('RESET')
        }
    }

    return {
        fetchDetail: fetchDetail,
        fetchRegisteredDefinitions: fetchRegisteredDefinitions,
        fetchRenderImages: fetchRenderImages,
        fetchCustomProperties: fetchCustomProperties,
        fetchPrices: fetchPrices,
        fetchPriceFormulas: fetchPriceFormulas,
        fetchDiscounts: fetchDiscounts,
        fetchSections: fetchSections,
        fetchAttachments: fetchAttachments,
        fetchGallery: fetchGallery,
        fetchAvailablePriceMatrices: fetchAvailablePriceMatrices,
        setDefinitionClassName: setDefinitionClassName,
        setDefinitionValues: setDefinitionValues,
        setDetailValue: setDetailValue,
        resetDefinitionValues: resetDefinitionValues,
        reset: reset
    }
};

ElementActions.$inject = ElementActionsInject;

export default ['ElementActions', ElementActions];
