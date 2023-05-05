module.exports = {
    getPackage: function () {
        return 'apto/catalog';
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
            priority: 5000,
            app: 'apto-catalog/catalog.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 5000,
            app: 'apto-catalog.js',
            less: 'apto-catalog.less'
        };
    }
};