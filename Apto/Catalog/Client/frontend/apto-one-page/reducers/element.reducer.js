import update from 'immutability-helper';

const ElementReducerInject = ['AptoReducersProvider'];
const ElementReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_ONE_PAGE_ELEMENT_';
    const initialState = {
        sidebarOpen: false
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.onePageElement = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }
        switch (action.type) {
            // change Sidebar
            case getType('SET_SIDEBAR_OPEN'):
                newState = update(state, {
                    sidebarOpen: {
                        $set: action.payload
                    }
                });
                return newState;
        }
        return state;
    };

    AptoReducersProvider.addReducer('onePageElement', this.onePageElement);

    this.$get = function() {};
};

ElementReducer.$inject = ElementReducerInject;

export default ['ElementReducer', ElementReducer];