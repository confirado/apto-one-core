import SliderTemplate from './slider.component.html';

const SliderControllerInject = ['$ngRedux'];
const SliderController = function($ngRedux) {
    const self = this;

    function mapStateToThis(state) {
        return {
            editable: state.pluginImageUploadDefinition.editable,
            currentRenderImage: state.renderImage.currentRenderImage,
            spinnerRenderImage: state.index.spinnerRenderImage
        }
    }

    const reduxUnSubscribe = $ngRedux.connect(mapStateToThis, {})(self);

    self.$onDestroy = function () {
        reduxUnSubscribe();
    };
};

const Slider = {
    template: SliderTemplate,
    controller: SliderController
};

SliderController.$inject = SliderControllerInject;

export default ['aptoSlider', Slider];