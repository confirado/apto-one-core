export default class ElementTextValue {
    constructor(minLength, maxLength) {
        this.minLength = parseInt(minLength);
        this.maxLength = parseInt(maxLength);
    }

    getValueLowerThan(value) {
        return ''; // @TODO
    }

    getValueGreaterThan(value) {
        return ''; // @TODO
    }

    getValueEqualTo(value) {
        return ''; // @TODO
    }

    getValueNotEqualTo(value) {
        return ''; // @TODO
    }

    getAnyValue() {
        return ''; // @TODO
    }

    contains(value) {
        if (typeof value !== 'string') {
            return false;
        }

        if (value.length < this.minLength || value.length > this.maxLength) {
            return false;
        }

        return true;
    }
}