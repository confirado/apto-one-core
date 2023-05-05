export default class ElementBoolValue {
    constructor() {
    }

    getValueLowerThan(value) {
        return null;
    }

    getValueGreaterThan(value) {
        return null;
    }

    getValueEqualTo(value) {
        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        return (value === true || value === false) ? value : null;
    }

    getValueNotEqualTo(value) {
        // prevent objects
        if (typeof value === 'object') {
            return null;
        }

        return (value === true || value === false) ? !value : false;
    }

    getAnyValue() {
        return false;
    }

    contains(value) {
        return value === true || value === false;
    }
}