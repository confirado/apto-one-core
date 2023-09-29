const ProviderInject = ['AptoExtendProvider'];
const Provider = function(AptoExtendProvider) {
    const self = this;

    function onElementIsDefault(state, element, sectionId, elementId) {
        if(element.staticValues.aptoElementDefinitionId !== 'apto-element-float-input-element') {
            return state;
        }

        if (!element.staticValues.defaultValue) {
            return state;
        }

        const defaultValue = element.staticValues.defaultValue;

        if(null !== defaultValue) {
            state = this.setValue(state, sectionId, elementId, 'value', defaultValue);
        }

        return state;
    }

    AptoExtendProvider.addExtend('ConfigurationReducer.handleOnInitDefaultValues', onElementIsDefault);

    self.$get = function() {};
};

Provider.$inject = ProviderInject;

export default ['AptoFloatInputElementExtendConfigurationReducer', Provider];
