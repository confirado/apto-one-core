import update from 'immutability-helper';

const RenderImageReducerInject = ['AptoReducersProvider', 'APTO_RENDER_IMAGE_PERSPECTIVES'];
const RenderImageReducer = function(AptoReducersProvider, APTO_RENDER_IMAGE_PERSPECTIVES) {
    const TYPE_NS = 'APTO_RENDER_IMAGE_';
    const self = this;
    const initialState = {
        currentRenderImage: false,
        currentPerspective: APTO_RENDER_IMAGE_PERSPECTIVES.default,
        perspectives: APTO_RENDER_IMAGE_PERSPECTIVES.perspectives,
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function renderImage (state, action) {
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_CURRENT_RENDER_IMAGE_FULFILLED'):
                state = update(state, {
                    currentRenderImage: {
                        $set: action.payload.data.result
                    }
                });
                return state;
            case getType('SET_CURRENT_PERSPECTIVE'):
                state = update(state, {
                    currentPerspective: {
                        $set: action.payload
                    }
                });
                return state;
        }

        return state;
    }

    AptoReducersProvider.addReducer('renderImage', renderImage);

    self.$get = function() {};
};

RenderImageReducer.$inject = RenderImageReducerInject;

export default ['RenderImageReducer', RenderImageReducer];