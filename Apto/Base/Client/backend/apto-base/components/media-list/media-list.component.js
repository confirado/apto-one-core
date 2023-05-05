import ContainerController from '../apto-container.controller';
import Template from './media-list.component.html';
import RenameDialogTemplate from './dialog/rename-dialog.controller.html';
import RenameDialogController from './dialog/rename-dialog.controller';
import DialogTemplate from "../media-select/dialog/dialog.controller.html";
import DialogController from "../media-select/dialog/dialog.controller";

const ControllerInject = ['$ngRedux', '$document', '$mdDialog', 'MediaActions', 'AclIsGrantedFactory', 'APTO_ENVIRONMENT', 'APTO_USER_SETTINGS', 'MessageBusFactory'];
class Controller extends ContainerController {
    constructor($ngRedux, $document, $mdDialog, MediaActions, AclIsGrantedFactory, APTO_ENVIRONMENT, APTO_USER_SETTINGS, MessageBusFactory) {
        // call parent constructor
        super($ngRedux);

        // set services
        this.mdDialog = $mdDialog;
        this.mediaActions = MediaActions;
        this.document = $document;
        this.messageBusFactory = MessageBusFactory;

        // set service functions
        this.aclAllGranted = AclIsGrantedFactory.allGranted;

        // set properties
        this.maxFiles = APTO_ENVIRONMENT.upload.maxFiles;
        this.maxFileSize = APTO_ENVIRONMENT.upload.maxFileSize;
        this.maxTotalSize = APTO_ENVIRONMENT.upload.maxTotalSize;
        this.mediaPath = APTO_ENVIRONMENT.routes.routeUrls.media_url;
        this.mediaListPagination = {
            recordsPerPage: 50,
            currentPage: 1
        };
        this.errors = {
            maxFiles: false,
            maxFileSize: false,
            maxTotalSize: false
        };

        this.allowOverwriteExisting = false;
        if (APTO_USER_SETTINGS.mediaList && APTO_USER_SETTINGS.mediaList.allowOverwriteExisting) {
            this.allowOverwriteExisting = APTO_USER_SETTINGS.mediaList.allowOverwriteExisting;
        }
    }

    connectProps() {
        // call parent
        super.connectProps();

        // map state
        return (state) => {
            return {
                mediaFiles: state.media.mediaFiles,
                runningUploads: state.media.runningUploads,
                uploadProgress: state.media.uploadProgress,
                currentDirectory: state.media.currentDirectory,
                removeFileAclMessagesRequired: state.media.removeFileAclMessagesRequired
            }
        }
    }

    connectActions() {
        // call parent
        super.connectActions();

        // map actions
        return {
            fetchMediaFiles: this.mediaActions.fetchMediaFiles,
            removeDirectoryRecursive: this.mediaActions.removeDirectoryRecursive,
            mediaFileRemove: this.mediaActions.mediaFileRemove,
            mediaFileUpload: this.mediaActions.mediaFileUpload,
            mediaDirectoryRename: this.mediaActions.mediaDirectoryRename,
            addDirectory: this.mediaActions.addDirectory
        }
    }

    onSelectMediaFile(path) {
        this.onSelectFile({path: path});
    }

    $onInit() {
        // call parent
        super.$onInit();

        // init component
        this.actions.fetchMediaFiles(
            this.state.currentDirectory
        );
    }

    changeDirectory(path) {
        this.actions.fetchMediaFiles(
            path
        ).then(() => {
            this.resetPagination();
        });
    }

    changeDirectoryUp(path) {
        this.actions.fetchMediaFiles(
            this.getPathUp(path)
        ).then(() => {
            this.resetPagination();
        });
    }

    downloadFile(e, mediaFile) {
        e.stopPropagation();
        let uri = this.mediaPath + mediaFile.path;
        let link = document.createElement("a");
        link.setAttribute('download', '');
        link.href = uri;
        this.document[0].body.appendChild(link);
        link.click();
        link.remove();
    }

