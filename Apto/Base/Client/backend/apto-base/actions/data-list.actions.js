const DataListActionsInject = [];
const DataListActions = function() {

    function setSelected(typeNs) {
        return function (payload) {
            return {
                type: typeNs + 'SET_SELECTED',
                payload: payload
            }
        }
    }

    return {
        setSelected: setSelected
    };
};

DataListActions.$inject = DataListActionsInject;

export default ['DataListActions', DataListActions];