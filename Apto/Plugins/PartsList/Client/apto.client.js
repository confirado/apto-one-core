module.exports = {
    getPackage: function () {
        return 'apto-plugin/parts-list';
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
            less: 'apto-plugin-parts-list/assets/less/app.less',
            app: 'apto-plugin-parts-list/app.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            app: 'apto-plugin-parts-list/app.js'
        };
    }
};