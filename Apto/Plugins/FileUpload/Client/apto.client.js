const webpack = require('webpack');

module.exports = {
    getPackage: function () {
        return 'apto-plugin/file-upload';
    },
    client: function (app) {
        switch (app) {
            case 'backend': {
                return this.backend();
            }
            case 'frontend': {
                return this.frontend();
            }
        }
        return null;
    },
    backend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            app: 'apto-plugin-file-upload/app.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            less: 'apto-plugin-file-upload/assets/less/app.less',
            app: 'apto-plugin-file-upload/app.js'
        };
    }
};