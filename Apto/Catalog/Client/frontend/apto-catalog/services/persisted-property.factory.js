const PersistedPropertiesInject = ['$ngRedux'];
const PersistedProperties = function ($ngRedux) {

    let persistedPropertiesFactory = {
        properties: {},
        groups: {}
    };

    this.mapStateToThis = function(state) {
        return {
            properties: state.persistedProperties.properties,
            groups: state.persistedProperties.groups,
            getPersistedProperty: getPersistedProperty
        }
    };
    $ngRedux.connect(this.mapStateToThis)(persistedPropertiesFactory);

    // get group by sectionId and elementId
    function getGroupBySectionAndElement(sectionId, elementId) {
        if (
            !persistedPropertiesFactory.groups.hasOwnProperty(sectionId) ||
            !persistedPropertiesFactory.groups[sectionId].hasOwnProperty(elementId)
        ) {
            return null;
        }

        return persistedPropertiesFactory.groups[sectionId][elementId];
    }

    // get properties by group
    function getPropertiesByGroup(group) {
        if (!persistedPropertiesFactory.properties.hasOwnProperty(group)) {
            return null;
        }

        return persistedPropertiesFactory.properties[group];
    }


    // get properties by sectionId and elementId
    function getPropertiesBySectionAndElement(sectionId, elementId) {
        let group = getGroupBySectionAndElement(sectionId, elementId);

        if (null === group) {
            return null;
        }

        return getPropertiesByGroup(group);
    }

    // get persisted property or default for sectionId and elementId
    function getPersistedProperty(sectionId, elementId, property, defaultValue) {
        let properties = getPropertiesBySectionAndElement(sectionId, elementId);

        // use null for undefined defaultValue
        if (typeof defaultValue === 'undefined') {
            defaultValue = null;
        }

        // return default if no properties found
        if (null === properties) {
            return defaultValue;
        }

        // return default if property does not exist in properties
        if (!properties.hasOwnProperty(property)) {
            return defaultValue;
        }

        return properties[property];
    }

    return persistedPropertiesFactory;
};

PersistedProperties.$inject = PersistedPropertiesInject;

export default ['PersistedPropertiesFactory', PersistedProperties];