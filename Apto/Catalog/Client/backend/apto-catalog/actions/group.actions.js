const GroupActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const GroupActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_GROUP_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchGroups(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }
            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            return dispatch({
                type: getType('FETCH_GROUPS'),
                payload: MessageBusFactory.query('FindGroups', [searchString])
            });
        };
    }

    function fetchGroupDetail(id) {
        return {
            type: getType('FETCH_GROUP_DETAIL'),
            payload: MessageBusFactory.query('FindGroup', [id])
        }
    }

    function saveGroup(groupDetail) {
        return dispatch => {
            let commandArguments = [];

            commandArguments.push(groupDetail.name);
            commandArguments.push(groupDetail.position);
            commandArguments.push(groupDetail.identifier);

            if(typeof groupDetail.id !== "undefined") {
                commandArguments.unshift(groupDetail.id);
                return dispatch({
                    type: getType('UPDATE_GROUP'),
                    payload: MessageBusFactory.command('UpdateGroup', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_GROUP'),
                payload: MessageBusFactory.command('AddGroup', commandArguments)
            });
        }
    }

    function removeGroup(id) {
        return {
            type: getType('REMOVE_GROUP'),
            payload: MessageBusFactory.command('RemoveGroup', [id])
        }
    }

    function groupDetailReset() {
        return {
            type: getType('GROUP_DETAIL_RESET')
        }
    }

    return {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        fetchGroupDetail: fetchGroupDetail,
        saveGroup: saveGroup,
        groupDetailReset: groupDetailReset,
        removeGroup: removeGroup,
        fetchGroups: fetchGroups
    };
};

GroupActions.$inject = GroupActionsInject;

export default ['GroupActions', GroupActions];