let constants = [];

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

export default constants;