module.exports = {
    getPackage: function () {
        return 'apto-plugin/parts-list-element';
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
            app: 'apto-plugin-parts-list-element/parts-list-element.js'
        };
    },
};
