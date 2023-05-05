const FactoryInject = ['LanguageFactory'];
const Factory = function (LanguageFactory) {
    const self = this;
    self.snippets = {};

    function getNode(path) {
        return path.split('.').reduce(
            (o, i) => {
                if (o[i]) {
                    return o[i];
                }
                return {};
            }, self.snippets
        );
    }

    function get(path, trustAsHtml) {
        if (trustAsHtml) {
            return LanguageFactory.translateTrustAsHtml(
                getNode(path)
            );
        }

        return LanguageFactory.translate(
            getNode(path)
        );
    }

    function add(component, snippets) {
        self.snippets[component] = snippets;
    }

    return {
        getNode: getNode,
        get: get,
        add: add
    };
};

Factory.$inject = FactoryInject;

export default ['SnippetFactory', Factory];