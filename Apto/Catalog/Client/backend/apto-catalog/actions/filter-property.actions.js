const FilterPropertyActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const FilterPropertyActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_FILTER_PROPERTY_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchFilterProperties(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('FETCH_FILTER_PROPERTIES'),
                payload: MessageBusFactory.query('FindFilterProperties', [searchString])
            });
        };
    }

    function fetchFilterPropertyDetail(id) {
        return {
            type: getType('FETCH_FILTER_PROPERTY_DETAIL'),
            payload: MessageBusFactory.query('FindFilterProperty', [id])
        }
    }

    function saveFilterProperty(filterPropertyDetail) {
        return dispatch => {
            let commandArguments = [];

            if (typeof filterPropertyDetail.filterCategories === "undefined") {
                filterPropertyDetail.filterCategories = [];
            }

            commandArguments.push(filterPropertyDetail.name);
            commandArguments.push(filterPropertyDetail.identifier);
            commandArguments.push(filterPropertyDetail.filterCategories);

            if(typeof filterPropertyDetail.id !== "undefined") {
                commandArguments.unshift(filterPropertyDetail.id);
                return dispatch({
                    type: getType('UPDATE_FILTER_PROPERTY'),
                    payload: MessageBusFactory.command('UpdateFilterProperty', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_FILTER_PROPERTY'),
                payload: MessageBusFactory.command('AddFilterProperty', commandArguments)
            });
        }
    }

    function removeFilterProperty(id) {
        return {
            type: getType('REMOVE_FILTER_PROPERTY'),
            payload: MessageBusFactory.command('RemoveFilterProperty', [id])
        }
    }

    function filterPropertyDetailReset() {
        return {
            type: getType('FILTER_PROPERTY_DETAIL_RESET')
        }
    }

    function filterPropertyDetailAssignCategories(categories) {
        return {
            type: getType('FILTER_PROPERTY_DETAIL_ASSIGN_CATEGORIES'),
            payload: categories
        }
    }

    return {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        fetchFilterPropertyDetail: fetchFilterPropertyDetail,
        saveFilterProperty: saveFilterProperty,
        filterPropertyDetailReset: filterPropertyDetailReset,
        removeFilterProperty: removeFilterProperty,
        fetchFilterProperties: fetchFilterProperties,
        filterPropertyDetailAssignCategories: filterPropertyDetailAssignCategories
    };
};

FilterPropertyActions.$inject = FilterPropertyActionsInject;

export default ['FilterPropertyActions', FilterPropertyActions];