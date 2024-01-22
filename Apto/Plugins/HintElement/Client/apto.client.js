module.exports = {
    getPackage: function () {
        return 'apto-plugin/hint';
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
            app: 'apto-plugin-hint/hint.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            app: 'apto-plugin-hint/hint.js',
            less: 'apto-plugin-hint/assets/less/hint.less'
        };
    }
};