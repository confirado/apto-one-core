export default class ElementJsonValue {
    constructor() {
    }

    getValueLowerThan(value) {
        return '';
    }

    getValueGreaterThan(value) {
        return '';
    }

    getValueEqualTo(value) {
        return value;
    }

    getValueNotEqualTo(value) {
        return '';
    }

    getAnyValue() {
        return '';
    }

    contains(value) {
        try {
            let a = JSON.stringify(value);
            return true;
        }
        catch (e) {
            return false;
        }
    }
}