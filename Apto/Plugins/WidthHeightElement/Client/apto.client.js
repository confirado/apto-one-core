module.exports = {
    getPackage: function () {
        return 'apto-plugin/width-height';
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
            app: 'apto-plugin-width-height/width-height.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            less: 'apto-plugin-width-height/assets/less/width-height.less',
            app: 'apto-plugin-width-height/width-height.js'
        };
    }
};