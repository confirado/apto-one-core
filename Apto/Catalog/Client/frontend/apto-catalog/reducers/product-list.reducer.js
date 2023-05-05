import update from 'immutability-helper';

const ReducerInject = ['AptoReducersProvider', '$windowProvider'];
const Reducer = function(AptoReducersProvider, $windowProvider) {
    const TYPE_NS = 'APTO_PRODUCT_LIST_';
    const $window = $windowProvider.$get();
    let sessionRestored = false;
    const initialState = {
        products: [],
        categories: [],
        categoryTree: [],
        selectedNode: {},
        expandedNodes: [],
        categoryTreeFilter: [],
        filterProperties: {},
        initialProperties: {},
        productPropertyFilter: [],
        currentCategoryFilter: '',
        currentCategoryFilterParents: [],
        sessionRestored: false,
        filterSaved: false
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    this.reducer = function (state, action) {
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
            //state = applySessionStorage(state);
        }

        switch (action.type) {
            case getType('FETCH_PRODUCT_LIST_FULFILLED'): {
                let result = action.payload.data.result.data ? action.payload.data.result.data : [];
                result = addFilteredPropertyToProducts(result);
                state = update(state, {
                    products: {
                        $set: result
                    }
                });
                return state;
            }
            case getType('FETCH_CATEGORIES_FULFILLED'): {
                const result = action.payload.data.result.data ? action.payload.data.result.data : [];
                state = update(state, {
                    categories: {
                        $set: result
                    }
                });
                return state;
            }
            case getType('SET_PRODUCT_PROPERTY_FILTER'): {
                state = update(state, {
                    productPropertyFilter: {
                        $set: action.payload.filter
                    },
                    filterProperties: {
                        $set: action.payload.properties
                    },
                    filterSaved: {
                        $set: true
                    }
                });
                return state;
            }
            case getType('RESET_PRODUCT_PROPERTY_FILTER'): {
                state = update(state, {
                    productPropertyFilter: {
                        $set: angular.copy(initialState.productPropertyFilter)
                    },
                    filterProperties: {
                        $set: angular.copy(state.initialProperties)
                    }
                });
                return state;
            }
            case getType('FETCH_CATEGORY_TREE_FULFILLED'): {
                const result = action.payload.data.result ? action.payload.data.result : [];
                const categoryTreeFilter = initCategoryTreeFilterLevel(result);
                state = update(state, {
                    categoryTree: {
                        $set: result
                    },
                    categoryTreeFilter: {
                        $set: categoryTreeFilter
                    }
                });
                return state;
            }
            case getType('UPDATE_CATEGORY_FILTER'): {
                const category = action.payload;
                const categoryTreeFilter = updateCategoryFilter(category, angular.copy(state.categoryTreeFilter));
                let selectedNode = {};
                let expandedNodes = [];
                let currentCategoryFilter = angular.copy(state.currentCategoryFilter);
                let parents = [];
                if (currentCategoryFilter === category.id) {
                    currentCategoryFilter = '';
                }
                else {
                    currentCategoryFilter = category.id;
                    selectedNode = getCategory(category.id, state.categoryTree);
                    parents = getAllParentCategoryIds(category.id, categoryTreeFilter, parents);
                    for (let i = 0; i < parents.length; i++) {
                        expandedNodes.push(getCategory(parents[i], state.categoryTree));
                    }
                }
                const productsFilteredByCategory = filterProductsByCategory(angular.copy(state.products), currentCategoryFilter, parents);
                const filterProperties = updateFilterPropertyState(state, productsFilteredByCategory);
                state = update(state, {
                    categoryTreeFilter: {
                        $set: categoryTreeFilter
                    },
                    currentCategoryFilter: {
                        $set: currentCategoryFilter
                    },
                    currentCategoryFilterParents: {
                        $set: parents
                    },
                    products: {
                        $set: productsFilteredByCategory
                    },
                    filterProperties: {
                        $set: filterProperties
                    },
                    productPropertyFilter: {
                        $set: []
                    },
                    filterSaved: {
                        $set: true
                    },
                    selectedNode: {
                        $set: selectedNode
                    },
                    expandedNodes: {
                        $set: expandedNodes
                    }
                });
                return state;
            }
            case getType('FETCH_FILTER_PROPERTIES_FULFILLED'): {
                const result = action.payload.data.result.data ? action.payload.data.result.data : [];
                let newFilterProperties = {};
                for (let i = 0; i < result.length; i++) {
                    result[i].checked = false;
                    for (let u = 0; u < result[i].filterCategories.length; u++) {
                        if (result[i].filterCategories[u].identifier === 'data-incomplete.' || result[i].filterCategories[u].identifier === 'external-calculation.') {
                            continue;
                        }
                        if(!newFilterProperties.hasOwnProperty(result[i].filterCategories[u].identifier)) {
                            newFilterProperties[result[i].filterCategories[u].identifier] = {};
                            newFilterProperties[result[i].filterCategories[u].identifier].name = result[i].filterCategories[u].name;
                            newFilterProperties[result[i].filterCategories[u].identifier].properties = [];
                        }
                        result[i].active = true;
                        newFilterProperties[result[i].filterCategories[u].identifier].properties.push(result[i]);

                    }
                    if(result[i].filterCategories.length === 0) {
                        if(!newFilterProperties.hasOwnProperty('noCategories')) {
                            newFilterProperties.noCategories = [];
                        }
                        newFilterProperties.noCategories.push(result[i]);
                    }
                }

                state = update(state, {
                    filterProperties: {
                        $set: newFilterProperties
                    },
                    initialProperties: {
                        $set: newFilterProperties
                    }

                });
                return state;
            }
        }
        return state;
    };

    function initCategoryTreeFilterLevel(categories, parentId) {
        let categoryFilterLevel = [];

        for (let i = 0; i < categories.length; i++) {
            const category = categories[i];
            let children = [];
            if (category.children && category.children.length > 0) {
                children = initCategoryTreeFilterLevel(category.children, category.id);
            }

            categoryFilterLevel.push({
                id: category.id,
                name: category.name,
                checked: false,
                parentId: parentId,
                children: children
            });
        }
        return categoryFilterLevel;
    }

    function updateCategoryFilter(category, categoryTree) {
        for (let i = 0; i < categoryTree.length; i++) {
            if (categoryTree[i].id === category.id) {
                categoryTree[i].checked = !categoryTree[i].checked;
                break;
            }
            else {
                categoryTree[i].checked = false;
                if (categoryTree[i].children.length > 0) {
                    categoryTree[i].children = updateCategoryFilter(category, categoryTree[i].children);
                }
            }
        }
        return categoryTree;
    }

    function getAllParentCategoryIds(categoryId, categoryTree, parentIds = []) {
        let category = getCategory(categoryId, categoryTree);
        if (category.parentId) {
            parentIds.push(category.parentId);
            parentIds = getAllParentCategoryIds(category.parentId, categoryTree, parentIds)
        }
        return parentIds;
    }

    function getCategory(categoryId, categoryTree) {
        if (typeof categoryTree === 'undefined') {
            return null;
        }
        let result = null;
        for (let i = 0; i < categoryTree.length; i++) {
            if (categoryTree[i].id === categoryId) {
                return categoryTree[i];
            }
            else {
                result = getCategory(categoryId, categoryTree[i].children);
            }
            if (result !== null) {
                return result;
            }
        }
        return null;
    }
    
    function addFilteredPropertyToProducts(products) {
        for (let i = 0; i < products.length; i++) {
            products[i].filteredByCategory = true;
        }
        return products;
    }
    
    function filterProductsByCategory(products, currentCategoryFilter, parents){
        for (let i = 0; i < products.length; i++) {
            if (isProductFilteredByCategory(products[i], currentCategoryFilter, parents)) {
                products[i].filteredByCategory = true;
            }
            else {
                products[i].filteredByCategory = false;
            }
        }
        return products;
    }

    function isProductFilteredByCategory(product, currentCategoryFilter, parents) {
        if (currentCategoryFilter === '') {
            return true;
        }
        // Category is explicitly in product and filter
        for (let i = 0; i < product.categories.length; i++) {
            if (product.categories[i].id === currentCategoryFilter) {
                return true;
            }
        }
        // Product is ONLY Part of any parent category and NOT part of ANY other category on the same level as the filter category
        // There for the category level of the product has to be less or equal to the amount of parents of the current category filter
        // TODO: Will not work if a Product is on more than one higher level CategoriesTrees
        if (product.categories.length <= parents.length) {
            for (let i = 0; i < product.categories.length; i++) {
                if (parents.indexOf(product.categories[i].id) > -1) {
                    return true;
                }
            }
        }
        // Product is not affected by category filtering
        return (product.categories.length === 0);
    }

    function updateFilterPropertyState(state, productsFilteredByCategory) {
        let activeFilterProperties = [];
        for (let i = 0; i < productsFilteredByCategory.length; i++) {
            for (let n = 0; n < productsFilteredByCategory[i].filterProperties.length; n++) {
                if (productsFilteredByCategory[i].filteredByCategory) {
                    activeFilterProperties.push(productsFilteredByCategory[i].filterProperties[n]);
                }
            }
        }
        activeFilterProperties = [...new Set(activeFilterProperties)];
        let filterProperties = angular.copy(state.initialProperties);
        for (let properties in filterProperties) {
            if( filterProperties.hasOwnProperty( properties ) ) {
                for (let i = 0; i < filterProperties[properties].properties.length; i++) {
                    filterProperties[properties].properties[i].active = activeFilterProperties.includes(filterProperties[properties].properties[i].id);
                }
            }
        }
        return filterProperties;
    }

    function getSessionStorage() {
        return JSON.parse($window.sessionStorage.getItem('aptoProductListReduxState'));
    }

    function saveStateToSessionStorage(state) {
        // save state to session storage
        const sessionState = JSON.stringify({
            products: state.products,
            filterProperties: state.filterProperties,
            initialProperties: state.initialProperties,
            productPropertyFilter: state.productPropertyFilter,
            currentCategoryFilter: state.currentCategoryFilter,
            currentCategoryFilterParents: state.currentCategoryFilterParents
        });
        $window.sessionStorage.setItem('aptoProductListReduxState', sessionState);
    }

    function applySessionStorage(state) {
        let sessionState = getSessionStorage();

        // if state has no configuration in sessionStorage or an empty state return state
        if (
            null === sessionState ||
            (sessionState.state && Object.keys(sessionState.state).length <= 0)
        ) {
            return state;
        }

        state = loadSessionStorage(state);
        return state;
    }

    function loadSessionStorage(state) {
        return state;
        let sessionState = getSessionStorage();
        state = update(state, {
            products: {
                $set: sessionState.products
            },
            filterProperties: {
                $set: sessionState.filterProperties
            },
            initialProperties: {
                $set: sessionState.initialProperties
            },
            productPropertyFilter: {
                $set: sessionState.productPropertyFilter
            },
            currentCategoryFilter: {
                $set: sessionState.currentCategoryFilter
            },
            currentCategoryFilterParents: {
                $set: sessionState.currentCategoryFilterParents
            },
            categoryTree: {
                $set: sessionState.categoryTree
            },
            categoryTreeFilter: {
                $set: sessionState.categoryTreeFilter
            },
            sessionRestored: {
                $set: true
            }
        });

        return state;
    }

    AptoReducersProvider.addReducer('productList', this.reducer);

    this.$get = function() {};
};

Reducer.$inject = ReducerInject;

export default ['ProductListReducer', Reducer];