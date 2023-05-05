module.exports = {
    getPackage: function () {
        return 'apto-plugin/date-element';
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
            app: 'apto-plugin-date-element/app.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            less: 'apto-plugin-date-element/assets/less/app.less',
            app: 'apto-plugin-date-element/app.js'
        };
    }
};