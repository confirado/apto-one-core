import BrowserDetect from 'bowser';

const XDomainRequestFactoryInject = ['$http', '$sce', 'APTO_SHOP_CONTEXT', 'LanguageFactory'];
const XDomainRequestFactory = function($http, $sce, APTO_SHOP_CONTEXT, LanguageFactory) {
    const preIE10 = BrowserDetect.msie && parseFloat(BrowserDetect.version) < 10;

    function query(query, queryArguments) {
        let url = LanguageFactory.translate(APTO_SHOP_CONTEXT.connectorUrl);
        let data = {
            query: query,
            arguments: queryArguments
        };

        return sendMessage(url, data);
    }

    function sendMessage(url, data) {
        let request = null;
        let config = {
            withCredentials: true,
            params: {
                encode: 'json',
                data: data
            }
        };
        if (preIE10) {
            // @todo: requests use GET method by design, parameters with secret data could be logged in apache etc.
            config.params.encode = 'jsonp';
            config.jsonpCallbackParam = 'callback';
            request = $http.jsonp($sce.trustAsResourceUrl(url), config);
        } else {
            request = $http.post(url, config.params, {
                withCredentials: true
            });
        }

        return request;
    }

    return {
        query: query
    };
};

XDomainRequestFactory.$inject = XDomainRequestFactoryInject;

export default ['XDomainRequestFactory', XDomainRequestFactory];