import update from 'immutability-helper';

const ReducerInject = ['AptoReducersProvider'];
const Reducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_FRONTEND_USERS_';
    const initialState = {
        isLoggedIn: false,
        id: '',
        userName: '',
        email: ''
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === 'undefined') {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('FIND_CURRENT_FRONTEND_USER_FULFILLED'): {
                if(action.payload.data.result === null) {
                    return state;
                }
                return update(state, {
                    isLoggedIn: {
                        $set: true
                    },
                    id: {
                        $set: action.payload.data.result.id
                    },
                    userName: {
                        $set: action.payload.data.result.username
                    },
                    email: {
                        $set: action.payload.data.result.email
                    },
                    externalCustomerGroupId: {
                        $set: action.payload.data.result.externalCustomerGroupId
                    },
                    customerNumber: {
                        $set: action.payload.data.result.customerNumber
                    }
                });
            }
            case getType('LOGOUT_CURRENT_USER'): {
                return update(state, {
                    isLoggedIn: {
                        $set: false
                    },
                    userName: {
                        $set: ''
                    },
                    email: {
                        $set: ''
                    }
                });
            }
        }
        return state;
    };

    AptoReducersProvider.addReducer('frontendUser', this.reducer);

    this.$get = function() {};
};

Reducer.$inject = ReducerInject;

export default ['FrontendUserReducer', Reducer];
