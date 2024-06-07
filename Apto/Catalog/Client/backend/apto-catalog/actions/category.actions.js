const CategoryActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const CategoryActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_CATEGORY_';
    const factory = {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        categoryDetailFetch: categoryDetailFetch,
        categoryDetailSave: categoryDetailSave,
        categoryDetailReset: categoryDetailReset,
        categoryRemove: categoryRemove,
        categoryTreeFetch: categoryTreeFetch,
        fetchCustomProperties: fetchCustomProperties,
        addCategoryCustomProperty: addCategoryCustomProperty,
        removeCategoryCustomProperty: removeCategoryCustomProperty,
        setDetailValue: setDetailValue,

    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function categoryTreeFetch(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('TREE_FETCH'),
                payload: MessageBusFactory.query('FindCategoryTree', [searchString])
            });
        };
    }

    function categoryDetailFetch(id) {
        return {
            type: getType('CATEGORY_DETAIL_FETCH'),
            payload: MessageBusFactory.query('FindCategory', [id])
        }
    }

    function categoryDetailSave(categoryDetail) {
        return dispatch => {
            let commandArguments = [];
            if(typeof categoryDetail.previewImage === "undefined") {
                categoryDetail.previewImage = null;
            }
            if(typeof categoryDetail.position === "undefined") {
                categoryDetail.position = 0;
            }

            commandArguments.push(categoryDetail.name);
            commandArguments.push(categoryDetail.description);
            commandArguments.push(categoryDetail.position);
            commandArguments.push(categoryDetail.parent);
            commandArguments.push(categoryDetail.previewImage);

            if(typeof categoryDetail.id !== "undefined") {
                commandArguments.unshift(categoryDetail.id);
                return dispatch({
                    type: getType('CATEGORY_DETAIL_UPDATE'),
                    payload: MessageBusFactory.command('UpdateCategory', commandArguments)
                });
            }

            return dispatch({
                type: getType('CATEGORY_DETAIL_ADD'),
                payload: MessageBusFactory.command('AddCategory', commandArguments)
            });
        }
    }

    function categoryRemove(id) {
        return {
            type: getType('CATEGORY_REMOVE'),
            payload: MessageBusFactory.command('RemoveCategory', [id])
        }
    }

    function categoryDetailReset() {
        return {
            type: getType('CATEGORY_DETAIL_RESET')
        }
    }

    function addCategoryCustomProperty(categoryId, key, value, translatable) {
        return {
            type: getType('ADD_CATEGORY_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('AddCategoryCustomProperty', [categoryId, key, value, translatable])
        }
    }

    function removeCategoryCustomProperty(categoryId, id) {
        return {
            type: getType('REMOVE_CATEGORY_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('RemoveCategoryCustomProperty', [categoryId, id])
        }
    }

    function fetchCustomProperties(categoryId) {
        return {
            type: getType('FETCH_CUSTOM_PROPERTIES'),
            payload: MessageBusFactory.query('FindCategoryCustomProperties', [categoryId])
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

    return factory;
};

CategoryActions.$inject = CategoryActionsInject;

export default ['CategoryActions', CategoryActions];
