const PropertyActionsInject = ['MessageBusFactory', 'PageHeaderActions'];
const PropertyActions = function (MessageBusFactory, PageHeaderActions) {
    const TYPE_NS = 'PLUGIN_MATERIAL_PICKER_PROPERTY_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchGroup(id) {
        return {
            type: getType('FETCH_GROUP'),
            payload: MessageBusFactory.query('FindMaterialPickerGroup', [id])
        }
    }

    function fetchGroups(searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_GROUPS'),
            payload: MessageBusFactory.query('FindMaterialPickerGroups', [searchString])
        }
    }

    function fetchGroupsByPage(pageNumber, recordsPerPage, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_GROUPS_BY_PAGE'),
            payload: MessageBusFactory.query('FindMaterialPickerGroupsByPage', [pageNumber, recordsPerPage, searchString])
        };
    }

    function saveGroup(group) {
        return dispatch => {
            let commandArguments = [];

            if (typeof group.allowMultiple === "undefined") {
                group.allowMultiple = false;
            }

            commandArguments.push(group.name);
            commandArguments.push(group.allowMultiple);

            if(typeof group.id !== "undefined") {
                commandArguments.unshift(group.id);
                return dispatch({
                    type: getType('UPDATE_GROUP'),
                    payload: MessageBusFactory.command('UpdateMaterialPickerGroup', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_GROUP'),
                payload: MessageBusFactory.command('AddMaterialPickerGroup', commandArguments)
            });
        }
    }

    function removeGroup(id) {
        return {
            type: getType('REMOVE_GROUP'),
            payload: MessageBusFactory.command('RemoveMaterialPickerGroup', [id])
        }
    }

    function resetGroup() {
        return {
            type: getType('RESET_GROUP')
        }
    }

    function fetchGroupProperties(id, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_GROUP_PROPERTIES'),
            payload: MessageBusFactory.query('FindMaterialPickerGroupProperties', [id, searchString])
        }
    }

    function addGroupProperty(id, propertyName) {
        return {
            type: getType('ADD_GROUP_PROPERTY'),
            payload: MessageBusFactory.command('AddMaterialPickerGroupProperty', [id, propertyName])
        }
    }

    function removeGroupProperty(id, propertyId) {
        return {
            type: getType('REMOVE_GROUP_PROPERTY'),
            payload: MessageBusFactory.command('RemoveMaterialPickerGroupProperty', [id, propertyId])
        }
    }

    function fetchProperty(id) {
        return {
            type: getType('FETCH_PROPERTY'),
            payload: MessageBusFactory.query('FindMaterialPickerProperty', [id])
        }
    }

    function saveProperty(property) {
        let commandArguments = [];

        commandArguments.push(property.id);
        commandArguments.push(property.name);

        return {
            type: getType('UPDATE_PROPERTY'),
            payload: MessageBusFactory.command('UpdateMaterialPickerProperty', commandArguments)
        }
    }

    function resetProperty() {
        return {
            type: getType('RESET_PROPERTY')
        }
    }

    function fetchPropertyCustomProperties(propertyId, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_PROPERTY_CUSTOM_PROPERTIES'),
            payload: MessageBusFactory.query('FindMaterialPickerPropertyCustomProperties', [propertyId, searchString])
        }
    }

    function addPropertyCustomProperty(propertyId, key, value) {
        return {
            type: getType('ADD_PROPERTY_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('AddMaterialPickerPropertyCustomProperty', [propertyId, key, value])
        }
    }

    function removePropertyCustomProperty(propertyId, key) {
        return {
            type: getType('REMOVE_PROPERTY_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('RemoveMaterialPickerPropertyCustomProperty', [propertyId, key])
        }
    }

    function setGroupPropertyIsDefault(id, propertyId, isDefault) {
        return {
            type: getType('SET_GROUP_PROPERTY_IS_DEFAULT'),
            payload: MessageBusFactory.command('SetMaterialPickerGroupPropertyIsDefault', [id, propertyId, isDefault])
        }
    }


    return {
        setPageNumber: PageHeaderActions.setPageNumber(TYPE_NS),
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        fetchGroup: fetchGroup,
        fetchGroups: fetchGroups,
        fetchGroupsByPage: fetchGroupsByPage,
        saveGroup: saveGroup,
        removeGroup: removeGroup,
        resetGroup: resetGroup,
        fetchGroupProperties: fetchGroupProperties,
        addGroupProperty: addGroupProperty,
        removeGroupProperty: removeGroupProperty,
        fetchProperty: fetchProperty,
        saveProperty: saveProperty,
        resetProperty: resetProperty,
        fetchPropertyCustomProperties: fetchPropertyCustomProperties,
        addPropertyCustomProperty: addPropertyCustomProperty,
        removePropertyCustomProperty: removePropertyCustomProperty,
        setGroupPropertyIsDefault: setGroupPropertyIsDefault
    };
};

PropertyActions.$inject = PropertyActionsInject;

export default ['MaterialPickerPropertyActions', PropertyActions];