import { BigNumber } from 'bignumber.js';

// @todo refactor all methods to use BigNumber
// methods refactored: contains
export default class ElementRangeValue {
    constructor(minimum, maximum, step) {
        this.minimum = parseFloat(minimum);
        this.maximum = parseFloat(maximum);
        this.step = parseFloat(step);
    }

    static modulo(a, b) {
        return a - b * Math.floor(a / b);
    }

    getValueLowerThan(value) {
        let offset, result;

        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        // parse value
        value = parseFloat(value);
        if (value === null) {
            return null;
        }

        // value outside of range
        if (value < this.minimum || value > this.maximum) {
            return null;
        }

        offset = value - this.minimum;
        if (offset % this.step === 0) {
            offset -= this.step;
        } else {
            offset -= offset % this.step;
        }

        result = this.minimum + offset;
        if (result < this.minimum || result > this.maximum) {
            return null;
        }

        return result;
    }

    getValueGreaterThan(value) {
        let offset, result;

        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        // parse value
        value = parseFloat(value);
        if (value === null) {
            return null;
        }

        // value outside of range
        if (value < this.minimum || value > this.maximum) {
            return null;
        }

        offset = value - this.minimum;
        if (this.constructor.modulo(offset, this.step) === 0) {
            offset += this.step;
        } else {
            offset -= this.constructor.modulo(offset, this.step) - this.step;
        }

        result = this.minimum + offset;
        if (result < this.minimum || result > this.maximum) {
            return null;
        }

        return result;
    }

    getValueEqualTo(value) {
        let offset;

        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        // parse value
        value = parseFloat(value);
        if (value === null) {
            return null;
        }

        // value outside of range
        if (value < this.minimum || value > this.maximum) {
            return null;
        }

        offset = value - this.minimum;
        if (this.constructor.modulo(offset, this.step) !== 0) {
            return null;
        }

        return value;
    }

    getValueNotEqualTo(value) {
        let min, max;

        min = this.getValueLowerThan(value);
        if (null !== min) {
            return min;
        }

        max = this.getValueGreaterThan(value);
        if (null !== max) {
            return max;
        }

        return null;
    }

    getAnyValue() {
        return this.minimum;
    }

    contains(value) {
        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        // parse value
        value = parseFloat(value);
        if (value === null) {
            return null;
        }

        // transform minimum, maximum and step to BigNumber
        const minimum = new BigNumber(this.minimum);
        const maximum = new BigNumber(this.maximum);
        const step = new BigNumber(this.step);

        // transform value to BigNumber
        value = new BigNumber(value);

        // if value is out of range return false
        if (value.lt(minimum) || value.gt(maximum)) {
            return false;
        }

        // calculate modulo
        const offset = value.minus(minimum);
        const moduloResult = offset.modulo(step);
        const moduloExpected = new BigNumber(0);

        // return comparison result
        return moduloResult.eq(moduloExpected);
    }
}