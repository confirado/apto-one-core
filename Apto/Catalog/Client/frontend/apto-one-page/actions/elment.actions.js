const ElementActionsInject = [];
const ElementActions = function() {
    const TYPE_NS = 'APTO_ONE_PAGE_ELEMENT_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function setSidebarOpen(sidebarOpen) {
        return {
            type: getType('SET_SIDEBAR_OPEN'),
            payload: sidebarOpen
        }
    }


    return {
        setSidebarOpen: setSidebarOpen
    };
};

ElementActions.$inject = ElementActionsInject;

export default ['OnePageElementActions', ElementActions];