module.exports = {
    getPackage: function () {
        return 'apto-plugin/import-export';
    },
    client: function (app) {
        switch (app) {
            case 'backend': {
                return this.backend();
            }
        }
        return null;
    },
    backend: function () {
        return {
            package: this.getPackage(),
            priority: 1000,
            app: 'apto-plugin-import-export/app.js',
            less: 'apto-plugin-import-export/assets/less/app.less'
        };
    },
};