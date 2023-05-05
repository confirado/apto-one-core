const RenderImageActionsInject = ['MessageBusFactory'];
const RenderImageActions = function(MessageBusFactory) {
    const TYPE_NS = 'APTO_RENDER_IMAGE_';
    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchCurrentRenderImage(compressedState, perspective, productId) {
        return {
            type: getType('FETCH_CURRENT_RENDER_IMAGE'),
            payload: MessageBusFactory.query('FindRenderImageByState', [compressedState, perspective, productId])
        }
    }

    function setCurrentPerspective(perspective) {
        return {
            type: getType('SET_CURRENT_PERSPECTIVE'),
            payload: perspective
        }
    }

    return {
        fetchCurrentRenderImage: fetchCurrentRenderImage,
        setCurrentPerspective: setCurrentPerspective,
    };
};

RenderImageActions.$inject = RenderImageActionsInject;

export default ['RenderImageActions', RenderImageActions];