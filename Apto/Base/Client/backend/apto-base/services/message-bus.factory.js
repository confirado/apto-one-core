import MessageLogDetailTemplate from '../pages/index/message-log-detail.controller.html';
import MessageLogDetailController from '../pages/index/message-log-detail.controller';

const MessageBusFactoryInject = ['$http', '$ngRedux', '$mdDialog', 'MessageBusActions', 'Upload', 'APTO_ENVIRONMENT'];
const MessageBusFactory = function($http, $ngRedux, $mdDialog, MessageBusActions, Upload, APTO_ENVIRONMENT) {
    function command(command, commandArguments, config) {
        let url = APTO_ENVIRONMENT.routes.routeUrls['messagebus_command'];
        let data = {
            command: command,
            arguments: commandArguments
        };

        return sendMessage(url, data, config);
    }

    function uploadCommand(command, commandArguments, files, config) {
        let url = APTO_ENVIRONMENT.routes.routeUrls['messagebus_command'];
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
        let url = APTO_ENVIRONMENT.routes.routeUrls['messagebus_query'];
        let data = {
            query: query,
            arguments: queryArguments
        };

        return sendMessage(url, data, config);
    }

    function batchExecute(messages, config) {
        let url = APTO_ENVIRONMENT.routes.routeUrls['messagebus_batchexecute'];
        let data = {
            messages: messages
        };

        return sendMessage(url, data, config);
    }

    function messagesIsGranted(commands, queries, config) {
        let url = APTO_ENVIRONMENT.routes.routeUrls['messagebus_messagesisgranted'];
        let data = {
            commands: commands,
            queries: queries
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
        if(typeof response.data.message !== "undefined") {
            $ngRedux.dispatch(MessageBusActions.addMessageLogMessage(response.data.message));
            if (true === response.data.message.error) {
                showErrorDialog(response.data.message);
            }
        }
        return response;
    }

    function handleError(response) {
        if(typeof response.data.message !== "undefined") {
            $ngRedux.dispatch(MessageBusActions.addMessageLogMessage(response.data.message));
            if (true === response.data.message.error) {
                showErrorDialog(response.data.message);
            }
        }
        return response;
    }

    function showErrorDialog(message, $event) {
        let parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: MessageLogDetailTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            multiple: true,
            locals: {
                message: message
            },
            controller: MessageLogDetailController
        });
    }

    return {
        command: command,
        uploadCommand: uploadCommand,
        query: query,
        batchExecute: batchExecute,
        messagesIsGranted: messagesIsGranted,
        sendMessage: sendMessage
    };
};

MessageBusFactory.$inject = MessageBusFactoryInject;

export default ['MessageBusFactory', MessageBusFactory];