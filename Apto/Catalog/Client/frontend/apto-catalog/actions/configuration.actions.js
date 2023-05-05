const ConfigurationActionsInject = ['MessageBusFactory', 'LanguageFactory', 'APTO_RENDER_IMAGE_PERSPECTIVES'];
const ConfigurationActions = function(MessageBusFactory, LanguageFactory, APTO_RENDER_IMAGE_PERSPECTIVES) {
    const TYPE_NS = 'APTO_CONFIGURATION_';
    function getType(type) {
        return TYPE_NS + type;
    }

    // @todo op-10902 remove?
    function setConfigurationId(configurationId) {
        return {
            type: getType('SET_CONFIGURATION_ID'),
            payload: configurationId
        }
    }

    // @todo op-10902 remove?
    function setConfigurationType(configurationType) {
        return {
            type: getType('SET_CONFIGURATION_TYPE'),
            payload: configurationType
        }
    }

    function fetchBasketConfiguration(configurationId) {
        return {
            type: getType('FETCH_BASKET_CONFIGURATION'),
            payload: MessageBusFactory.query('FindBasketConfiguration', [configurationId])
        }
    }

    function fetchCustomerConfiguration(configurationId) {
        return {
            type: getType('FETCH_CUSTOMER_CONFIGURATION'),
            payload: MessageBusFactory.query('FindCustomerConfiguration', [configurationId])
        }
    }

    function fetchOrderConfiguration(configurationId) {
        return {
            type: getType('FETCH_ORDER_CONFIGURATION'),
            payload: MessageBusFactory.query('FindOrderConfiguration', [configurationId])
        }
    }

    function fetchProposedConfigurations(productId, searchString) {
        if (typeof searchString === "undefined") {
            searchString = ''
        }
        return {
            type: getType('FETCH_PROPOSED_CONFIGURATIONS'),
            payload: MessageBusFactory.query('FindProposedConfigurations', [productId, searchString])
        }
    }

    function getConfigurationState(productId, compressedState, intention) {
        return {
            type: getType('GET_CONFIGURATION_STATE'),
            payload: MessageBusFactory.query('GetConfigurationState', [productId, compressedState, intention])
        }
    }

    function addStateToHistory() {
        return {
            type: getType('ADD_STATE_TO_HISTORY')
        }
    }

    function addBasketConfiguration(productId, compressedState, sessionCookies, quantity, perspectives, additionalData) {
        if (!perspectives && APTO_RENDER_IMAGE_PERSPECTIVES.basketPerspectives) {
            perspectives = APTO_RENDER_IMAGE_PERSPECTIVES.basketPerspectives;
        }

        if (!perspectives && !APTO_RENDER_IMAGE_PERSPECTIVES.basketPerspectives) {
            perspectives = [APTO_RENDER_IMAGE_PERSPECTIVES.default];
        }

        if (typeof perspectives === 'string') {
            perspectives = [perspectives];
        }

        if (!additionalData) {
            additionalData = {};
        }

        return {
            type: getType('ADD_BASKET_CONFIGURATION'),
            payload: MessageBusFactory.command('AddBasketConfiguration', [productId, compressedState, sessionCookies, LanguageFactory.activeLanguage.isocode, quantity, perspectives, additionalData])
        }
    }

    function updateBasketConfiguration(productId, configurationId, compressedState, sessionCookies, quantity, perspectives, additionalData) {
        if (!perspectives && APTO_RENDER_IMAGE_PERSPECTIVES.basketPerspectives) {
            perspectives = APTO_RENDER_IMAGE_PERSPECTIVES.basketPerspectives;
        }

        if (!perspectives && !APTO_RENDER_IMAGE_PERSPECTIVES.basketPerspectives) {
            perspectives = [APTO_RENDER_IMAGE_PERSPECTIVES.default];
        }

        if (typeof perspectives === 'string') {
            perspectives = [perspectives];
        }

        if (!additionalData) {
            additionalData = {};
        }

        return {
            type: getType('UPDATE_BASKET_CONFIGURATION'),
            payload: MessageBusFactory.command('UpdateBasketConfiguration', [productId, configurationId, compressedState, sessionCookies, LanguageFactory.activeLanguage.isocode, quantity, perspectives, additionalData])
        }
    }

    function addCustomerConfiguration(productId, name, compressedState, sessionCookies, customer) {
        return {
            type: getType('ADD_CUSTOMER_CONFIGURATION'),
            payload: MessageBusFactory.command('AddCustomerConfiguration', [productId, name, compressedState, sessionCookies, LanguageFactory.activeLanguage.isocode, customer])
        }
    }

    function addProposedConfiguration(productId, compressedState, uuid = null) {
        return {
            type: getType('ADD_PROPOSED_CONFIGURATION'),
            payload: MessageBusFactory.command('AddProposedConfiguration', [productId, compressedState, uuid])
        }
    }

    function addGuestConfiguration(productId, compressedState, email, name, sendMail, id, payload) {
        if (!name) {
            name = '';
        }

        if (!sendMail && sendMail !== false) {
            sendMail = true;
        }

        if (!id) {
            id = '';
        }

        if (!payload) {
            payload = {};
        }

        return {
            type: getType('ADD_GUEST_CONFIGURATION'),
            payload: MessageBusFactory.command('AddGuestConfiguration', [productId, compressedState, email, name, sendMail, id, payload])
        }
    }

    function addOfferConfiguration(productId, compressedState, email, name, payload) {
        if (!name) {
            name = '';
        }

        if (!payload) {
            payload = {};
        }

        return {
            type: getType('ADD_OFFER_CONFIGURATION'),
            payload: MessageBusFactory.command('AddOfferConfiguration', [productId, compressedState, email, name, payload])
        }
    }

    function addCodeConfiguration(productId, compressedState, id) {
        return {
            type: getType('ADD_CODE_CONFIGURATION'),
            payload: MessageBusFactory.command('AddCodeConfiguration', [productId, compressedState, id])
        }
    }

    function softRuleActive(softRules) {
        return {
            type: getType('SOFT_RULES_ACTIVE'),
            payload: {
                softRules: softRules
            }
        }
    }

    function setAcceptedSoftRules(acceptedSoftRules) {
        return {
            type: getType('SET_ACCEPTED_SOFT_RULES'),
            payload: {
                acceptedSoftRules: acceptedSoftRules
            }
        }
    }

    function initProduct(productId, configurationType, configurationId) {
        return (dispatch) => {
            // define configuration query
            let configurationQuery = null;
            if (configurationType && configurationId) {
                switch (configurationType) {
                    case 'basket': {
                        configurationQuery = MessageBusFactory.query('FindBasketConfiguration', [configurationId]);
                        break;
                    }
                    case 'customer': {
                        configurationQuery = MessageBusFactory.query('FindCustomerConfiguration', [configurationId]);
                        break;
                    }
                    case 'order': {
                        configurationQuery = MessageBusFactory.query('FindOrderConfiguration', [configurationId]);
                        break;
                    }
                    case 'proposed': {
                        configurationQuery = MessageBusFactory.query('FindProposedConfiguration', [configurationId]);
                        break;
                    }
                    case 'guest': {
                        configurationQuery = MessageBusFactory.query('FindGuestConfiguration', [configurationId]);
                        break;
                    }
                    case 'immutable': {
                        configurationQuery = MessageBusFactory.query('FindImmutableConfiguration', [configurationId])
                        break;
                    }
                    case 'code': {
                        configurationQuery = MessageBusFactory.query('FindCodeConfiguration', [configurationId]);
                        break;
                    }
                }
            }

            if (null === configurationQuery) {
                // load product
                return MessageBusFactory.query('FindConfigurableProduct', [productId]).then((response) => {
                    // init product without configuration
                    return dispatch({
                        type: getType('INIT_PRODUCT'),
                        payload: {
                            product: response.data.result
                        }
                    });
                });
            } else {
                // load product and configuration
                return configurationQuery.then((response) => {
                    const configuration = response.data.result;

                    if (configurationType === 'immutable') {
                        return MessageBusFactory.query('FindConfigurableProductByConfiguration', [configuration.productId, configurationType, configurationId]).then((response) => {
                            const product = response.data.result;
                            return dispatch({
                                type: getType('INIT_PRODUCT'),
                                payload: {
                                    product: product,
                                    configuration: configuration
                                }
                            });
                        });
                    }

                    return MessageBusFactory.query('FindConfigurableProduct', [configuration.productId]).then((response) => {
                        const product = response.data.result;

                        // init persisted properties
                        dispatch({
                            type: 'APTO_PERSISTED_PROPERTIES_INIT_PRODUCT',
                            payload: {
                                product: product,
                                configuration: configuration
                            }
                        });

                        // init product with configuration
                        return dispatch({
                            type: getType('INIT_PRODUCT'),
                            payload: {
                                product: product,
                                configuration: configuration
                            }
                        });
                    });
                })
            }
        };
    }

    function fetchElementComputableValues(compressedState, sectionId, elementId) {
        return {
            type: getType('FETCH_ELEMENT_COMPUTABLE_VALUES'),
            payload: MessageBusFactory.query('FindElementComputableValues', [compressedState, sectionId, elementId])
        }
    }

    function setProductView(productView) {
        return {
            type: getType('SET_PRODUCT_VIEW'),
            payload: productView
        }
    }

    function isInitialized(initialized) {
        return {
            type: getType('IS_INITIALIZED'),
            payload: initialized
        }
    }

    return {
        isInitialized: isInitialized,
        setConfigurationId: setConfigurationId,
        setConfigurationType: setConfigurationType,
        fetchBasketConfiguration: fetchBasketConfiguration,
        fetchCustomerConfiguration: fetchCustomerConfiguration,
        fetchOrderConfiguration: fetchOrderConfiguration,
        addStateToHistory: addStateToHistory,
        addBasketConfiguration: addBasketConfiguration,
        updateBasketConfiguration: updateBasketConfiguration,
        addCustomerConfiguration: addCustomerConfiguration,
        addProposedConfiguration: addProposedConfiguration,
        addGuestConfiguration: addGuestConfiguration,
        addOfferConfiguration: addOfferConfiguration,
        addCodeConfiguration: addCodeConfiguration,
        fetchProposedConfigurations: fetchProposedConfigurations,
        getConfigurationState: getConfigurationState,
        softRuleActive: softRuleActive,
        setAcceptedSoftRules: setAcceptedSoftRules,
        initProduct: initProduct,
        fetchElementComputableValues: fetchElementComputableValues,
        setProductView: setProductView
    };
};

ConfigurationActions.$inject = ConfigurationActionsInject;

export default ['ConfigurationActions', ConfigurationActions];
