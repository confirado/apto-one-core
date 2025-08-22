const webpack = require('webpack');

module.exports = {
    getPackage: function () {
        return 'apto/base';
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
            priority: 10000,
            app: 'apto-base/base.js',
            angular: {
                requires: [
                    'angular',
                    'angular-route',
                    'angular-sanitize',
                    'angular-animate',
                    'ng-redux',
                    'angular-material',
                    'angular-material-expansion-panel',
                    'angular-material-data-table',
                    'angular-loading-bar',
                    'ng-file-upload',
                    'apto-base/libs/angular-tree-control/context-menu',
                    'apto-base/libs/angular-tree-control/angular-tree-control',
                    'apto-base/libs/mdColorPicker/mdColorPicker.js'
                ],
                modules: [
                    'ngRoute',
                    'ngSanitize',
                    'ngAnimate',
                    'ngRedux',
                    'ngMaterial',
                    'material.components.expansionPanels',
                    'md.data.table',
                    'angular-loading-bar',
                    'ngFileUpload',
                    'treeControl',
                    'mdColorPicker'
                ]
            },
            webpackConfig: {
                plugins: [
                    new webpack.ProvidePlugin({
                        'tinycolor': 'tinycolor2'
                    })
                ]
            }
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 10000,
            app: 'apto-base/base.js',
            less: 'apto-base/assets/less/base.less',
            css: 'apto-base/assets/css/app.js',
            angular: {
                requires: [
                    'angular',
                    'angular-route',
                    'angular-sanitize',
                    'angular-animate',
                    'ng-redux',
                    'angular-loading-bar',
                    'ng-dialog',
                    'angular-filter',
                    'angularjs-slider',
                    'ng-file-upload',
                    'apto-base/libs/angular-tree-control/context-menu',
                    'apto-base/libs/angular-tree-control/angular-tree-control'
                ],
                modules: [
                    'ngRoute',
                    'ngSanitize',
                    'ngAnimate',
                    'ngRedux',
                    'angular-loading-bar',
                    'ngDialog',
                    'angular.filter',
                    'rzModule',
                    'ngFileUpload',
                    'treeControl'
                ]
            }
        };
    }
};
