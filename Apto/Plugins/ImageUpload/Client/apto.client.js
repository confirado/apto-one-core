const webpack = require('webpack');

module.exports = {
    getPackage: function () {
        return 'apto-plugin/image-upload';
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
            app: 'apto-plugin-image-upload/image-upload.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            less: 'apto-plugin-image-upload/assets/less/app.less',
            css: 'apto-plugin-image-upload/assets/css/app.js',
            app: 'apto-plugin-image-upload/app.js',
            webpackConfig: {
                plugins: [
                    new webpack.IgnorePlugin(/jsdom$/),
                    new webpack.IgnorePlugin(/xmldom$/),
                    new webpack.IgnorePlugin(/jsdom\/lib\/jsdom\/utils$/),
                    new webpack.IgnorePlugin(/jsdom\/lib\/jsdom\/living\/generated\/utils$/)
                ]
            }
        };
    }
};