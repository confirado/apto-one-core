import update from 'immutability-helper';

const IndexReducerInject = ['AptoReducersProvider', 'APTO_DEFAULT_LOCALE', 'APTO_LANGUAGES', 'APTO_SHOP_CONTEXT', 'APTO_DEFAULT_CURRENCY', 'APTO_DEFAULT_CUSTOMER_GROUP', 'APTO_CONTENT_SNIPPETS'];
const IndexReducer = function(AptoReducersProvider, APTO_DEFAULT_LOCALE, APTO_LANGUAGES, APTO_SHOP_CONTEXT, APTO_DEFAULT_CURRENCY, APTO_DEFAULT_CUSTOMER_GROUP, APTO_CONTENT_SNIPPETS) {
    const TYPE_NS = 'APTO_INDEX_';
    const initialState = {
        languages: [],
        activeLanguage: {},
        shopConnectorConfigured: false,
        shopSession: {
            sessionCookies: [],
            loggedIn: false,
            basket: {
                quantity: 0,
                articles: []
            },
            user: {},
            customerGroup: APTO_DEFAULT_CUSTOMER_GROUP,
            displayCurrency: APTO_DEFAULT_CURRENCY.displayCurrency,
            shopCurrency: APTO_DEFAULT_CURRENCY.shopCurrency,
            url: {}
        },
        quantity: null,
        sidebarRightOpen: false,
        sidebarRightOpenHTML: false,
        scrollbarWidth: 0,
        spinnerPage: false,
        spinnerRenderImage: false,
        spinnerRandomConfiguration: false,
        metaData: {},
        contentSnippets: APTO_CONTENT_SNIPPETS
    };

    function getType(type) {
        return TYPE_NS + type;
    }

    function setUserState(state, result) {
        state = update(state, {
            shopSession: {
                sessionCookies: {
                    $set: result.sessionCookies
                },
                loggedIn: {
                    $set: result.loggedIn
                },
                user: {
                    $set: result.user
                }
            }
        });
        return state;
    }

    function setBasketState(state, result) {
        state = update(state, {
            shopSession: {
                basket: {
                    $set: result.basket
                }
            }
        });
        return state;
    }

    function setCurrencyState(state, result) {
        state = update(state, {
            shopSession: {
                displayCurrency: {
                    $set: result.displayCurrency
                },
                shopCurrency: {
                    $set: result.shopCurrency
                }
            }
        });
        return state;
    }

    function setCustomerGroupState(state, result) {
        state = update(state, {
            shopSession: {
                customerGroup: {
                    $set: result.customerGroup
                }
            }
        });
        return state;
    }

    function setLanguages(state, languages) {
        state = update(state, {
            languages: {
                $set: languages
            }
        });

        if (!state.activeLanguage.id) {
            state = setActiveLanguageByLocale(state, APTO_DEFAULT_LOCALE);
        }
        return state;
    }

    function setActiveLanguageByLocale(state, locale) {
        for (let i = 0; i < state.languages.length; i++) {
            if (state.languages[i].isocode === locale) {
                state = update(state, {
                    activeLanguage: {
                        $set: state.languages[i]
                    }
                });
                return state;
            }
        }

        state = update(state, {
            activeLanguage: {
                $set: state.languages[0]
            }
        });

        return state;
    }

    function setShopConnectorConfigured(state, locale) {
        if (!APTO_SHOP_CONTEXT.connectorUrl) {
            return update(state, {
                shopConnectorConfigured: {
                    $set: false
                }
            });
        }

        if (!APTO_SHOP_CONTEXT.connectorUrl[locale]) {
            return update(state, {
                shopConnectorConfigured: {
                    $set: false
                }
            });
        }

        return update(state, {
            shopConnectorConfigured: {
                $set: true
            }
        });
    }

    this.index = function (state, action) {
        if (typeof state === "undefined") {
            state = angular.copy(initialState);
            state = setLanguages(state, APTO_LANGUAGES);
            state = setShopConnectorConfigured(state, APTO_DEFAULT_LOCALE);
        }

        switch (action.type) {
            case getType('LANGUAGES_FETCH_FULFILLED'):
                return setLanguages(state, action.payload.data.result.data);

            case getType('SET_ACTIVE_LANGUAGE'):
                state = update(state, {
                    activeLanguage: {
                        $set: action.payload
                    }
                });
                return state;

            case getType('SET_LOCALE_FULFILLED'):
                state = setActiveLanguageByLocale(state, action.payload.data.currentLocale);
                state = setShopConnectorConfigured(state, action.payload.data.currentLocale);
                return state;

            case getType('SHOP_SESSION_FETCH_FULFILLED'):
                state = update(state, {
                    shopSession: {
                        $set: action.payload.data.result
                    }
                });
                return state;

            case getType('SHOP_LOGIN_FULFILLED'):
                if (true === action.payload.data.result.loggedIn) {
                    state = setUserState(state, action.payload.data.result);
                    state = setCurrencyState(state, action.payload.data.result);
                    state = setCustomerGroupState(state, action.payload.data.result);
                    state = setBasketState(state, action.payload.data.result);
                }
                return state;

            case getType('SHOP_LOGOUT_FULFILLED'):
                state = setUserState(state, action.payload.data.result);
                state = setCurrencyState(state, action.payload.data.result);
                state = setCustomerGroupState(state, action.payload.data.result);
                state = setBasketState(state, action.payload.data.result);
                return state;

            case getType('SHOP_REMOVE_FROM_BASKET_FULFILLED'):
                state = setBasketState(state, action.payload.data.result);
                return state;

            case getType('FETCH_SHOP_SESSION_CUSTOMER_GROUP_BY_EXTERNAL_ID_FULFILLED'):
                let customerGroup = {};

                if (action.payload.data.result) {
                    customerGroup = {
                        id: action.payload.data.result.externalId,
                        name: action.payload.data.result.name,
                        inputGross: action.payload.data.result.inputGross,
                        showGross: action.payload.data.result.showGross
                    }
                }

                state = setCustomerGroupState(state,{
                    customerGroup: customerGroup
                });
                return state;

            case getType('SET_QUANTITY'):
                state = update(state, {
                    quantity: {
                        $set: action.payload
                    }
                });
                return state;

            case getType('OPEN_SIDEBAR_RIGHT'):
                state = update(state, {
                    sidebarRightOpen: {
                        $set: true
                    }
                });
                return state;

            case getType('CLOSE_SIDEBAR_RIGHT'):
                state = update(state, {
                    sidebarRightOpen: {
                        $set: false
                    }
                });
                return state;

            case getType('SET_SIDEBAR_RIGHT_HTML'):
                state = update(state, {
                    sidebarRightOpenHTML: {
                        $set: state.sidebarRightOpen
                    }
                });
                return state;

            case getType('SET_SCROLLBAR_WIDTH'):
                state = update(state, {
                    scrollbarWidth: {
                        $set: action.payload
                    }
                });
                return state;

            case getType('SET_META_DATA'):
                state = update(state, {
                    metaData: {
                        $set: action.payload
                    }
                });
                return state;

            // @todo remove relation to catalog module
            case 'APTO_RENDER_IMAGE_FETCH_CURRENT_RENDER_IMAGE_PENDING':
                state = update(state, {
                    spinnerRenderImage: {
                        $set: true
                    }
                });
                return state;

            // @todo remove relation to catalog module
            case 'APTO_RENDER_IMAGE_FETCH_CURRENT_RENDER_IMAGE_REJECTED':
            case 'APTO_RENDER_IMAGE_FETCH_CURRENT_RENDER_IMAGE_FULFILLED':
                state = update(state, {
                    spinnerRenderImage: {
                        $set: false
                    }
                });
                return state;

            // @todo remove relation to catalog module
            case 'APTO_CONFIGURATION_ADD_BASKET_CONFIGURATION_PENDING':
            case 'APTO_CONFIGURATION_UPDATE_BASKET_CONFIGURATION_PENDING':
                state = update(state, {
                    spinnerPage: {
                        $set: true
                    },
                    sidebarRightOpenHTML: {
                        $set: true
                    }
                });
                return state;

            // @todo remove relation to catalog module
            case 'APTO_CONFIGURATION_ADD_BASKET_CONFIGURATION_REJECTED':
            case 'APTO_CONFIGURATION_ADD_BASKET_CONFIGURATION_FULFILLED':
            case 'APTO_CONFIGURATION_UPDATE_BASKET_CONFIGURATION_REJECTED':
            case 'APTO_CONFIGURATION_UPDATE_BASKET_CONFIGURATION_FULFILLED':
                state = update(state, {
                    spinnerPage: {
                        $set: false
                    },
                    sidebarRightOpenHTML: {
                        $set: false
                    }
                });
                return state;

            case 'APTO_RANDOM_CONFIGURATION_FETCH_PREVIEW_IMAGE_PENDING':
                state = update(state, {
                    spinnerRandomConfiguration: {
                        $set: true
                    }
                });
                return state;

            case 'APTO_RANDOM_CONFIGURATION_FETCH_PREVIEW_IMAGE_FULFILLED':
            case 'APTO_RANDOM_CONFIGURATION_FETCH_PREVIEW_IMAGE_REJECTED':
                state = update(state, {
                    spinnerRandomConfiguration: {
                        $set: false
                    }
                });
                return state;
        }

        return state;
    };

    AptoReducersProvider.addReducer('index', this.index);

    this.$get = function() {};
};

IndexReducer.$inject = IndexReducerInject;

export default ['IndexReducer', IndexReducer];