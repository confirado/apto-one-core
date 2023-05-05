const CustomPropertyHelperInject = [];
const CustomPropertyHelper = function() {

    // find custom property with given key or null
    this.getByKey = function(customProperties, key) {
        for (let i = 0; i < customProperties.length; i++) {
            if (key === customProperties[i].key) {
                return customProperties[i].value;
            }
        }
        return null;
    };

    // convert custom properties to assoc array
    this.getAssocArray = function(customProperties) {
        let assocArray = {};
        for (let i = 0; i < customProperties.length; i++) {
            assocArray[customProperties[i].key] = customProperties[i].value;
        }
        return assocArray;
    };

    this.$get = function() {
        return {
            getByKey: this.getByKey,
            getAssocArray: this.getAssocArray
        };
    };
};

CustomPropertyHelper.$inject = CustomPropertyHelperInject;

export default ['CustomPropertyHelper', CustomPropertyHelper];