import update from 'immutability-helper';

const MediaReducerInject = ['AptoReducersProvider'];
const MediaReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_MEDIA_';
    const initialState = {
        pageHeaderConfig: {
            title: 'Medien',
            toggleSideBarRight: {
                show: true
            },
            add: {
                show: true,
                aclMessagesRequired: {
                    commands: ['AddMediaFileDirectory'],
                    queries: ['ListMediaFiles']
                }
            }
        },
        currentDirectory: '',
        mediaFiles: [],
        uploads: {},
        uploadProgress: 0,
        runningUploads: 0,
        addDirectoryDetails: {
            name: ''
        },
        removeFileAclMessagesRequired: {
            commands: ['RemoveMediaFileByName', 'RemoveMediaFileDirectory'],
            queries: ['ListMediaFiles']
        }
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function updateRunningUploads(state, action) {
        let uploads = angular.copy(state.uploads),
            total = 0,
            loaded = 0;

        uploads[action.payload.id] = {
            total: action.payload.total,
            loaded: action.payload.loaded
        };

        for (let id in uploads) {
            if (uploads.hasOwnProperty(id)) {
                total += uploads[id].total;
                loaded += uploads[id].loaded;
            }
        }

        return update(state, {
            uploads: {
                $set: uploads
            },
            uploadProgress: {
                $set: total === 0 ? 0 : parseInt(Math.round(loaded / total * 100))
            }
        });
    }

    this.media = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            // content list
            case getType('FETCH_MEDIA_FILES_FULFILLED'):
                newState = update(state, {
                    mediaFiles: {
                        $set: action.payload.data.result
                    }
                });

                return newState;
            // change current directory
            case getType('SET_CURRENT_DIRECTORY'):
                newState = update(state, {
                    currentDirectory: {
                        $set: action.payload
                    }
                });
                return newState;

            case getType('UPLOAD_FILES_PROGRESS_START'):
                //newState = updateRunningUploads(state, action);
                newState = update(state, {
                    runningUploads: {
                        $set: state.runningUploads + 1
                    }
                });
                return newState;

            case getType('UPLOAD_FILES_PROGRESS_UPDATE'):
                newState = updateRunningUploads(state, action);
                return newState;

            case getType('UPLOAD_FILES_PROGRESS_END'):
                newState = updateRunningUploads(state, action);
                newState = update(newState, {
                    runningUploads: {
                        $set: state.runningUploads - 1
                    }
                });
                // no running uploads, tidy up a bit
                if (newState.runningUploads === 0) {
                    newState = update(newState, {
                        uploads: {
                            $set: {}
                        },
                        uploadProgress: {
                            $set: 0
                        }
                    });
                }
                return newState;
            case getType('ADD_DIRECTORY_DETAIL_RESET'):
                newState = update(state, {
                    addDirectoryDetails: {
                        $set: angular.copy(initialState.addDirectoryDetails)
                    }
                });

                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('media', this.media);

    this.$get = function() {};
};

MediaReducer.$inject = MediaReducerInject;

export default ['MediaReducer', MediaReducer];