const ActionsInject = ['MessageBusFactory'];
const Actions = function (MessageBusFactory) {
    const TYPE_NS = 'APTO_FRONTEND_USERS_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function findCurrentFrontendUser(userName) {
        return {
            type: getType('FIND_CURRENT_FRONTEND_USER'),
            payload: MessageBusFactory.query('FindCurrentFrontendUser', [userName])
        }
    }

    function logoutCurrentFrontendUser() {
        return {
            type: getType('LOGOUT_CURRENT_USER')
        }
    }

    return {
        findCurrentFrontendUser: findCurrentFrontendUser,
        logoutCurrentFrontendUser: logoutCurrentFrontendUser
    };
};

Actions.$inject = ActionsInject;

export default ['FrontendUsersActions', Actions];