import ElementPriceTemplate from './element-price.dialog.html';
import ElementPriceController from './element-price.dialog';
import ImportTemplate from './import.dialog.html';
import ImportDialog from './import.dialog';
import ExportTemplate from './export.dialog.html';
import ExportDialog from './export.dialog';

const PriceMatrixDetailControllerInject = ['$scope', '$mdDialog', '$ngRedux', 'PriceMatrixActions', 'targetEvent', 'showDetailsDialog', 'priceMatrixId'];
const PriceMatrixDetailController = function($scope, $mdDialog, $ngRedux, PriceMatrixActions, targetEvent, showDetailsDialog, priceMatrixId) {

    $scope.mapStateToThis = function(state) {
        return {
            priceMatrixDetail: state.priceMatrix.priceMatrixDetail,
            elements: state.priceMatrix.elements
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        fetchPriceMatrixDetail: PriceMatrixActions.fetchPriceMatrixDetail,
        savePriceMatrix: PriceMatrixActions.savePriceMatrix,
        priceMatrixDetailReset: PriceMatrixActions.priceMatrixDetailReset,
        fetchPriceMatrixElements: PriceMatrixActions.fetchPriceMatrixElements,
        addPriceMatrixElement: PriceMatrixActions.addPriceMatrixElement,
        removePriceMatrixElement: PriceMatrixActions.removePriceMatrixElement
    })($scope);

    function init() {
        if (typeof priceMatrixId !== "undefined") {
            $scope.priceMatrixId = priceMatrixId;
            $scope.fetchPriceMatrixDetail(priceMatrixId);
            $scope.fetchPriceMatrixElements(priceMatrixId).then(afterElementsFetch);
        }
        initNewElement();
        initMatrixTable();
    }

    function initNewElement() {
        $scope.newElement = {
            columnValue: '',
            rowValue: ''
        };
    }

    function initMatrixTable() {
        $scope.matrixTable = {
            usedRows: {},
            usedColumns: {},
            rows: [],
            columns: [],
            cells: {}
        };
    }

    function save(priceMatrixForm, closeForm) {
        if(priceMatrixForm.$valid) {
            $scope.savePriceMatrix($scope.priceMatrixDetail).then(function () {
                if (typeof closeForm !== "undefined") {
                    close();
                } else if(typeof $scope.priceMatrixDetail.id === "undefined") {
                    $scope.priceMatrixDetailReset();
                    showDetailsDialog(targetEvent);
                }
            });
        }
    }

    function close() {
        $scope.priceMatrixDetailReset();
        $mdDialog.cancel();
    }

    function addElement() {
        $scope.addPriceMatrixElement(
            priceMatrixId,
            $scope.newElement.columnValue,
            $scope.newElement.rowValue
        ).then(() => {
            initNewElement();
            $scope.fetchPriceMatrixElements(priceMatrixId).then(afterElementsFetch);
        });
    }

    function removeElement(elementId) {
        $scope.removePriceMatrixElement(
            priceMatrixId,
            elementId
        ).then(() => {
            $scope.fetchPriceMatrixElements(priceMatrixId).then(afterElementsFetch);
        });
    }

    function afterElementsFetch() {
        initMatrixTable();
        for (let i = 0; i < $scope.elements.length; i++) {
            const
                cellId = $scope.elements[i].id,
                rowValue = parseFloat($scope.elements[i].rowValue),
                columnValue = parseFloat($scope.elements[i].columnValue),
                priceCount = $scope.elements[i].aptoPrices.length,
                customPropertiesCount = $scope.elements[i].customProperties.length;

            if (typeof $scope.matrixTable.usedRows[rowValue] === "undefined") {
                $scope.matrixTable.usedRows[rowValue] = rowValue;
                $scope.matrixTable.rows.push(rowValue);
            }

            if (typeof $scope.matrixTable.usedColumns[columnValue] === "undefined") {
                $scope.matrixTable.usedColumns[columnValue] = columnValue;
                $scope.matrixTable.columns.push(columnValue);
            }

            $scope.matrixTable.cells[(rowValue + '_' + columnValue)] = {
                cellId: cellId,
                rowValue: rowValue,
                columnValue: columnValue,
                priceCount: priceCount,
                customPropertiesCount: customPropertiesCount,
                hasPricesOrCustomProperties: priceCount > 0 || customPropertiesCount > 0
            };
        }
        $scope.matrixTable.rows.sort(function(a, b){return a - b});
        $scope.matrixTable.columns.sort(function(a, b){return a - b});
    }

    function afterPriceDialogClose() {
        $scope.fetchPriceMatrixElements(priceMatrixId).then(afterElementsFetch);
    }

    function openElementPriceDialog($event, element) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            template: ElementPriceTemplate,
            clickOutsideToClose: true,
            fullscreen: true,
            multiple: true,
            locals: {
                priceMatrixId: priceMatrixId,
                element: element
            },
            controller: ElementPriceController
        }).then(afterPriceDialogClose, afterPriceDialogClose);
    }

    function openImportDialog($event) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            clickOutsideToClose: true,
            fullscreen: false,
            multiple: true,
            locals: {
                title: 'Preismatrizen importieren',
                message: 'Bitte laden Sie ein oder mehrere Preismatrizen im CSV-Format hoch, um den Import automatisch zu starten.',
                uploadCommand: 'ImportPriceMatrix',
                onUploaded: null,
                priceMatrixId: priceMatrixId
            },
            template: ImportTemplate,
            controller: ImportDialog
        }).then(afterPriceDialogClose, afterPriceDialogClose);
    }

    function openExportDialog($event) {
        const parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            clickOutsideToClose: true,
            fullscreen: false,
            multiple: true,
            locals: {
                title: 'Preismatrizen Exportieren',
                content: 'Die Preismatrize wird im CSV Format zur Verfügung gestellt.',
                errorMessages: {
                    input: 'Bitte füllen sie alle benötigten Felder aus.',
                    export: 'Beim erstellen der CSV Datei ist ein Fehler aufgetreten.'
                },
                downloadCommand: 'ExportPriceMatrix',
                onDownloaded: null,
                priceMatrixId: priceMatrixId
            },
            template: ExportTemplate,
            controller: ExportDialog
        }).then(afterPriceDialogClose, afterPriceDialogClose);
    }

    init();

    $scope.save = save;
    $scope.close = close;
    $scope.addElement = addElement;
    $scope.removeElement = removeElement;
    $scope.openElementPriceDialog = openElementPriceDialog;
    $scope.openImportDialog = openImportDialog;
    $scope.openExportDialog = openExportDialog;
    $scope.$on('$destroy', subscribedActions);
};

PriceMatrixDetailController.$inject = PriceMatrixDetailControllerInject;

export default PriceMatrixDetailController;