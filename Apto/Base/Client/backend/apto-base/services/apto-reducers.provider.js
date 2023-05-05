import { combineReducers } from 'redux';

const AptoReducersProvider = function() {
    const reducers = {};

    this.addReducer = function (state, reducer) {
        reducers[state] = reducer;
    };

    this.getCombinedReducers = function () {
        return combineReducers(reducers);
    };

    this.$get = function() {};
};

export default ['AptoReducers', AptoReducersProvider];