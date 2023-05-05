import SliderTemplate from './slider.component.html';

const SliderControllerInject = ['$ngRedux'];
const SliderController = function($ngRedux) {
    const self = this;

    function mapStateToThis(state) {
        return {
            currentRenderImage: state.renderImage.currentRenderImage,
            spinnerRenderImage: state.index.spinnerRenderImage,
            aptoProduct: state.configuration.present.raw.product
        }
    }

    function getPreviewImage() {
        if (this.aptoProduct.previewImage) {
            return APTO_API.media + this.aptoProduct.previewImage;
        }

        return false;
    }

    const reduxUnSubscribe = $ngRedux.connect(mapStateToThis, {})(self);

    self.getPreviewImage = getPreviewImage;
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
