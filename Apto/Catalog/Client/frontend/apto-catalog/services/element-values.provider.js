import ElementBoolValue from '../model/element-bool-value.model';
import ElementSingleValue from '../model/element-single-value.model';
import ElementSingleTextValue from '../model/element-single-text-value.model';
import ElementRangeValue from '../model/element-range-value.model';
import ElementAreaRangeValue from '../model/element-area-range-value.model';
import ElementTextValue from '../model/element-text-value.model';
import ElementJsonValue from '../model/element-json-value.model';

const ElementValuesServiceInject = [];
const ElementValuesService = function() {
    const self = this;

    /**
     * Check if value is undefined
     * @param elementValue
     * @returns {boolean}
     */
    function isUndefinedValue(elementValue) {
        return typeof elementValue === 'undefined';
    }

    /**
     * Check if value is a bool value
     * @param elementValue
     * @returns {boolean}
     */
    function isBoolValue(elementValue) {
        return (
            typeof elementValue['type'] !== 'undefined' &&
            elementValue['type'] === 'bool'
        );
    }

    /**
     * Check if value is a single value
     * @param elementValue
     * @returns {boolean}
     */
    function isSingleValue(elementValue) {
        return (
            typeof elementValue['type'] !== 'undefined' &&
            elementValue['type'] === 'single' &&
            typeof elementValue['value'] !== 'undefined'
        );
    }

    /**
     * Check if value is a single text value
     * @param elementValue
     * @returns {boolean}
     */
    function isSingleTextValue(elementValue) {
        return (
            typeof elementValue['type'] !== 'undefined' &&
            elementValue['type'] === 'singleText' &&
            typeof elementValue['value'] !== 'undefined'
        );
    }

    /**
     * Check if given value is a range value
     * @param elementValue
     * @returns {boolean}
     */
    function isRangeValue(elementValue) {
        return (
            typeof elementValue['type'] !== 'undefined' &&
            elementValue['type'] === 'range' &&
            typeof elementValue['minimum'] !== 'undefined' &&
            typeof elementValue['maximum'] !== 'undefined' &&
            typeof elementValue['step'] !== 'undefined'
        );
    }

    /**
     * Check if given value is a range value
     * @param elementValue
     * @returns {boolean}
     */
    function isAreaRangeValue(elementValue) {
        return (
            typeof elementValue['type'] !== 'undefined' &&
            elementValue['type'] === 'areaRange' &&
            typeof elementValue['minimumWidth'] !== 'undefined' &&
            typeof elementValue['maximumWidth'] !== 'undefined' &&
            typeof elementValue['stepWidth'] !== 'undefined' &&
            typeof elementValue['minimumHeight'] !== 'undefined' &&
            typeof elementValue['maximumHeight'] !== 'undefined' &&
            typeof elementValue['stepHeight'] !== 'undefined'
        );
    }

    /**
     * Check if given value is a text value
     * @param elementValue
     * @returns {boolean}
     */
    function isTextValue(elementValue) {
        return (
            typeof elementValue['type'] !== 'undefined' &&
            elementValue['type'] === 'text' &&
            typeof elementValue['minLength'] !== 'undefined' &&
            typeof elementValue['maxLength'] !== 'undefined'
        );
    }

    /**
     * Check if given value is a json value
     * @param elementValue
     * @returns {boolean}
     */
    function isJsonValue(elementValue) {
        return (
            typeof elementValue['type'] !== 'undefined' &&
            elementValue['type'] === 'json'
        );
    }

    /**
     * render a human readable string out of valid elementValues
     * @param elementValues
     * @param prefix
     * @param suffix
     * @returns {string}
     */
    function getHumanReadableString(elementValues, prefix, suffix) {
        // set default values
        prefix = prefix ? prefix + ' ' : '';
        suffix = suffix ? ' ' + suffix : '';

        let i, elementValue, ranges = [];

        if (elementValues) {
            for (i = 0; i < elementValues.length; i++) {
                if (elementValues.hasOwnProperty(i)) {
                    elementValue = elementValues[i];

                    if (isUndefinedValue(elementValue)) {
                        // nothing to push
                    }
                    else if (isBoolValue(elementValue)) {
                        // @todo: use translation
                        ranges.push('ja/nein');
                    }
                    else if (isSingleValue(elementValue)) {
                        ranges.push(prefix + elementValue.value + suffix);
                    }
                    else if (isSingleTextValue(elementValue)) {
                        // ignore empty values
                        if (('' + elementValue.value).trim() !== '') {
                            ranges.push(prefix + elementValue.value + suffix);
                        }
                    }
                    else if (isRangeValue(elementValue)) {
                        // @todo: use elementValue.step ?
                        ranges.push(
                            prefix + elementValue.minimum + suffix + ' - ' + prefix + elementValue.maximum + suffix
                        );
                    }
                    else if (isAreaRangeValue(elementValue)) {
                        // @todo: intermediate sizes not covered
                        // @todo: use translation
                        if (typeof finished === "undefined") {
                            let minWidth = null, maxWidth = null, minHeight = null, maxHeight = null, finished = true;

                            for (i = 0; i < elementValues.length; i++) {
                                elementValue = elementValues[i];

                                if(minWidth === null || elementValue.minimumWidth < minWidth) {
                                    minWidth = elementValue.minimumWidth;
                                }

                                if(maxWidth === null || elementValue.maximumWidth > maxWidth) {
                                    maxWidth = elementValue.maximumWidth;
                                }

                                if(minHeight === null || elementValue.minimumHeight < minHeight) {
                                    minHeight = elementValue.minimumHeight;
                                }

                                if(maxHeight === null || elementValue.maximumHeight > maxHeight) {
                                    maxHeight = elementValue.maximumHeight;
                                }
                            }

                            let width = '(' + prefix[0] + ': ' + minWidth + suffix[0] + ' - ' + maxWidth  + suffix[0] + ')';
                            let height = '(' + prefix[1] + ': ' + minHeight + suffix[1] + ' - ' + maxHeight + suffix[1] + ')';

                            ranges.push(
                                width + ' x ' + height
                            );
                        }
                    }
                    else if (isTextValue(elementValue)) {
                        // @todo: use translation
                        ranges.push(
                            'Text (' + elementValue.minLength + ' - ' + elementValue.maxLength + ' Zeichen)'
                        );
                    } else if (isJsonValue(elementValue)) {
                        // nothing to push
                    }
                }
            }
        }

        return ranges.join(', ');
    }

    self.getHumanReadableString = getHumanReadableString;

    self.$getInject = [];
    self.$get = function() {
        return {
            getHumanReadableString: getHumanReadableString
        }
    };
    self.$get.$inject = self.$getInject;
};

ElementValuesService.$inject = ElementValuesServiceInject;

export default ['ElementValuesService', ElementValuesService];