export default class ElementSingleTextValue {
    constructor(value) {
        this.value = '' + value;
    }

    getValueLowerThan(value) {
        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        value = '' + value;
        return this.value < value ? this.value : null;
    }

    getValueGreaterThan(value) {
        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        value = '' + value;
        return this.value > value ? this.value : null;
    }

    getValueEqualTo(value) {
        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        value = '' + value;
        return this.value === value ? this.value : null;
    }

    getValueNotEqualTo(value) {
        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        value = '' + value;
        return this.value !== value ? this.value : null;
    }

    getAnyValue() {
        return this.value;
    }

    contains(value) {
        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        value = '' + value;
        return this.value === value;
    }
}