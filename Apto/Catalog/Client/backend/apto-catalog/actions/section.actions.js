const SectionActionsInject = ['MessageBusFactory'];
const SectionActions = function (MessageBusFactory) {
    function getType(type) {
        return 'APTO_SECTION_' + type;
    }

    function fetchDetail(sectionId) {
        return {
            type: getType('FETCH_DETAIL'),
            payload: MessageBusFactory.query('FindSection', [sectionId])
        }
    }

    function fetchElements(sectionId) {
        return {
            type: getType('FETCH_ELEMENTS'),
            payload: MessageBusFactory.query('FindSectionElements', [sectionId])
        }
    }

    function fetchPrices(sectionId) {
        return {
            type: getType('FETCH_PRICES'),
            payload: MessageBusFactory.query('FindSectionPrices', [sectionId])
        }
    }

    function fetchDiscounts(sectionId) {
        return {
            type: getType('FETCH_DISCOUNTS'),
            payload: MessageBusFactory.query('FindSectionDiscounts', [sectionId])
        }
    }

    function fetchGroups() {
        return {
            type: getType('FETCH_GROUPS'),
            payload: MessageBusFactory.query('FindGroups', [])
        }
    }

    function fetchCustomProperties(sectionId) {
        return {
            type: getType('FETCH_CUSTOM_PROPERTIES'),
            payload: MessageBusFactory.query('FindSectionCustomProperties', [sectionId])
        }
    }

    function setDetailValue(key, value) {
        return {
            type: getType('SET_DETAIL_VALUE'),
            payload: {
                key: key,
                value: value
            }
        }
    }


    function reset() {
        return {
            type: getType('RESET')
        }
    }

    return {
        fetchDetail: fetchDetail,
        fetchElements: fetchElements,
        fetchPrices: fetchPrices,
        fetchDiscounts: fetchDiscounts,
        fetchGroups: fetchGroups,
        fetchCustomProperties: fetchCustomProperties,
        reset: reset,
        setDetailValue: setDetailValue
    }
};

SectionActions.$inject = SectionActionsInject;

export default ['SectionActions', SectionActions];
