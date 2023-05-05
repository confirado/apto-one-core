import update from 'immutability-helper';

const ProductReducerInject = ['AptoReducersProvider'];
const ProductReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_PRODUCT_';
    const initialState = {
        productDetail: {},
        selectedSection: {},
        computedValues: {}
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.product = function (state, action) {
        let newState;
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case getType('SET_PRODUCT_DETAIL'):
                newState = update(state, {
                    productDetail: {
                        $set: action.payload
                    }
                });

                return newState;
            case getType('SELECT_SECTION'):
                const sectionId = action.payload;

                for (let i = 0; i < state.productDetail.sections.length; i++) {
                    if (state.productDetail.sections[i].id === sectionId) {
                        newState = update(state, {
                            selectedSection: {

                                $set: state.productDetail.sections[i]
                            }
                        });
                        return newState;
                    }
                }
                break;
            case getType('FETCH_COMPUTED_PRODUCT_VALUES_CALCULATED_FULFILLED'):
                newState = update(state, {
                    computedValues: {
                        $set: action.payload.data.result
                    }
                })
                return newState
            case getType('RESET_SECTION'):
                newState = update(state, {
                    selectedSection: {
                        $set: angular.copy(initialState.selectedSection)
                    }
                });
                return newState;
            case getType('RESET_PRODUCT_DETAIL'):
                newState = update(state, {
                    productDetail: {
                        $set: angular.copy(initialState.productDetail)
                    },
                    computedValues: {
                        $set: angular.copy(initialState.computedValues)
                    }
                });
                return newState;
        }

        return state;
    };

    AptoReducersProvider.addReducer('product', this.product);

    this.$get = function() {};
};

ProductReducer.$inject = ProductReducerInject;

export default ['ProductReducer', ProductReducer];
