/**
 * @todo something does not work well when items list changes and we reinit slick, cant use ng-if or ng-show right now on the outer container yet
 */
require('slick-carousel');
import SlickSliderTemplate from './slick-slider.component.html';

const SlickSliderControllerInject = ['$timeout', '$element'];
class SlickSliderController {
    constructor ($timeout, $element) {
        this.timeout = $timeout;
        this.element = $element;
        this.slickInitialized = false;
        this.bindItemComponent = '<apto-slick-slider-item item="item"></apto-slick-slider-item>';
        this.options = {
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 4,
            dots: true,
            prevArrow:'<button class="slick-prev slick-arrow"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>',
            nextArrow:'<button class="slick-next slick-arrow"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>'
        };
    }

    mergeOptions() {
        angular.merge(this.options, this.userOptions);
    }

    setItemComponent() {
        if (this.itemComponent) {
            this.bindItemComponent = '<' + this.itemComponent + ' item="item" index="$index" first="$first" middle="$middle" last="$last" even="$even" odd="odd"></' + this.itemComponent + '>';
        }
    }

    initSlider() {
        if (this.items.length < 1) {
            return;
        }

        this.element.slick(this.options);
        this.slickInitialized = true;
    }

    destroySlider() {
        if (true === this.slickInitialized) {
            this.element.slick('destroy');
        }
    }

    $onInit() {
        this.mergeOptions();
        this.setItemComponent();
        this.destroySlider();
        this.initSlider();
    }

    $onChanges(changes) {
        if (changes.items) {
            this.destroySlider();
            this.initSlider();
        }

        if (changes.userOptions) {
            this.mergeOptions();
        }

        if (changes.itemComponent) {
            this.setItemComponent();
        }
    }

    $onDestroy() {
        this.destroySlider();
    }
}

SlickSliderController.$inject = SlickSliderControllerInject;

const SlickSliderComponent = {
    bindings: {
        userOptions: '<options',
        itemComponent: '@',
        items: '<'
    },
    template: SlickSliderTemplate,
    controller: SlickSliderController
};

export default ['aptoSlickSlider', SlickSliderComponent];