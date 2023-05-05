const ProviderInject = ['AptoExtendProvider'];
const Provider = function(AptoExtendProvider) {
    const self = this;

    function onElementIsDefault(state, element, sectionId, elementId) {
        if(element.staticValues.aptoElementDefinitionId !== 'apto-element-select-box') {
            return state;
        }

        const defaultItem = element.staticValues.defaultItem;

        if(null !== defaultItem) {
            state = this.setValue(state, sectionId, elementId, 'id', defaultItem.id);
            state = this.setValue(state, sectionId, elementId, 'name', defaultItem.name);
        }

        return state;
    }

    AptoExtendProvider.addExtend('ConfigurationReducer.handleOnInitDefaultValues', onElementIsDefault);

    self.$get = function() {};
};

Provider.$inject = ProviderInject;

export default ['AptoSelectBoxElementExtendConfigurationReducer', Provider];