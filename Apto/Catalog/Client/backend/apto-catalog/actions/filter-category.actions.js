const FilterCategoryActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const FilterCategoryActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_FILTER_CATEGORY_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchFilterCategories(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('FETCH_FILTER_CATEGORIES'),
                payload: MessageBusFactory.query('FindFilterCategories', [searchString])
            });
        };
    }

    function fetchFilterCategoryDetail(id) {
        return {
            type: getType('FETCH_FILTER_CATEGORY_DETAIL'),
            payload: MessageBusFactory.query('FindFilterCategory', [id])
        }
    }

    function saveFilterCategory(filterCategoryDetail) {
        return dispatch => {
            let commandArguments = [];

            commandArguments.push(filterCategoryDetail.name);
            commandArguments.push(filterCategoryDetail.identifier);
            commandArguments.push(filterCategoryDetail.position);

            if(typeof filterCategoryDetail.id !== "undefined") {
                commandArguments.unshift(filterCategoryDetail.id);
                return dispatch({
                    type: getType('UPDATE_FILTER_CATEGORY'),
                    payload: MessageBusFactory.command('UpdateFilterCategory', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_FILTER_CATEGORY'),
                payload: MessageBusFactory.command('AddFilterCategory', commandArguments)
            });
        }
    }

    function removeFilterCategory(id) {
        return {
            type: getType('REMOVE_FILTER_CATEGORY'),
            payload: MessageBusFactory.command('RemoveFilterCategory', [id])
        }
    }

    function filterCategoryDetailReset() {
        return {
            type: getType('FILTER_CATEGORY_DETAIL_RESET')
        }
    }

    return {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        fetchFilterCategoryDetail: fetchFilterCategoryDetail,
        saveFilterCategory: saveFilterCategory,
        filterCategoryDetailReset: filterCategoryDetailReset,
        removeFilterCategory: removeFilterCategory,
        fetchFilterCategories: fetchFilterCategories
    };
};

FilterCategoryActions.$inject = FilterCategoryActionsInject;

export default ['FilterCategoryActions', FilterCategoryActions];