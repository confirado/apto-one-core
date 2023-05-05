const IndexAuthActionsInject = ['XDomainRequestFactory', 'ConfigurationService'];
const IndexAuthActions = function(XDomainRequestFactory, ConfigurationService) {
    const TYPE_NS = 'APTO_INDEX_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function shopLogin(email, password) {
        return (dispatch, getState) => {
            // please use state in action creators rarely, this is normally not the place where we access the redux store
            if (getState().index.shopConnectorConfigured) {
                return dispatch({
                    type: getType('SHOP_LOGIN'),
                    payload: XDomainRequestFactory.query('Login', {email: email, password: password})
                }).then(ConfigurationService.fetchCurrentStatePrice);
            } else {
                dispatch({
                    type: getType('SHOP_SESSION_NOT_AVAILABLE')
                });
            }
        };
    }

    function shopLogout() {
        return (dispatch, getState) => {
            // please use state in action creators rarely, this is normally not the place where we access the redux store
            if (getState().index.shopConnectorConfigured) {
                return dispatch({
                    type: getType('SHOP_LOGOUT'),
                    payload: XDomainRequestFactory.query('Logout', [])
                }).then(ConfigurationService.fetchCurrentStatePrice);
            } else {
                dispatch({
                    type: getType('SHOP_SESSION_NOT_AVAILABLE')
                });
            }
        };
    }

    return {
        shopLogin: shopLogin,
        shopLogout: shopLogout
    };
};

IndexAuthActions.$inject = IndexAuthActionsInject;

export default ['IndexAuthActions', IndexAuthActions];