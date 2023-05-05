const ImportDialogInject = ['$scope', '$mdDialog', '$ngRedux', '$templateCache', '$window', 'PriceMatrixActions', 'ProductActions', 'title', 'message', 'onUploaded', 'priceMatrixId', 'APTO_ENVIRONMENT'];
const ImportDialog = function($scope, $mdDialog, $ngRedux, $templateCache, $window, PriceMatrixActions, ProductActions, title, message, onUploaded, priceMatrixId, APTO_ENVIRONMENT) {

    $scope.mapStateToThis = function(state) {
        return {
            availableCustomerGroups: state.product.availableCustomerGroups,
            runningUploads: state.priceMatrix.runningUploads,
            uploadProgress: state.priceMatrix.uploadProgress,
            report: state.priceMatrix.report
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        availableCustomerGroupsFetch: ProductActions.availableCustomerGroupsFetch,
        importFileUpload: PriceMatrixActions.importFileUpload,
        resetImportReport: PriceMatrixActions.resetImportReport
    })($scope);

    function init() {
        $scope.availableCustomerGroupsFetch();
        $scope.resetImportReport();
    }

    function uploadFiles(files, invalidFiles) {
        // reset errors
        if (files.length > 0 || invalidFiles.length > 0) {
            $scope.errors.maxFiles = false;
            $scope.errorsmaxFileSize = false;
            $scope.errorsmaxTotalSize = false;
        }

        // look for error in invalid files
        if (invalidFiles.length > 0) {
            for (let i in invalidFiles) {
                if (invalidFiles.hasOwnProperty(i)) {
                    let invalidFile = invalidFiles[i];
                    // max file amount exceeded
                    if (invalidFile.$errorMessages.maxFiles) {
                        $scope.errors.maxFiles = true;
                    }
                    // max file size exceeded
                    if (invalidFile.$errorMessages.maxSize) {
                        $scope.errors.maxFileSize = true;
                    }
                }
            }
        }

        // upload files
        if (files.length > 0) {
            $scope.importFileUpload(
                'ImportPriceMatrix',
                [$scope.currency, priceMatrixId, $scope.customerGroupId, $scope.csvType],
                files,
                getId()
            ).then(() => {
                if (typeof onUploaded === 'function') {
                    onUploaded(files);
                }
            });
        }
    }

    function getId() {
        return 'upload-' + Date.now();
    }

    function close() {
        $mdDialog.cancel();
    }

    init();

    $scope.close = close;

    $scope.currency = 'EUR';
    $scope.customerGroupId = '';
    $scope.title = title;
    $scope.message = message;
    $scope.uploadFiles = uploadFiles;
    $scope.maxFiles = APTO_ENVIRONMENT.upload.maxFiles;
    $scope.maxFileSize = APTO_ENVIRONMENT.upload.maxFileSize;
    $scope.maxTotalSize = APTO_ENVIRONMENT.upload.maxTotalSize;
    $scope.errors = {
        maxFiles: false,
        maxFileSize: false,
        maxTotalSize: false
    };
    $scope.csvType = 'flat';
    $scope.csvTypes = [
        {
            'value': 'flat',
            'label': 'flache Liste'
        },
        {
            'value': 'matrix',
            'label': 'Matrix'
        }
    ];

    $scope.$on('$destroy', subscribedActions);
};

ImportDialog.$inject = ImportDialogInject;

export default ImportDialog;