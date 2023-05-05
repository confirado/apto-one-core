import update from 'immutability-helper';

const RuleReducerInject = ['AptoReducersProvider'];
const RuleReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_RULE_';
    const initialState = {
        operatorsActive: [{
            id: 1,
            name: 'aktiv'
        }, {
            id: 0,
            name: 'nicht aktiv'
        }],
        operatorsEqual: [{
            id: 4,
            name: 'gleich'
        }, {
            id: 5,
            name: 'nicht gleich'
        }, {
            id: 2,
            name: 'kleiner'
        }, {
            id: 3,
            name: 'kleiner gleich'
        }, {
            id: 7,
            name: 'größer'
        }, {
            id: 6,
            name: 'größer gleich'
        }, {
            id: 8,
            name: 'enthält'
        }, {
            id: 9,
            name: 'enthält nicht'
        }],
        operatorsFull: [{
            id: 1,
            name: 'aktiv'
        }, {
            id: 0,
            name: 'nicht aktiv'
        }, {
            id: 4,
            name: 'gleich'
        }, {
            id: 5,
            name: 'nicht gleich'
        }, {
            id: 2,
            name: 'kleiner'
        }, {
            id: 3,
            name: 'kleiner gleich'
        }, {
            id: 7,
            name: 'größer'
        }, {
            id: 6,
            name: 'größer gleich'
        }, {
            id: 8,
            name: 'enthält'
        }, {
            id: 9,
            name: 'enthält nicht'
        }],
        detail: {},
        sections: [],
        conditions: [],
        implications: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.rule = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_DETAIL_FULFILLED'):
                newState = update(state, {
                    detail: {
                        $set: action.payload.data.result
                    }
                });
                return newState;
            case getType('FETCH_SECTIONS_FULFILLED'):
                newState = update(state, {
                    sections: {
                        $set: action.payload.data.result.sections
                    }
                });
                return newState;
            case getType('FETCH_CONDITIONS_FULFILLED'):
                newState = update(state, {
                    conditions: {
                        $set: action.payload.data.result.conditions
                    }
                });
                return newState;
            case getType('FETCH_IMPLICATIONS_FULFILLED'):
                newState = update(state, {
                    implications: {
                        $set: action.payload.data.result.implications
                    }
                });
                return newState;
            case getType('RESET'):
                newState = update(state, {
                    $set: angular.copy(initialState)
                });
                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('rule', this.rule);

    this.$get = function() {};
};

RuleReducer.$inject = RuleReducerInject;

export default ['RuleReducer', RuleReducer];