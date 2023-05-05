const MessageBusFactoryInject = ['$http', 'Upload'];
const MessageBusFactory = function($http, Upload) {
    function command(command, commandArguments, config) {
        let url = APTO_API.command;
        let data = {
            command: command,
            arguments: commandArguments
        };

        return sendMessage(url, data, config);
    }

    function uploadCommand(command, commandArguments, files, config) {
        let url = APTO_API.command;
        let data = {
            file: files,
            command: command,
            arguments: commandArguments
        };

        return Upload.upload({
            url: url,
            data: data
        });
    }

    function query(query, queryArguments, config) {
        let url = APTO_API.query;
        let data = {
            query: query,
            arguments: queryArguments
        };

        return sendMessage(url, data, config);
    }

    function batchExecute(messages, config) {
        let url = APTO_API.batchExecute;
        let data = {
            messages: messages
        };

        return sendMessage(url, data, config);
    }

    function setLocale(locale, config) {
        let url = APTO_API.setLocale;
        let data = {
            _locale: locale
        };

        return sendMessage(url, data, config);
    }

    function sendMessage(url, data, config) {
        if(typeof config === "undefined") {
            return $http.post(url, data).then(handleSuccess, handleError);
        }
        else {
            return $http.post(url, data, config).then(handleSuccess, handleError);
        }
    }

    function handleSuccess(response) {
        return response;
    }

    function handleError(response) {
        return response;
    }

    return {
        command: command,
        uploadCommand: uploadCommand,
        query: query,
        batchExecute: batchExecute,
        setLocale: setLocale
    };
};

MessageBusFactory.$inject = MessageBusFactoryInject;

export default ['MessageBusFactory', MessageBusFactory];