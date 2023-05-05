import update from 'immutability-helper';

const FavoriteDesignsReducerInject = ['AptoReducersProvider'];
const FavoriteDesignsReducer = function(AptoReducersProvider) {
    const TYPE_NS = 'APTO_FAVORITE_DESIGNS_';
    const initialState = {
        designs: []
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.favoriteDesigns = function (state, action) {
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
        }

        switch (action.type) {
            case 'APTO_CONFIGURATION_FETCH_PROPOSED_CONFIGURATIONS_FULFILLED':
                const result = action.payload.data.result.data ? action.payload.data.result.data : [];

                state = update(state, {
                    designs: {
                        $set: result
                    }
                });
                return state;
        }
        return state;
    };

    AptoReducersProvider.addReducer('favoriteDesigns', this.favoriteDesigns);

    this.$get = function() {};
};

FavoriteDesignsReducer.$inject = FavoriteDesignsReducerInject;

export default ['FavoriteDesignsReducer', FavoriteDesignsReducer];