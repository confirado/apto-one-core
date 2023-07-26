module.exports = {
    getPackage: function () {
        return 'apto-plugin/frontend-users';
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
            app: 'apto-plugin-frontend-users/app.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            app: 'apto-plugin-frontend-users/app.js',
            less: 'apto-plugin-frontend-users/assets/app.less'
        };
    }
};