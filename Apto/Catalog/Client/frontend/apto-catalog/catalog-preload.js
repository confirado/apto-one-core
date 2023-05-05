import { register } from 'app/apto-loader';
const domain = window.location.host;

const Preload = [{
    query: 'FindShopContext',
    arguments: [domain],
    fulfilled: function (response) {
        let shop = null;
        let languages = null;

        if (null !== response.data.result) {
            shop = response.data.result;
            languages = shop.languages;
            delete shop.languages;
        }

        register('constants', [
            ['APTO_SHOP_CONTEXT', shop],
            ['APTO_LANGUAGES', languages]
        ]);
    },
    rejected: function (error) {
        register('constants', [
            ['APTO_SHOP_CONTEXT', null],
            ['APTO_LANGUAGES', null]
        ]);
    }
}];
export default Preload;
