module.exports = {
    getPackage: function () {
        return 'apto-plugin/material-picker';
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
            less: 'apto-plugin-material-picker/assets/less/material-picker.less',
            app: 'apto-plugin-material-picker/material-picker.js',
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            less: 'apto-plugin-material-picker/assets/less/material-picker.less',
            app: 'apto-plugin-material-picker/material-picker.js'
        };
    }
};