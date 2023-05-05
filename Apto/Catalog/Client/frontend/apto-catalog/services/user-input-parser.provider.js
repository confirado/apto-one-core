const UserInputParserInject = [];
const UserInputParser = function() {

    /**
     * treat given value as number and sanitize it
     * e.g. " ,1230  " => 0.123
     *      "000123,0" => 123
     *      "0" => 0
     * @param value
     * @returns {string}
     */
    function sanitizeNumber(value) {

        // cast value to string
        let stringValue = '' + value;

        // trim spaces and replace , with .
        stringValue = stringValue.trim().replace(/,/g, '.');

        // trim all leading zeros
        if (stringValue.length > 1) {
            stringValue = stringValue.replace(/^0+/g, '');
        }

        // add at least one zero before .
        if (stringValue.charAt(0) === '.') {
            stringValue = '0' + stringValue;
        }

        // trim succeeding zeros and comma, if needed
        if (stringValue.indexOf('.') >= 0) {
            stringValue = stringValue.replace(/\.?[0]*$/g, '');
        }

        return stringValue;
    }

    // convert input to float with respect to localised separator , or .
    this.convertFloat = function(value) {
        let replacedValue = sanitizeNumber(value),
            parsedValue = parseFloat(replacedValue);

        return replacedValue === ('' + parsedValue) ? parsedValue : null;
    };

    // convert input to int value
    this.convertInt = function(value) {
        let replacedValue = sanitizeNumber(value),
            parsedValue = parseInt(replacedValue);

        return replacedValue === ('' + parsedValue) ? parsedValue : null;
    };


    this.$get = function() {
        return {
            convertFloat: this.convertFloat,
            convertInt: this.convertInt
        };
    };
};

UserInputParser.$inject = UserInputParserInject;

export default ['UserInputParser', UserInputParser];