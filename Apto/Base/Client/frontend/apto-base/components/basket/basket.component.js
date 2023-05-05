import BasketTemplate from './basket.component.html';
import DefaultBasketThumb from '../../assets/img/default-basket-thumb.jpg';

const BasketControllerInject = ['$ngRedux', '$timeout', 'ngDialog', 'IndexActions', 'SnippetFactory', 'APTO_DIST_PATH_URL'];
const BasketController = function($ngRedux, $timeout, ngDialog, IndexActions, SnippetFactory, APTO_DIST_PATH_URL) {
    const self = this;

    function mapStateToThis (state) {
        return {
            shopSession: state.index.shopSession,
            scrollbarWidth: state.index.scrollbarWidth,
            sidebarRightOpen: state.index.sidebarRightOpen
        };
    }

    const reduxSubscribe = $ngRedux.connect(mapStateToThis, {
        closeSidebarRight: IndexActions.closeSidebarRight,
        shopRemoveFromBasket: IndexActions.shopRemoveFromBasket
    })(self);

    function removeFromBasket($event, article) {
        $event.preventDefault();
        ngDialog.openConfirm({
            template:'\
                <p>' + snippet('confirmDeleteDialog.text') + '</p>\
                <div class="block-group">\
                    <button type="button" class="block" ng-click="closeThisDialog(0)">' + snippet('confirmDeleteDialog.cancel') + '</button>\
                    <button type="button" class="block right" ng-click="confirm(1)">' + snippet('confirmDeleteDialog.confirm') + '</button>\
                </div>',
            plain: true,
            className: 'ngdialog-theme-default confirm-dialog'
        }).then(()=>{
            self.shopRemoveFromBasket(article);
        }, ()=>{
        });
    }

    function closeBasket($event) {
        $event.preventDefault();
        self.closeSidebarRight();
    }

    function snippet(path, trustAsHtml) {
        return SnippetFactory.get('aptoBasket.' + path, trustAsHtml);
    }

    self.snippet = snippet;
    self.removeFromBasket = removeFromBasket;
    self.closeBasket = closeBasket;
    self.defaultBasketThumb = APTO_DIST_PATH_URL + DefaultBasketThumb;

    self.$onDestroy = function () {
        reduxSubscribe();
    };
};

const Basket = {
    template: BasketTemplate,
    controller: BasketController
};

BasketController.$inject = BasketControllerInject;

export default ['aptoBasket', Basket];
