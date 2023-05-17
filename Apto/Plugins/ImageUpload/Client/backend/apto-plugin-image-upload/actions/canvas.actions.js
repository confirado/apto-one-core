const ActionsInject = ['MessageBusFactory', 'PageHeaderActions'];
const Actions = function (MessageBusFactory, PageHeaderActions) {
    const TYPE_NS = 'PLUGIN_IMAGE_UPLOAD_CANVAS_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchCanvas(id) {
        return {
            type: getType('FETCH_CANVAS'),
            payload: MessageBusFactory.query('ImageUploadFindCanvas', [id])
        }
    }

    function removeCanvas(id) {
        return {
            type: getType('REMOVE_CANVAS'),
            payload: MessageBusFactory.command('ImageUploadRemoveCanvas', [id])
        }
    }

    function fetchCanvasList(pageNumber, recordsPerPage, searchString) {
        if (typeof searchString === "undefined") {
            searchString = '';
        }

        return {
            type: getType('FETCH_CANVAS_LIST'),
            payload: MessageBusFactory.query('ImageUploadFindCanvasList', [pageNumber, recordsPerPage, searchString])
        }
    }

    function fetchCanvasIds() {
        return {
            type: getType('FETCH_CANVAS_IDS'),
            payload: MessageBusFactory.query('ImageUploadFindCanvasIds', [])
        }
    }

    function saveCanvas(details) {
        return dispatch => {
            let commandArguments = [];

            commandArguments.push(details.identifier);
            commandArguments.push(details.imageSettings);
            commandArguments.push(details.textSettings);
            commandArguments.push(details.areaSettings);
            commandArguments.push(details.priceSettings);

            if(typeof details.id !== "undefined") {
                commandArguments.unshift(details.id);
                return dispatch({
                    type: getType('UPDATE_CANVAS'),
                    payload: MessageBusFactory.command('ImageUploadUpdateCanvas', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_CANVAS'),
                payload: MessageBusFactory.command('ImageUploadAddCanvas', commandArguments)
            });
        }
    }

    function resetCanvas(id) {
        return {
            type: getType('RESET_CANVAS')
        }
    }

    return {
        setPageNumber: PageHeaderActions.setPageNumber(TYPE_NS),
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        fetchCanvasList: fetchCanvasList,
        fetchCanvasIds: fetchCanvasIds,
        fetchCanvas: fetchCanvas,
        removeCanvas: removeCanvas,
        saveCanvas: saveCanvas,
        resetCanvas: resetCanvas
    };
};

Actions.$inject = ActionsInject;

export default ['ImageUploadCanvasActions', Actions];
