import { BigNumber } from 'bignumber.js';

// @todo refactor all methods to use BigNumber
// methods refactored: contains
export default class ElementAreaRangeValue {
    constructor(minimumWidth, maximumWidth, stepWidth, minimumHeight, maximumHeight, stepHeight) {
        this.minimumWidth = parseFloat(minimumWidth);
        this.maximumWidth = parseFloat(maximumWidth);
        this.stepWidth = parseFloat(stepWidth);
        this.minimumHeight = parseFloat(minimumHeight);
        this.maximumHeight = parseFloat(maximumHeight);
        this.stepHeight = parseFloat(stepHeight);
    }

    static modulo(a, b) {
        return a - b * Math.floor(a / b);
    }

    getValueLowerThan(value) {
        let offset, resultValue, result = {}, resume = true;
        result['width'] = null;
        result['height'] = null;
        let width = parseFloat(value['width']);
        let height = parseFloat(value['height']);

        if (width === null) {
            resume = false;
        }

        if(resume) {
            // value outside of range
            if (width < this.minimumWidth || width > this.maximumWidth) {
                return null;
            }

            offset = width - this.minimumWidth;
            if (offset % this.stepWidth === 0) {
                offset -= this.stepWidth;
            } else {
                offset -= offset % this.stepWidth;
            }

            resultValue = this.minimumWidth + offset;
            if (result < this.minimumWidth || result > this.maximumWidth) {
                result['width'] = null;
            } else {
                result['width'] = resultValue;
            }
        }

        resume = true;

        if (height === null) {
            resume = false;
        }

        if(resume) {
            // value outside of range
            if (height < this.minimumHeight || height > this.maximumHeight) {
                return null;
            }

            offset = height - this.minimumHeight;
            if (offset % this.stepHeight === 0) {
                offset -= this.stepHeight;
            } else {
                offset -= offset % this.stepHeight;
            }

            resultValue = this.minimumHeight + offset;
            if (result < this.minimumHeight || result > this.maximumHeight) {
                result['height'] = null;
            } else {
                result['height'] = resultValue;
            }
        }

        return result;
    }

    getValueGreaterThan(value) {
        let offset, resultValue, result = {}, resume = true;
        result['width'] = null;
        result['height'] = null;
        let width = parseFloat(value['width']);
        let height = parseFloat(value['height']);


        // value outside of range
        if (width < this.minimumWidth || width > this.maximumWidth) {
            resume = false;
        }

        if(resume) {
            offset = width - this.minimumWidth;
            if (this.constructor.modulo(offset, this.stepWidth) === 0) {
                offset += this.stepWidth;
            } else {
                offset -= this.constructor.modulo(offset, this.stepWidth) - this.stepWidth;
            }

            resultValue = this.minimumWidth + offset;
            if (result < this.minimumWidth || result > this.maximumWidth) {
                return null;
            } else {
                result['width'] = resultValue;
            }
        }

        // value outside of range
        if (height < this.minimumHeight || height > this.maximumHeight) {
            resume = false;
        }

        if(resume) {
            offset = height - this.minimumHeight;
            if (this.constructor.modulo(offset, this.stepHeight) === 0) {
                offset += this.stepHeight;
            } else {
                offset -= this.constructor.modulo(offset, this.stepHeight) - this.stepHeight;
            }

            resultValue = this.minimumHeight + offset;
            if (result < this.minimumHeight || result > this.maximumHeight) {
                return null;
            } else {
                result['height'] = resultValue;
            }
        }

        return result;
    }

    getValueEqualTo(value) {
        let offset, result = {}, resume = true;
        result['width'] = null;
        result['height'] = null;

        let width = parseFloat(value['width']);
        let height = parseFloat(value['height']);
        
        
        if (width === null) {
            resume = false;
        }
        
        if(resume) {
            // value outside of range
            if (width < this.minimumWidth || value > this.maximumWidth) {
                return null;
            }

            offset = width - this.minimumWidth;
            if (this.constructor.modulo(offset, this.stepWidth) !== 0) {
                return null;
            }
        }

        if (height === null) {
            resume = false;
        }

        if(resume) {
            // value outside of range
            if (height < this.minimumHeight || value > this.maximumHeight) {
                return null;
            }

            offset = height - this.minimumHeight;
            if (this.constructor.modulo(offset, this.stepHeight) !== 0) {
                return null;
            }
        }

        return result;
    }

    getValueNotEqualTo(value) {
        let min, max, result = {};
        result['width'] = null;
        result['height'] = null;

        min = this.getValueLowerThan(value);
        if (null !== min['width']) {
            result['width'] = min['width'];
        }
        if (null !== min['height']) {
            result['height'] = min['height'];
        }

        max = this.getValueGreaterThan(value);
        if (null !== max['width']) {
            result['width'] = max['width'];
        }
        if (null !== max['height']) {
            result['height'] = max['height'];
        }

        return result;
    }

    getAnyValue() {
        let result = {};
        result['width'] = this.minimumWidth;
        result['height'] = this.minimumHeight;
        return result;
    }

    contains(value) {
        return this.containsSingle(
            value['width'],
            this.minimumWidth,
            this.maximumWidth,
            this.stepWidth
        ) && this.containsSingle(
            value['height'],
            this.minimumHeight,
            this.maximumHeight,
            this.stepHeight
        );
    }

    containsSingle(value, minSingle, maxSingle, stepSingle) {
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
        const minimum = new BigNumber(minSingle);
        const maximum = new BigNumber(maxSingle);
        const step = new BigNumber(stepSingle);

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