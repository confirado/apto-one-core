const ActionsInject = ['MessageBusFactory'];
const Actions = function (MessageBusFactory) {
    const TYPE_NS = 'APTO_PLUGIN_PARTS_LIST_PART_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchCSV(productId, state, locale, currency, customerGroupId) {
        return {
            type: getType('FETCH_CSV'),
            payload: MessageBusFactory.query('AptoPartsListFindPartsListCsv', [productId, makeFileName(15), state, locale, currency, customerGroupId])
        }
    }

    function makeFileName(length) {
        let result           = '';
        const characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        for ( let i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    return {
        fetchCSV: fetchCSV
    };
};

Actions.$inject = ActionsInject;

export default ['AptoPartsListPartActions', Actions];