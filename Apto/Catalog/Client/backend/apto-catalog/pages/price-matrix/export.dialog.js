const ExportDialogInject = ['$scope', '$document', '$mdDialog', '$ngRedux', '$templateCache', '$window', 'PriceMatrixActions', 'ProductActions', 'title', 'content', 'errorMessages', 'priceMatrixId'];
const ExportDialog = function($scope, $document, $mdDialog, $ngRedux, $templateCache, $window, PriceMatrixActions, ProductActions, title, content, errorMessages, priceMatrixId) {

    $scope.mapStateToThis = function(state) {
        return {
            availableCustomerGroups: state.product.availableCustomerGroups,
            report: state.priceMatrix.report,
            csvExportString: state.priceMatrix.csvExportString
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        availableCustomerGroupsFetch: ProductActions.availableCustomerGroupsFetch,
        resetCsvExportString: PriceMatrixActions.resetCsvExportString,
        fetchCsvExportString: PriceMatrixActions.fetchCsvExportString
    })($scope);

    function init() {
        $scope.availableCustomerGroupsFetch();
        $scope.resetCsvExportString();
    }

    function close() {
        $mdDialog.cancel();
    }

    function downloadCsvExport() {
        $scope.errors.input = false;
        $scope.errors.input = false;

        if (!priceMatrixId || !$scope.customerGroupId || !$scope.currencyCode) {
            $scope.errors.input = true;
            return;
        }

        $scope.fetchCsvExportString(priceMatrixId, $scope.customerGroupId, $scope.currencyCode, $scope.csvType).then(() => {
            if (null === $scope.csvExportString) {
                $scope.errors.export = true;
                return;
            }

            download('PreismatritzeExport.csv', $scope.csvExportString);
            $scope.resetCsvExportString();
        });
    }

    function download(filename, text) {
        let element = $document[0].createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        $document[0].body.appendChild(element);

        element.click();

        $document[0].body.removeChild(element);
    }

    init();

    $scope.close = close;
    $scope.customerGroupId = null;
    $scope.currencyCode = 'EUR';
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
    $scope.title = title;
    $scope.content = content;
    $scope.errorMessages = errorMessages;
    $scope.downloadCsvExport = downloadCsvExport;
    $scope.errors = {
        input: false,
        export: false
    };

    $scope.$on('$destroy', subscribedActions);
};

ExportDialog.$inject = ExportDialogInject;

export default ExportDialog;