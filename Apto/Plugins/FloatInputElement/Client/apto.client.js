module.exports = {
    getPackage: function () {
        return 'apto-plugin/float-input';
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
            app: 'apto-plugin-float-input/float-input.js'
        };
    },
    frontend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            less: 'apto-plugin-float-input/assets/less/float-input.less',
            app: 'apto-plugin-float-input/float-input.js'
        };
    }
};