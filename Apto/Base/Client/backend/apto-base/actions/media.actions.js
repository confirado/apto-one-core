const MediaActionsInject = ['$ngRedux', 'MessageBusFactory', 'APTO_ENVIRONMENT'];
const MediaActions = function($ngRedux, MessageBusFactory, APTO_ENVIRONMENT) {
    const TYPE_NS = 'APTO_MEDIA_';
    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchMediaFiles(directory) {
        return dispatch => {
            if (typeof directory === 'undefined') {
                directory = '';
            }

            const fetchMediaFilesPromise = dispatch({
                type: getType('FETCH_MEDIA_FILES'),
                payload: MessageBusFactory.query('ListMediaFiles', [directory])
            });

            fetchMediaFilesPromise.then(() => {
                dispatch(setCurrentDirectory(directory));
            });

            return fetchMediaFilesPromise;
        }
    }

    function setCurrentDirectory(directory) {
        return {
            type: getType('SET_CURRENT_DIRECTORY'),
            payload: directory
        }
    }

    function mediaDirectoryRemove(directory, path) {
        return dispatch => {
            const removeDirectoryPromise = dispatch({
                type: getType('MEDIA_DIRECTORY_REMOVE'),
                payload: MessageBusFactory.command('RemoveMediaFileDirectory', [path])
            });

            removeDirectoryPromise.then(() => {
                dispatch(fetchMediaFiles(directory));
            });

            return removeDirectoryPromise;
        }
    }

    function removeDirectoryRecursive(directory, path) {
        return dispatch => {
            const removeDirectoryPromise = dispatch({
                type: getType('REMOVE_DIRECTORY_RECURSIVE'),
                payload: MessageBusFactory.sendMessage(
                    APTO_ENVIRONMENT.routes.routeUrls.base_url + '/media/remove-directory',
                    {'directory': path}
                )
            });

            removeDirectoryPromise.then(() => {
                dispatch(fetchMediaFiles(directory));
            });

            return removeDirectoryPromise;
        }
    }

    function mediaFileRemove(directory, path) {
        return dispatch => {
            const removeFilePromise = dispatch({
                type: getType('MEDIA_FILE_REMOVE'),
                payload: MessageBusFactory.command('RemoveMediaFileByName', [path])
            });

            removeFilePromise.then(() => {
                dispatch(fetchMediaFiles(directory));
            });

            return removeFilePromise;
        }
    }

    function mediaDirectoryRename(parentDirectory, directory, newName) {
        return dispatch => {
            const renameDirectoryPromise = dispatch({
                type: getType('MEDIA_DIRECTORY_RENAME'),
                payload: MessageBusFactory.command('RenameMediaFileDirectory', [directory, newName])
            });

            renameDirectoryPromise.then(() => {
                dispatch(fetchMediaFiles(parentDirectory));
            });

            return renameDirectoryPromise;
        }
    }

    function mediaFileUpload(path, files, id, overwriteExisting) {
        if (overwriteExisting) {
            overwriteExisting = 1;
        } else {
            overwriteExisting = 0;
        }

        return dispatch => {
            let config = '';
            dispatch({
                type: getType('UPLOAD_FILES_PENDING')
            });
            dispatch({
                type: getType('UPLOAD_FILES_PROGRESS_START'),
                payload: {id: id, total: 0, loaded: 0}
            });
            return MessageBusFactory.uploadCommand('UploadMediaFile', [path, overwriteExisting], files, config).then(
                (response) => {
                    dispatch({
                        type: getType('UPLOAD_FILES_FULFILLED'),
                        payload: response
                    });
                    dispatch({
                        type: getType('UPLOAD_FILES_PROGRESS_END'),
                        payload: {id: id, total: 0, loaded: 0}
                    });
                },
                (error) => {
                    dispatch({
                        type: getType('UPLOAD_FILES_REJECTED'),
                        payload: error
                    });
                    dispatch({
                        type: getType('UPLOAD_FILES_PROGRESS_END'),
                        payload: {id: id, total: 0, loaded: 0}
                    });
                },
                (evt) => {
                    dispatch({
                        type: getType('UPLOAD_FILES_PROGRESS_UPDATE'),
                        payload: {id: id, total: evt.total, loaded: evt.loaded}
                    });
                }
            );
        };
    }

    function addDirectoryDetailSave(directory, name) {
        return dispatch => {
            if (typeof directory === 'undefined') {
                directory = '';
            }

            const addDirectoryPromise = dispatch({
                type: getType('ADD_DIRECTORY'),
                payload: MessageBusFactory.command('AddMediaFileDirectory', [directory + '/' + name])
            });

            addDirectoryPromise.then(() => {
                dispatch(fetchMediaFiles(directory));
            });

            return addDirectoryPromise;
        }
    }

    function addDirectoryDetailReset() {
        return {
            type: getType('ADD_DIRECTORY_DETAIL_RESET')
        }
    }

    return {
        fetchMediaFiles: fetchMediaFiles,
        setCurrentDirectory: setCurrentDirectory,
        mediaDirectoryRemove: mediaDirectoryRemove,
        mediaDirectoryRename: mediaDirectoryRename,
        removeDirectoryRecursive: removeDirectoryRecursive,
        mediaFileRemove: mediaFileRemove,
        mediaFileUpload: mediaFileUpload,
        addDirectoryDetailSave: addDirectoryDetailSave,
        addDirectoryDetailReset: addDirectoryDetailReset
    };
};

MediaActions.$inject = MediaActionsInject;

export default ['MediaActions', MediaActions];