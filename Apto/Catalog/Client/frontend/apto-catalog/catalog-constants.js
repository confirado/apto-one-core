let constants = [];

// set constants
if (typeof AptoFrontendProductList === "undefined") {
    constants.push(['APTO_PRODUCT_LIST', null]);
} else {
    constants.push(['APTO_PRODUCT_LIST', AptoFrontendProductList]);
}

// set inline constant
if (typeof AptoInline === "undefined") {
    constants.push(['APTO_INLINE', null]);
} else {
    constants.push(['APTO_INLINE', AptoInline]);
}

constants.push(['APTO_CONFIGURATION_REDUCER_AUTOLOAD_SESSION_STORAGE', false]);
constants.push(['APTO_CONFIGURATION_REDUCER_DISABLE_SESSION_STORAGE', false]);
constants.push(['APTO_CONFIGURATION_REDUCER_FAST_DISABLE_ELEMENTS', false]);
constants.push(['APTO_CONFIGURATION_SERVICE_DISABLE_RENDER_IMAGE_FETCH', false]);

// set perspectives constant
if (typeof AptoPerspectives === "undefined") {
    constants.push(['APTO_RENDER_IMAGE_PERSPECTIVES', {
        default: 'persp1',
        perspectives: ['persp1', 'persp2', 'persp3', 'persp4'],
        basketPerspectives: [ 'persp1' ]
    }]);
} else {
    constants.push(['APTO_RENDER_IMAGE_PERSPECTIVES', AptoPerspectives]);
}

constants.push(['APTO_CONFIGURATION_SERVICE_DEFAULT_REPAIR_SETTINGS', null]);
constants.push(['APTO_FRONTEND_ROUTE_ACCESS', {
    onlyLoggedIn: false,
    onlyLoggedInWhiteList: []
}]);

export default constants;
