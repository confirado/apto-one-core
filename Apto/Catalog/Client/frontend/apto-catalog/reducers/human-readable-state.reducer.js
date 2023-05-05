import update from 'immutability-helper';

const HumanReadableStateReducerInject = ['AptoReducersProvider'];
const HumanReadableStateReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_HUMAN_READABLE_STATE_';
    const initialState = {
        humanReadableState: {
            elements: {}
        }
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FETCH_CURRENT_HUMAN_READABLE_STATE_FULFILLED'): {
                state = update(state, {
                    humanReadableState: {
                        $set: action.payload.data.result
                    }
                });
                return state;
            }
        }

        return state;
    };

    AptoReducersProvider.addReducer('humanReadableState', this.reducer);

    this.$get = function() {};
};

HumanReadableStateReducer.$inject = HumanReadableStateReducerInject;

export default ['HumanReadableStateReducer', HumanReadableStateReducer];