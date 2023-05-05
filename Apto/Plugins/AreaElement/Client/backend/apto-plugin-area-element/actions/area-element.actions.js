const ActionsInject = ['$ngRedux', 'MessageBusFactory'];
const Actions = function($ngRedux, MessageBusFactory) {
    const TYPE_NS = 'PLUGIN_AREA_ELEMENT_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchPriceMatrices(searchString) {
        return dispatch => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }

            return dispatch({
                type: getType('FETCH_PRICE_MATRICES'),
                payload: MessageBusFactory.query('FindPriceMatrices', [searchString])
            });
        };
    }

    return {
        fetchPriceMatrices: fetchPriceMatrices
    };
};

Actions.$inject = ActionsInject;

export default ['AreaElementActions', Actions];