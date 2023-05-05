const ProviderInject = [];
const Provider = function() {
    const self = this;
    self.extend = {};

    function addExtend(id, extend) {
        if(!self.extend[id]) {
            self.extend[id] = [];
        }

        self.extend[id].push(extend);
    }

    function getExtend(id) {
        if(!self.extend[id]) {
            return [];
        }

        return self.extend[id];
    }

    self.addExtend = addExtend;
    self.getExtend = getExtend;
    self.$get = function() {};
};

Provider.$inject = ProviderInject;

export default ['AptoExtend', Provider];