    selectFiles(files, invalidFiles) {
        // reset errors
        if ((files && files.length > 0) || (invalidFiles && invalidFiles.length > 0)) {
            this.errors.maxFiles = false;
            this.errorsmaxFileSize = false;
            this.errorsmaxTotalSize = false;
        }

        // look for error in invalid files
        if (invalidFiles && invalidFiles.length > 0) {
            for (let i in invalidFiles) {
                if (invalidFiles.hasOwnProperty(i)) {
                    let invalidFile = invalidFiles[i];

                    // max file amount exceeded
                    if (invalidFile.$errorMessages.maxFiles) {
                        this.errors.maxFiles = true;
                    }

                    // max file size exceeded
                    if (invalidFile.$errorMessages.maxSize) {
                        this.errors.maxFileSize = true;
                    }
                }
            }
        }

        // upload files
        if (files && files.length > 0) {
            if (this.allowOverwriteExisting) {
                const existingFiles = this.getExistingFiles(files);
                if (existingFiles.length > 0) {
                    this.showOverwriteExistingFilesDialog().then(() => {
                        // confirmed
                        this.uploadFiles(files, true);
                    }, () => {
                        // canceled
                    });
                    return;
                }
            }

            // always upload when all files are new
            this.uploadFiles(files, false);
        }
    }

    uploadFiles(files, overwriteExisting) {
        this.actions.mediaFileUpload(
            this.state.currentDirectory,
            files,
            this.getId(),
            overwriteExisting
        ).then(() => {
            this.actions.fetchMediaFiles(
                this.state.currentDirectory
            ).then(() => {
                this.resetPagination();
            });
        });
    }

    renameFolder($event, folder) {
        $event.stopPropagation();
        const parentEl = angular.element(document.body);

        this.mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: RenameDialogTemplate,
            clickOutsideToClose: true,
            multiple: true,
            locals: {
                targetEvent: $event,
                folder: folder,
                onRenameFolder: this.onRenameFolder.bind(this)
            },
            controller: RenameDialogController
        });
    }

    onRenameFolder(folder, name) {
        this.actions.mediaDirectoryRename(
            this.state.currentDirectory,
            folder.path,
            name
        ).then(() => {
            this.actions.fetchMediaFiles(
                this.state.currentDirectory
            ).then(() => {
                this.resetPagination();
            });
        });
    }

    renameFolderConfirmed(folder) {

    }

    showOverwriteExistingFilesDialog() {
        // Appending dialog to document.body to cover sidenav in docs app
        let confirm = this.mdDialog.confirm()
            .title('Dateien überschreiben')
            .htmlContent('Einige Dateien existieren bereits. Diese werden beim erneuten Upload überschrieben.')
            .ariaLabel('Dateien überschreiben')
            .ok('Dateien überschreiben')
            .cancel('Abbrechen');

        return this.mdDialog.show(confirm);
    }

    getExistingFiles(files) {
        let existingFiles = [];

        for (let i = 0; i < files.length; i++) {
            const fileName = files[i].name;

            for (let j = 0; j < this.state.mediaFiles.length; j++) {
                const existingFileName = this.state.mediaFiles[j].name;

                if (fileName.trim().toLowerCase() === existingFileName.trim().toLowerCase()) {
                    existingFiles.push(files[i]);
                }
            }
        }

        return existingFiles;
    }

    removeFile($event, mediaFile) {
        $event.stopPropagation();
        if (mediaFile.isDir) {
            this.actions.removeDirectoryRecursive(this.state.currentDirectory, mediaFile.path);
        } else {
            this.actions.mediaFileRemove(this.state.currentDirectory, mediaFile.path)
        }
    }

    getId() {
        return 'upload-' + Date.now();
    }

    getPathUp(path) {
        let pathUp = '';
        if (path.lastIndexOf('/') === (path.length - 1)) {
            pathUp = path.substring(0, path.lastIndexOf('/'));
            pathUp = pathUp.substring(0, pathUp.lastIndexOf('/'));
        } else {
            pathUp = path.substring(0, path.lastIndexOf('/'));
        }
        return pathUp;
    }

    resetPagination() {
        this.mediaListPagination = {
            recordsPerPage: 50,
            currentPage: 1
        };
    }
}
Controller.$inject = ControllerInject;

const Component = {
    bindings: {
        onSelectFile: '&'
    },
    template: Template,
    controller: Controller
};

export default ['aptoMediaList', Component];