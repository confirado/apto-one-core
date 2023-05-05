const ProviderInject = ['AptoExtendProvider'];
const Provider = function(AptoExtendProvider) {
    const self = this;

    function onElementIsDefault(state, element, sectionId, elementId) {
        if(element.staticValues.aptoElementDefinitionId !== 'apto-element-area-element') {
            return state;
        }

        for (let i = 0; i < element.staticValues.fields.length; i++) {
            const
                field = element.staticValues.fields[i],
                fieldName = 'field_' + i;

            if (field.default === null) {
                continue
            }

            state = this.setValue(state, sectionId, elementId, fieldName, field.default);
        }

        return state;
    }

    AptoExtendProvider.addExtend('ConfigurationReducer.handleOnInitDefaultValues', onElementIsDefault);

    self.$get = function() {};
};

Provider.$inject = ProviderInject;

export default ['AptoAreaElementExtendConfigurationReducer', Provider];