import update from 'immutability-helper';

const PersistedPropertiesReducerInject = ['$windowProvider', 'AptoReducersProvider'];
const PersistedPropertiesReducer = function($windowProvider, AptoReducersProvider) {
    const TYPE_NS = 'APTO_PERSISTED_PROPERTIES_';
    const self = this;
    const $window = $windowProvider.$get();
    const initialState = {
        groups: {},
        properties: {}
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function savePropertiesToSessionStorage(state) {
        // save state to session storage
        const propertiesState = JSON.stringify({
            properties: state.properties
        });
        $window.sessionStorage.setItem('aptoPersistedPropertiesReduxState', propertiesState);
    }

    function initReducerState(state, aptoProduct) {
        state = angular.copy(initialState);

        // if product is loaded
        if (aptoProduct) {
            // populate state from product
            state = initGroups(state, aptoProduct.sections);
        }

        // restore persisted properties from session storage
        let sessionState = JSON.parse($window.sessionStorage.getItem('aptoPersistedPropertiesReduxState'));
        if (null !== sessionState) {
            state = update(state, {
                properties: {
                    $set: sessionState.properties
                }
            });
        }

        return state;
    }

    function initConfiguration(state, compressedState) {
        for (let sectionId in compressedState) {
            if (!compressedState.hasOwnProperty(sectionId)) {
                continue;
            }

            for (let elementId in compressedState[sectionId]) {
                if (!compressedState[sectionId].hasOwnProperty(elementId)) {
                    continue;
                }

                const elementState = compressedState[sectionId][elementId];
                if (elementState === true || elementState === true) {
                    continue;
                }

                for (let property in elementState) {
                    if (!elementState.hasOwnProperty(property)) {
                        continue;
                    }

                    state = setPropertyBySectionAndElement(
                        state,
                        sectionId,
                        elementId,
                        property,
                        elementState[property]
                    );
                }
            }
        }
        return state;
    }

    function initGroups(state, sections) {
        let groups = {},
            i, j, section, element, persistGroup;

        // sections
        for (i = 0; i < sections.length; i++) {
            section = sections[i];

            // elements
            for (j = 0; j < section.elements.length; j++) {
                element = section.elements[j];

                // skip, if no properties or customProperties
                if (
                    typeof element.definition.properties === 'undefined' ||
                    typeof element.customProperties === 'undefined'
                ) {
                    continue;
                }

                // get persist group value
                persistGroup = getCustomPropertyByKey(element.customProperties, 'persistGroup');

                // skip, if no persistGroup is set
                if (null === persistGroup) {
                    continue;
                }

                if (Object.keys(element.definition.properties).length > 0) {
                    // create section if not existing
                    if (!groups[section.id]) {
                        groups[section.id] = {};
                    }

                    // create element
                    groups[section.id][element.id] = persistGroup;
                }
            }
        }

        state = update(state, {
            groups: {
                $set: groups
            }
        });

        return state;
    }

    function getCustomPropertyByKey(customProperties, key) {
        for (let i = 0; i < customProperties.length; i++) {
            if (customProperties[i].key === key) {
                return customProperties[i].value;
            }
        }

        return null;
    }

    function getGroupBySectionAndElement(state, sectionId, elementId) {
        if (
            !state.groups.hasOwnProperty(sectionId) ||
            !state.groups[sectionId].hasOwnProperty(elementId)
        ) {
            return null;
        }

        return state.groups[sectionId][elementId];
    }


    function setPropertyByGroup(state, group, property, value) {
        if (!state.properties.hasOwnProperty(group)) {
            // create new entry for group
            state = update(state, {
                properties: {
                    [group]: {
                        $set: {
                            [property]: value
                        }
                    }
                }
            });
        } else {
            // create new entry for property within existing group
            state = update(state, {
                properties: {
                    [group]: {
                        [property]: {
                            $set: value
                        }
                    }
                }
            });
        }

        return state;
    }

    function setPropertyBySectionAndElement(state, sectionId, elementId, property, value) {
        let group = getGroupBySectionAndElement(state, sectionId, elementId);
        if (null === group) {
            return state;
        }

        return setPropertyByGroup(state, group, property, value);
    }

    function persistedProperties(state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case 'APTO_CONFIGURATION_INIT_PRODUCT': {
                state = initReducerState(state, action.payload.product);

                if (action.payload.configuration) {
                    state = initConfiguration(state, action.payload.configuration.state)
                }
                savePropertiesToSessionStorage(state);
                return state;
            }
            case 'APTO_CONFIGURATION_GET_CONFIGURATION_STATE_FULFILLED': {
                if (!action.payload.data.result.intention.set) {
                    return state;
                }

                const propertyValues = action.payload.data.result.intention.set;
                for (let i = 0; i < propertyValues.length; i++) {
                    const propertyValue = propertyValues[i];
                    state = setPropertyBySectionAndElement(
                        state,
                        propertyValue.sectionId,
                        propertyValue.elementId,
                        propertyValue.property,
                        propertyValue.value
                    );
                }
                savePropertiesToSessionStorage(state);

                return state;
            }
            case getType('CLEAR_SESSION_STORAGE'): {
                $window.sessionStorage.setItem('aptoPersistedPropertiesReduxState', null);
                return initReducerState(state);
            }
        }

        return state;
    }

    self.getProperties = function (state, sectionId, elementId) {
        let properties = getPropertiesBySectionAndElement(state, sectionId, elementId);
        return null === properties ? {} : properties;
    };

    AptoReducersProvider.addReducer('persistedProperties', persistedProperties);

    self.$get = function() {};
};

PersistedPropertiesReducer.$inject = PersistedPropertiesReducerInject;

export default ['PersistedPropertiesReducer', PersistedPropertiesReducer];
