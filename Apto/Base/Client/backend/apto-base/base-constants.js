let constants = [];

// set route constant
constants.push(['APTO_ENVIRONMENT', AptoEnvironment]);

// additional user settings
constants.push(['APTO_USER_SETTINGS', {
    mediaSelectDefault: 'mediaSelect',
    mediaList: {
        allowOverwriteExisting: false
    }
}]);

// trumbowyg templates
constants.push(['APTO_TRUMBOWYG_TEMPLATES', []]);
constants.push(['APTO_DIST_PATH_URL', APTO_API.root + '/' + APTO_DIST_PATH + '/']);

export default constants;
