let constants = [];

// set locale constant
if (typeof AptoFrontendLocale === "undefined") {
    constants.push(['APTO_DEFAULT_LOCALE', null]);
} else {
    constants.push(['APTO_DEFAULT_LOCALE', AptoFrontendLocale]);
}

// set languages constant
if (typeof AptoFrontendLanguages === "undefined") {
    constants.push(['APTO_LANGUAGES', null]);
} else {
    constants.push(['APTO_LANGUAGES', AptoFrontendLanguages]);
}

constants.push(['APTO_DEFAULT_CURRENCY', {
    displayCurrency: {
        symbol: '€',
        name: 'Euro',
        currency: 'EUR',
        factor: 1.0
    },
    shopCurrency: {
        symbol: '€',
        name: 'Euro',
        currency: 'EUR',
        factor: 1.0
    }
}]);

constants.push(['APTO_DEFAULT_CUSTOMER_GROUP', {
    id: '00000000-0000-0000-0000-000000000000',
    name: 'Internal',
    inputGross: true,
    showGross: true
}]);

constants.push(['APTO_DIST_PATH_URL', APTO_API.root + '/' + APTO_DIST_PATH + '/']);

// @todo this constant is registered in preload from catalog, i think we can remove it
constants.push(['APTO_SHOP_CONTEXT', {}]);

export default constants;
