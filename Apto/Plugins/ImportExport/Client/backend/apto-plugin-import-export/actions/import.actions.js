const ActionsInject = ['$ngRedux', 'MessageBusFactory', 'Upload'];
const Actions = function ($ngRedux, MessageBusFactory, Upload) {
    const TYPE_NS = 'APTO_PLUGIN_IMPORT_EXPORT_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function importFile(domain, isocode, files) {
        let url = APTO_API.root + '/import-export/import-upload';
        let data = {
            file: files,
            arguments: {
                host: domain,
                locale: isocode
            }
        };

        return {
            type: getType('IMPORT_FILE'),
            payload: Upload.upload({
                url: url,
                data: data
            })
        };
    }

    function resetResults() {
        return {
            type: getType('RESET_RESULTS')
        };
    }

    return {
        importFile: importFile,
        resetResults: resetResults
    };
};

Actions.$inject = ActionsInject;
export default ['PluginImportExportImportActions', Actions];