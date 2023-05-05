import SlickSliderItemTemplate from './slick-slider-item-component.html';

const SlickSliderItemControllerInject = [];
class SlickSliderItemController {
}

SlickSliderItemController.$inject = SlickSliderItemControllerInject;

const SlickSliderItemComponent = {
    bindings: {
        item: '<',
        index: '<',
        first: '<',
        middle: '<',
        last: '<',
        even: '<',
        odd: '<'
    },
    template: SlickSliderItemTemplate,
    controller: SlickSliderItemController
};

export default ['aptoSlickSliderItem', SlickSliderItemComponent];