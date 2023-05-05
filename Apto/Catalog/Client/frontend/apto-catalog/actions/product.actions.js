const ProductActionsInject = ['$ngRedux', 'MessageBusFactory'];
const ProductActions = function($ngRedux, MessageBusFactory) {
    const TYPE_NS = 'APTO_PRODUCT_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function setProductDetail(product) {
        return {
            type: getType('SET_PRODUCT_DETAIL'),
            payload: product
        }
    }

    function selectSection(sectionId) {
        return {
            type: getType('SELECT_SECTION'),
            payload: sectionId
        }
    }

    function fetchComputedValues(productId, compressedState) {
        return {
            type: getType('FETCH_COMPUTED_PRODUCT_VALUES_CALCULATED'),
            payload: MessageBusFactory.query('FindProductComputedValuesCalculated', [productId, compressedState])
        }
    }

    function resetSection() {
        return {
            type: getType('RESET_SECTION')
        }
    }

    return {
        setProductDetail: setProductDetail,
        selectSection: selectSection,
        resetSection: resetSection,
        fetchComputedValues: fetchComputedValues
    };
};

ProductActions.$inject = ProductActionsInject;

export default ['ProductActions', ProductActions];