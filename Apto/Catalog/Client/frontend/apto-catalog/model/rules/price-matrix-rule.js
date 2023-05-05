export default class PriceMatrixRule {
    constructor(matrixLookUp, mapping) {
        // lookup table for possible row/column pair
        this.matrixLookUp = matrixLookUp;

        // mapping value.property -> row/column
        this.mapping = mapping;

        // available rows
        this.rows = [];

        // available columns
        this.columns = [];

        // init rows
        this.initRows();

        // init columns
        this.initColumns();
    }

    initRows() {
        for(let row in this.matrixLookUp) {
            if (!this.matrixLookUp.hasOwnProperty(row)) {
                continue;
            }

            this.rows.push(parseFloat(row));
        }

        this.rows.sort(PriceMatrixRule.sortNumbersDesc);
    }

    initColumns() {
        if (this.rows.length > 0) {
            const row = this.matrixLookUp[this.rows[0]];

            for(let column in row) {
                if (!row.hasOwnProperty(column)) {
                    continue;
                }

                this.columns.push(parseFloat(column));
            }
        }

        this.columns.sort(PriceMatrixRule.sortNumbersDesc);
    }

    fulfilled(values) {
        const rowValue = values[this.mapping.row];
        const columnValue = values[this.mapping.column];

        let lookUpRow = this.getLookupRow(rowValue);
        let lookUpColumn = this.getLookupColumn(columnValue);

        if (
            null === lookUpRow ||
            null === lookUpColumn ||
            !this.matrixLookUp[lookUpRow] ||
            !this.matrixLookUp[lookUpRow][lookUpColumn]
        ) {
            return false;
        }

        return true;
    }

    getLookupRow(value) {
        return this.getLookUpValue(value, 'rows');
    }

    getLookupColumn(value) {
        return this.getLookUpValue(value, 'columns');
    }

    getLookUpValue(value, rowsOrColumns) {
        if (rowsOrColumns !== 'rows' && rowsOrColumns !== 'columns') {
            return null;
        }

        for (let i = 0; i < this[rowsOrColumns].length; i++) {
            const rowOrColumnValue = this[rowsOrColumns][i];

            if (value <= rowOrColumnValue) {
                return rowOrColumnValue;
            }
        }

        return null;
    }

    static sortNumbersDesc(a, b) {
        if (a === b) {
            return 0;
        }

        return a < b ? -1 : 1;
    }
}