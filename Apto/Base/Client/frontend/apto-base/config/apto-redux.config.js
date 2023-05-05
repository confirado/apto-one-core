import loggingMiddleware from 'redux-logger';
import thunkMiddleware from 'redux-thunk';
import promiseMiddleware from 'redux-promise-middleware';
import {compose} from 'redux';

const AptoReduxConfigInject = ['$ngReduxProvider', 'AptoReducersProvider'];
const AptoReduxConfig = function ($ngReduxProvider, AptoReducersProvider) {
    if (process.env.NODE_ENV == 'development') {
        if (typeof window.devToolsExtension !== "undefined") {
            const enhancers = compose(window.devToolsExtension());
            $ngReduxProvider.createStoreWith(AptoReducersProvider.getCombinedReducers(), [promiseMiddleware(), thunkMiddleware], [enhancers]);
        }
        else {
            $ngReduxProvider.createStoreWith(AptoReducersProvider.getCombinedReducers(), [promiseMiddleware(), thunkMiddleware, loggingMiddleware()]);
        }
    }
    else {
        $ngReduxProvider.createStoreWith(AptoReducersProvider.getCombinedReducers(), [promiseMiddleware(), thunkMiddleware]);
    }
};
AptoReduxConfig.$inject = AptoReduxConfigInject;

export default ['AptoReduxConfig', AptoReduxConfig];