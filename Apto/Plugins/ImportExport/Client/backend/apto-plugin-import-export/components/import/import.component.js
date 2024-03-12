import Template from './import.component.html';
import ContainerController from 'apto-base/components/apto-container.controller';

const ControllerInject = ['$ngRedux', 'LanguageFactory', 'PluginImportExportImportActions', 'ShopActions'];
class Controller extends ContainerController{
    constructor($ngRedux, LanguageFactory, PluginImportExportImportActions, ShopActions) {
        super($ngRedux);

        // services
        this.languageFactory = LanguageFactory;
        this.importActions = PluginImportExportImportActions;
        this.shopActions = ShopActions;

        // properties
        this.selectedShop = null;
        this.selectedLanguage = null;
        this.uploadInProgress = false;
        this.errors = {
            maxFiles: false,
            maxFileSize: false,
            maxTotalSize: false
        };
        this.documentationLink = APTO_API.root + '/import-export/documentation';
    }

    connectActions() {
        // actions mapping object
        return {
            importFile: this.importActions.importFile,
            resetResults: this.importActions.resetResults,
            fetchShops: this.shopActions.shopsFetch
        }
    }

    connectProps() {
        return (state) => {
            // state mapping object
            return {
                shops: state.shop.shops,
                languages: state.index.languages,
                results: state.pluginImportExportImport.results
            }
        }
    }

    onStateChange(state) {
        super.onStateChange(state);

        if (this.selectedShop === null) {
            this.setSelectedDefaultShop();
        }

        if (this.selectedLanguage === null) {
            this.setSelectedDefaultLanguage();
        }
    }

    $onInit() {
        super.$onInit();

        this.actions.fetchShops();
        this.actions.resetResults();
    }

    setSelectedDefaultShop() {
        if (this.state.shops.length > 0) {
            this.selectedShop = this.state.shops[0];
        }
    }

    setSelectedDefaultLanguage() {
        if (this.state.languages.length > 0) {
            this.selectedLanguage = this.state.languages[0];
        }
    }

    uploadFiles(files, invalidFiles) {
        // reset errors
        if (files.length > 0 || invalidFiles.length > 0) {
            this.errors.maxFiles = false;
            this.errors.maxFileSize = false;
            this.errors.maxTotalSize = false;
        }

        // look for error in invalid files
        if (invalidFiles.length > 0) {
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

        // upload file
        if (files.length > 0 && null !== this.selectedShop && null !== this.selectedLanguage) {
            this.uploadInProgress = true;
            this.actions.importFile(
                this.selectedShop.domain,
                this.selectedLanguage.isocode,
                files
            ).then(() => {
                this.uploadInProgress = false;
            });
        }
    }
}

Controller.$inject = ControllerInject;

const Component = {
    bindings: {
    },
    template: Template,
    controller: Controller
};

export default ['pluginImportExportImport', Component];