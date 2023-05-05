module.exports = {
    getPackage: function () {
        return 'apto-plugin/custom-text';
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
            app: 'apto-plugin-custom-text/custom-text.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            less: 'apto-plugin-custom-text/assets/less/custom-text.less',
            app: 'apto-plugin-custom-text/custom-text.js'
        };
    }
};