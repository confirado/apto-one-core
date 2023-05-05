const ProviderInject = ['AptoExtendProvider'];
const Provider = function(AptoExtendProvider) {
    const self = this;

    function onElementIsDefault(state, element, sectionId, elementId) {
        if(element.staticValues.aptoElementDefinitionId !== 'apto-element-width-height') {
            return state;
        }

        const defaultWidth = element.staticValues.defaultWidth;
        const defaultHeight = element.staticValues.defaultHeight;

        if(null !== defaultWidth) {
            state = this.setValue(state, sectionId, elementId, 'width', defaultWidth);
        }

        if(null !== defaultHeight) {
            state = this.setValue(state, sectionId, elementId, 'height', defaultHeight);
        }

        return state;
    }

    AptoExtendProvider.addExtend('ConfigurationReducer.handleOnInitDefaultValues', onElementIsDefault);

    self.$get = function() {};
};

Provider.$inject = ProviderInject;

export default ['AptoWidthHeightElementExtendConfigurationReducer', Provider];