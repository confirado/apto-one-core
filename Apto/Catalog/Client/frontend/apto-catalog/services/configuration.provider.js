import { ActionCreators as ReduxUndoActionCreators } from 'redux-undo';
import { Promise } from "es6-promise";

import AptoLoadConfigurationDialogController from '../dialogs/load-session-configuration/load-session-configuration';
import AptoLoadConfigurationDialogTemplate from '../dialogs/load-session-configuration/load-session-configuration.html';

import AptoSoftRuleDialogController from '../dialogs/soft-rule/soft-rule.controller';
import AptoSoftRuleDialogTemplate from '../dialogs/soft-rule/soft-rule.controller.html';

const ConfigurationProviderInject = [];
const ConfigurationProvider = function() {
    const self = this;

    self.getCompressedState = function (configurationState, quantity) {
        let compressedState = {
            quantity: quantity ? quantity.value : 1
        };

        for (let sectionId in configurationState) {
            if (configurationState.hasOwnProperty(sectionId)) {
                let sectionNode = configurationState[sectionId];
                if (sectionNode['state']['active'] !== true || sectionNode['state']['disabled'] !== false) {
                    continue;
                }

                compressedState[sectionId] = {};
                for (let elementId in sectionNode.elements) {
                    if (sectionNode.elements.hasOwnProperty(elementId)) {
                        let elementNode = sectionNode.elements[elementId];
                        if (elementNode['state']['active'] !== true || elementNode['state']['disabled'] !== false) {
                            continue;
                        }

                        if (elementNode['state']['values'] !== null) {
                            compressedState[sectionId][elementId] = {};
                            compressedState[sectionId][elementId] = elementNode['state']['values'];
                        } else {
                            compressedState[sectionId][elementId] = true;
                        }
                    }
                }
            }
        }

        return compressedState;
    };

    self.stateElementIsSelected = function (configurationState, sectionId, elementId) {
        return configurationState[sectionId]['elements'][elementId]['state']['active'];
    };

    self.stateSectionIsSelected = function (configurationState, sectionId) {
        return configurationState[sectionId]['state']['active'];
    };

    self.objectPathDefined = function(obj /*, level1, level2, ... levelN*/) {
        let args = Array.prototype.slice.call(arguments, 1);

        for (let i = 0; i < args.length; i++) {
            if (!obj || !obj.hasOwnProperty(args[i])) {
                return false;
            }
            obj = obj[args[i]];
        }
        return true;
    };

    self.sectionHasMultipleActiveElements = function (configurationState, sectionId) {
        let count = 0;
        let elements = configurationState[sectionId]['elements'];
        for (let elementId in elements) {
            if (elements.hasOwnProperty(elementId)) {
                if (elements[elementId]['state']['active'] === true) {
                    count++;
                    if (count > 1) {
                        return true;
                    }
                }
            }
        }
        return false;
    };

    self.$getInject = [
        '$window',
        '$document',
        '$location',
        '$routeParams',
        '$ngRedux',
        '$rootScope',
        'ngDialog',
        'APTO_CONFIGURATION_REDUCER_AUTOLOAD_SESSION_STORAGE',
        'APTO_CONFIGURATION_REDUCER_DISABLE_SESSION_STORAGE',
        'APTO_CONFIGURATION_SERVICE_DISABLE_RENDER_IMAGE_FETCH',
        'LanguageFactory',
        'ConfigurationActions',
        'RenderImageActions',
        'StatePriceActions',
        'HumanReadableStateActions',
        'IndexActions',
        'ProductActions',
        'SnippetFactory',
        'APTO_INLINE',
        'APTO_CONFIGURATION_SERVICE_DEFAULT_REPAIR_SETTINGS'
    ];

    self.$get = function(
        $window,
        $document,
        $location,
        $routeParams,
        $ngRedux,
        $rootScope,
        ngDialog,
        APTO_CONFIGURATION_REDUCER_AUTOLOAD_SESSION_STORAGE,
        APTO_CONFIGURATION_REDUCER_DISABLE_SESSION_STORAGE,
        APTO_CONFIGURATION_SERVICE_DISABLE_RENDER_IMAGE_FETCH,
        LanguageFactory,
        ConfigurationActions,
        RenderImageActions,
        StatePriceActions,
        HumanReadableStateActions,
        IndexActions,
        ProductActions,
        SnippetFactory,
        APTO_INLINE,
        APTO_CONFIGURATION_SERVICE_DEFAULT_REPAIR_SETTINGS
    ) {
        let initialized = false;
        let shopSessionInitialized = false;
        const redux = {};

        const mapStateToThis = function(state) {
            return {
                configurationId: state.configuration.present.configurationId,
                configurationType: state.configuration.present.configurationType,
                configurationState: state.configuration.present.configurationState,
                failedRules: state.configuration.present.failedRules,
                initialized: state.configuration.present.initialized,
                currentPerspective: state.renderImage.currentPerspective,
                perspectives: state.renderImage.perspectives,
                productId: state.configuration.present.productId,
                productSeoUrl: state.configuration.present.productSeoUrl,
                useStepByStep: state.configuration.present.useStepByStep,
                countPreviousStates: state.configuration.past.length,
                countNextStates: state.configuration.future.length,
                statePrice: state.statePrice.statePrice,
                humanReadableState: state.humanReadableState.humanReadableState,
                sortOrderSections: state.configuration.present.sortOrderSections,
                shopSession: state.index.shopSession,
                activeLanguage: state.index.activeLanguage,
                productDetail: state.configuration.present.raw.product,
                rawConfiguration: state.configuration.present.raw.configuration,
                selectedSection: state.product.selectedSection,
                quantity: state.index.quantity
            }
        };

        $ngRedux.connect(mapStateToThis, {
            isInitialized: ConfigurationActions.isInitialized,
            initProduct: ConfigurationActions.initProduct,
            fetchCurrentStatePrice: StatePriceActions.fetchCurrentStatePrice,
            setCurrentPerspective: RenderImageActions.setCurrentPerspective,
            fetchCurrentRenderImage: RenderImageActions.fetchCurrentRenderImage,
            fetchComputedProductValues: ProductActions.fetchComputedValues,
            fetchCurrentHumanReadableState: HumanReadableStateActions.fetchCurrentHumanReadableState,
            addStateToHistory: ConfigurationActions.addStateToHistory,
            addBasketConfiguration: ConfigurationActions.addBasketConfiguration,
            updateBasketConfiguration: ConfigurationActions.updateBasketConfiguration,
            addProposedConfiguration: ConfigurationActions.addProposedConfiguration,
            fetchProposedConfigurations: ConfigurationActions.fetchProposedConfigurations,
            getConfigurationState: ConfigurationActions.getConfigurationState,
            addGuestConfiguration: ConfigurationActions.addGuestConfiguration,
            addOfferConfiguration: ConfigurationActions.addOfferConfiguration,
            addCodeConfiguration: ConfigurationActions.addCodeConfiguration,
            softRuleActive: ConfigurationActions.softRuleActive,
            setAcceptedSoftRules: ConfigurationActions.setAcceptedSoftRules,
            fetchElementComputableValues: ConfigurationActions.fetchElementComputableValues,
            setProductView: ConfigurationActions.setProductView,
            shopSessionFetch: IndexActions.shopSessionFetch,
            openSidebarRight: IndexActions.openSidebarRight,
            setQuantity: IndexActions.setQuantity,
            setProductDetail: ProductActions.setProductDetail,
            setSelectedSection: ProductActions.selectSection,
            resetSelectedSection: ProductActions.resetSection
        })(redux);

        const cartEvent = new Event('itemAddedToCart');

        function init(product = null) {
            // reset initialized status
            redux.isInitialized(false);

            // reset selected section
            redux.resetSelectedSection();

            // set product from route
            let productId = $routeParams.productId;
            let configurationType = $routeParams.configurationType;
            let configurationId = $routeParams.configurationId;

            // set product from inline variables
            if (!productId && !configurationType && !configurationId && APTO_INLINE ) {
                productId = APTO_INLINE.productId;
                configurationType = APTO_INLINE.configurationType;
                configurationId = APTO_INLINE.configurationId;
            }

            // set product from given product
            if (product) {
                productId = product.productId;
                configurationType = product.configurationType;
                configurationId = product.configurationId;
            }

            // set product from session storage
            const sessionStorage = getSessionStorage();
            let loadFromSessionStorage = false;
            let sessionStoragePromise = new Promise((resolve, reject) => {
                resolve({value: false});
            });

            if (sessionStorage !== null && !APTO_CONFIGURATION_REDUCER_DISABLE_SESSION_STORAGE) {
                if (APTO_CONFIGURATION_REDUCER_AUTOLOAD_SESSION_STORAGE) {
                    productId = sessionStorage.productId;
                    configurationType = sessionStorage.configurationType;
                    configurationId = sessionStorage.configurationId;
                    loadFromSessionStorage = true;
                } else {
                    sessionStoragePromise = openLoadConfigurationDialog().then((data) => {
                        if (data.value === true) {
                            productId = sessionStorage.productId;
                            configurationType = sessionStorage.configurationType;
                            configurationId = sessionStorage.configurationId;
                            loadFromSessionStorage = true;
                        }
                    });
                }
            }

            return sessionStoragePromise.then((data) => {
                // first step is to init configurable product
                return redux.initProduct(
                    productId,
                    configurationType,
                    configurationId
                ).then(() => {
                    // @deprecated set product details to product reducer
                    redux.setProductDetail(redux.productDetail);

                    // set compressed state
                    let compressedState = (product && product.compressedState) ? product.compressedState : {};
                    if (configurationId) {
                        compressedState = redux.rawConfiguration.state;
                    }

                    if (loadFromSessionStorage === true) {
                        compressedState = sessionStorage.state;
                    }

                    let intention = {
                        init: !(product && product.compressedState),
                        complete: getCompletedSections()
                    };

                    // prohibit init (default values) on restore OP-12863
                    if(configurationType) {
                        intention.init = false;
                    }

                    return initShopSession().then(() => {
                        return handleGetConfigurationState(intention, compressedState).then(() => {
                            // @todo op-10902
                            // @deprecated set local initialized variables
                            initialized = true;
                            shopSessionInitialized = true;

                            // set reducer initialized
                            redux.isInitialized(true);

                            // send event configuration service initialized
                            $rootScope.$emit('APTO_CONFIGURATION_SERVICE_INITIALIZED');
                            $rootScope.$emit('APTO_CONFIGURATION_SERVICE_SHOP_SESSION_INITIALIZED');
                        }).catch((e) => {
                            if(e.initStateException) {
                                handleException(e, 'Fehler beim inititalisieren des Produktes');
                            } else {
                                throw e;
                            }
                        });
                    });
                });
            });
        }

        function initShopSession() {
            const possiblePromise = redux.shopSessionFetch(redux.activeLanguage.isocode);

            if (!possiblePromise.then) {
                shopSessionInitialized = true;
                $rootScope.$emit('APTO_CONFIGURATION_SERVICE_SHOP_SESSION_INITIALIZED');
                return new Promise((resolve, reject) => {
                    resolve();
                })
            }

            return possiblePromise.then(()=> {
                // init quantity from shop basket
                let article = getBasketArticleByConfigId(redux.configurationId);
                if (article) {
                    redux.setQuantity({
                        value: article.quantity
                    });
                }
            });
        }

        function openLoadConfigurationDialog() {
            return ngDialog.open({
                template: AptoLoadConfigurationDialogTemplate,
                plain: true,
                controller: AptoLoadConfigurationDialogController,
                className: 'ngdialog-theme-default',
                width: '360px',
                showClose: false,
                closeByEscape: false,
                closeByNavigation: false,
                closeByDocument: false
            }).closePromise;
        }

        function saveStateToSessionStorage() {
            // save state to session storage
            const sessionState = JSON.stringify({
                configurationId: redux.configurationId,
                configurationType: redux.configurationType,
                productId: redux.productId,
                productSeoUrl: redux.productSeoUrl,
                state: getCompressedState()
            });
            $window.sessionStorage.setItem('aptoConfigurationReduxState', sessionState);
        }

        function getSessionStorage() {
            return JSON.parse($window.sessionStorage.getItem('aptoConfigurationReduxState'));
        }

        function clearSessionStorage() {
            // @todo op-10902 did we need to clear storage anymore?
            //  Was happen after user decides to continue and dont load founded configuration.
            //  If we clear session storage after that decision next load will not pop up the dialog until changes on configuration are done.
            $window.sessionStorage.setItem('aptoConfigurationReduxState', null);
        }

        function isInitialized() {
            return initialized;
        }

        function isShopSessionInitialized() {
            return shopSessionInitialized;
        }

        function previousStateAvailable() {
            return redux.countPreviousStates > 1;
        }

        function nextStateAvailable() {
            return redux.countNextStates > 0;
        }

        function previousState() {
            // @ todo why is the first added state not a valid state? its setting after init and after getting the price
            if (redux.countPreviousStates <= 1) {
                return;
            }

            $ngRedux.dispatch(ReduxUndoActionCreators.undo());
            saveStateToSessionStorage();
            fetchCurrentRenderImage();
            fetchComputedProductValues();
            // @todo missing fetchCurrentStatePrice(); ???
            fetchCurrentHumanReadableState();
            handleOnConfigurationChanged();
        }

        function nextState() {
            if (redux.countNextStates <= 0) {
                return;
            }

            $ngRedux.dispatch(ReduxUndoActionCreators.redo());
            saveStateToSessionStorage();
            fetchCurrentRenderImage();
            fetchComputedProductValues();
            // @todo missing fetchCurrentStatePrice(); ???
            fetchCurrentHumanReadableState();
            handleOnConfigurationChanged();
        }

        function setSectionComplete(sectionId, complete) {
            let completedSections = getCompletedSections();
            completedSections.push({
                sectionId: sectionId,
                complete: complete
            });

            redux.getConfigurationState(
                redux.productId,
                self.getCompressedState(redux.configurationState, redux.quantity),
                {
                    complete: completedSections
                }
            ).then((response)=>{
                continueWithNextSection();
            });
        }

        function getCompletedSections(filter = []) {
            let completedSections = [];
            for (let sectionId in redux.configurationState) {
                if (!redux.configurationState.hasOwnProperty(sectionId) || !redux.configurationState[sectionId].state.complete || filter.includes(sectionId)) {
                    continue;
                }

                completedSections.push({
                    sectionId: sectionId,
                    complete: true
                });
            }

            return completedSections;
        }

        function handleGetConfigurationState(intention, compressedState, addStateToHistory = true, continueWithNextSectionFlag = false) {
            if (null !== APTO_CONFIGURATION_SERVICE_DEFAULT_REPAIR_SETTINGS && !intention.repair) {
                intention.repair = APTO_CONFIGURATION_SERVICE_DEFAULT_REPAIR_SETTINGS;
            }

            return redux.getConfigurationState(
                redux.productId,
                compressedState,
                intention
            ).then((response) => {
                if (addStateToHistory === true) {
                    redux.addStateToHistory();
                }

                fetchCurrentStatePrice();
                fetchCurrentRenderImage();
                fetchComputedProductValues();
                fetchCurrentHumanReadableState();
                handleOnConfigurationChanged();

                if (continueWithNextSectionFlag) {
                    continueWithNextSection();
                }

                if (redux.initialized) {
                    saveStateToSessionStorage();
                }
            }).catch((e) => {
                if(e.hardRule || e.validateValueException) {
                    handleException(e);
                } else {
                    throw e;
                }

                // @todo op-10902 add soft rule handling for new server side configuration state handling
                /* old soft rule handling
                redux.softRuleActive(e.failedRules);
                const promise = handleSoftRuleException(e);
                return promise.then(function (data) {
                    if(data.value){
                        let acceptedSoftRules = [];
                        for( let i = 0; i < e.failedRules.length; i++)
                        {
                            acceptedSoftRules.push(e.failedRules[i].id)
                        }
                        redux.setAcceptedSoftRules(acceptedSoftRules);
                        return setSingleValue(sectionId, elementId, property, value, addStateToHistory, true);
                    }
                    return false;
                });
                */
            });
        }

        function setSingleValue(sectionId, elementId, property, value, addStateToHistory, continueWithNextSectionFlag = false) {
            // @todo op-10902 remove this annoying construct of logic, its used to prevent remove last element in section if section is mandatory and multiple
            // this code was used to prevent removing last element on mandatory sections
            // i think we should not use this behaviour anymore because it can make it easier for customers in special rule constellations when the customer has to disable an element to enable an other element that is disabled currently by that element
            // if the customer remove an element on an mandatory section this case is already catched by our caonfigurationIsValid check
            /*if (elementIsSelected(sectionId, elementId) && redux.configurationState[sectionId].isMandatory === true) {
                const emptyPromise = new Promise((resolve, reject) => {
                    resolve();
                });

                if (redux.configurationState[sectionId].allowMultiple === true) {
                    if (!self.sectionHasMultipleActiveElements(redux.configurationState, sectionId)) {
                        return emptyPromise;
                    }
                } else {
                    return emptyPromise;
                }
            }*/

            let intention = {
                complete: getCompletedSections()
            };

            if(elementIsSelected(sectionId, elementId)) {
                intention.remove = [{
                    sectionId: sectionId,
                    elementId: elementId
                }];
            } else {
                intention.set = [{
                    sectionId: sectionId,
                    elementId: elementId,
                    property: property,
                    value: value
                }];
            }

            return handleGetConfigurationState(intention, getCompressedState(), addStateToHistory, continueWithNextSectionFlag);
        }

        function setElementProperties(sectionId, elementId, properties, addStateToHistory, continueWithNextSectionFlag = false) {
            let values = [];
            for (let property in properties) {
                if (!properties.hasOwnProperty(property)) {
                    continue;
                }

                values.push({
                    sectionId: sectionId,
                    elementId: elementId,
                    property: property,
                    value: properties[property]
                });
            }

            let intention = {
                set: values,
                complete: getCompletedSections()
            };

            return handleGetConfigurationState(intention, getCompressedState(), addStateToHistory, continueWithNextSectionFlag);
        }

        function removeElement(sectionId, elementId, addStateToHistory) {
            let intention = {
                remove: [{
                    sectionId: sectionId,
                    elementId: elementId
                }],
                complete: getCompletedSections()
            };

            return handleGetConfigurationState(intention, getCompressedState(), addStateToHistory);
        }

        function removeSection(sectionId) {
            let intention = {
                remove: [{
                    sectionId: sectionId
                }],
                complete: getCompletedSections()
            };

            return handleGetConfigurationState(intention, getCompressedState());
        }

        function batchUpdate(updates) {
            let intention = {
                set: [],
                remove: [],
                complete: updates.complete ? updates.complete : getCompletedSections()
            };

            if(typeof updates.singleValues !== "undefined") {
                for (let i = 0; i < updates.singleValues.length; i++) {
                    intention.set.push({
                        sectionId: updates.singleValues[i].sectionId,
                        elementId: updates.singleValues[i].elementId,
                        property: updates.singleValues[i].property,
                        value: updates.singleValues[i].value
                    });
                }
            }

            if(typeof updates.propertyValues !== "undefined") {
                for (let i = 0; i < updates.propertyValues.length; i++) {
                    for (let property in updates.propertyValues[i].properties) {
                        intention.set.push({
                            sectionId: updates.propertyValues[i].sectionId,
                            elementId: updates.propertyValues[i].elementId,
                            property: property,
                            value: updates.propertyValues[i].properties[property]
                        });
                    }
                }
            }

            if(typeof updates.removeElements !== "undefined") {
                for (let i = 0; i < updates.removeElements.length; i++) {
                    intention.remove.push({
                        sectionId: updates.removeElements[i].sectionId,
                        elementId: updates.removeElements[i].elementId
                    });
                }
            }

            if(typeof updates.removeSections !== "undefined") {
                for (let i = 0; i < updates.removeSections.length; i++) {
                    intention.remove.push({
                        sectionId: updates.removeSections[i]
                    });
                }
            }

            if (typeof updates.repair !== "undefined") {
                intention.repair = updates.repair;
            }

            return handleGetConfigurationState(intention, getCompressedState());
        }

        function setCurrentPerspective(perspective) {
            redux.setCurrentPerspective(perspective);
            fetchCurrentRenderImage();
        }

        function nextPerspective() {
            const currentIndex = redux.perspectives.indexOf(redux.currentPerspective);
            if ((currentIndex + 1) === redux.perspectives.length) {
                setCurrentPerspective(redux.perspectives[0]);
            } else {
                setCurrentPerspective(redux.perspectives[currentIndex + 1]);
            }
        }

        function previousPerspective() {
            const currentIndex = redux.perspectives.indexOf(redux.currentPerspective);
            if (currentIndex === 0) {
                setCurrentPerspective(redux.perspectives[redux.perspectives.length - 1]);
            } else {
                setCurrentPerspective(redux.perspectives[currentIndex - 1]);
            }
        }

        function fetchCurrentHumanReadableState() {
            return redux.fetchCurrentHumanReadableState(
                redux.productId, // @todo really needed?
                self.getCompressedState(redux.configurationState, redux.quantity)
            );
        }

        function fetchCurrentStatePrice() {
            let taxState = null;
            if (redux.shopSession.taxState) {
                taxState = redux.shopSession.taxState;
            }

            return redux.fetchCurrentStatePrice(
                redux.productId,
                self.getCompressedState(redux.configurationState, redux.quantity),
                redux.shopSession.shopCurrency,
                redux.shopSession.displayCurrency,
                redux.shopSession.customerGroup,
                redux.shopSession.sessionCookies,
                taxState
            );
        }

        function fetchCurrentStatePriceWithCustomState(state) {
            return redux.fetchCurrentStatePrice(
                redux.productId,
                state,
                redux.shopSession.shopCurrency,
                redux.shopSession.displayCurrency,
                redux.shopSession.customerGroup,
                redux.shopSession.sessionCookies
            );
        }

        function fetchCurrentRenderImage() {
            if (true === APTO_CONFIGURATION_SERVICE_DISABLE_RENDER_IMAGE_FETCH) {
                return;
            }

            $rootScope.$emit('APTO_FETCH_RENDER_IMAGE');
            return redux.fetchCurrentRenderImage(self.getCompressedState(redux.configurationState, redux.quantity), redux.currentPerspective, redux.productId);
        }

        function fetchComputedProductValues() {
            // @todo Add Switch to turn off query - maybe obsolete if server-sided-rules-sprint is done
            return redux.fetchComputedProductValues(redux.productId, self.getCompressedState(redux.configurationState, redux.quantity));
        }

        function getFormattedStatePrice(type) {
            type = type ? type : 'price';

            return redux.statePrice.sum[type].formatted;
        }

        function getFormattedProductPrice(type) {
            type = type ? type : 'price';

            if (!redux.statePrice) {
                return '';
            }

            const own = redux.statePrice.own;

            if (own.price.amount === 0 && own.pseudoPrice.amount === 0) {
                return '';
            }

            return own[type] ? own[type].formatted : '';
        }

        function getFormattedSectionPrice(sectionId, type) {
            type = type ? type : 'price';

            if (!redux.statePrice.sections[sectionId]) {
                return '';
            }

            const own = redux.statePrice.sections[sectionId].own;

            if (own.price.amount === 0 && own.pseudoPrice.amount === 0) {
                return '';
            }

            return own[type] ? own[type].formatted : '';
        }

        function getFormattedElementPrice(sectionId, elementId, type) {
            type = type ? type : 'price';

            if (!redux.statePrice.sections[sectionId] || !redux.statePrice.sections[sectionId].elements[elementId]) {
                return '';
            }

            const own = redux.statePrice.sections[sectionId].elements[elementId].own;

            if (own.price.amount === 0 && own.pseudoPrice.amount === 0) {
                return '';
            }

            return own[type] ? own[type].formatted : '';
        }

        function getProductDiscountName() {
            return JSON.parse(redux.statePrice.discount.name);
        }

        function getSectionDiscountName(sectionId) {
            if (!redux.statePrice.sections[sectionId]) {
                return null;
            }

            const discount = redux.statePrice.sections[sectionId].discount;

            return JSON.parse(discount.name);
        }

        function getElementDiscountName(sectionId, elementId) {
            if (!redux.statePrice.sections[sectionId] || !redux.statePrice.sections[sectionId].elements[elementId]) {
                return null;
            }

            const discount = redux.statePrice.sections[sectionId].elements[elementId].discount;

            return JSON.parse(discount.name);
        }

        function hasStatePseudoPrice() {
            const sum = redux.statePrice.sum;

            return sum.price.amount < sum.pseudoPrice.amount;
        }

        function hasProductPseudoPrice() {
            const own = redux.statePrice.own;

            return own.price.amount < own.pseudoPrice.amount;
        }

        function hasSectionPseudoPrice(sectionId) {
            if (!redux.statePrice.sections[sectionId]) {
                return false;
            }

            const own = redux.statePrice.sections[sectionId].own;

            return own.price.amount < own.pseudoPrice.amount;
        }

        function hasElementPseudoPrice(sectionId, elementId) {
            if (!redux.statePrice.sections[sectionId] || !redux.statePrice.sections[sectionId].elements[elementId]) {
                return false;
            }

            const own = redux.statePrice.sections[sectionId].elements[elementId].own;

            return own.price.amount < own.pseudoPrice.amount;
        }

        function getShowGross() {
            return redux.shopSession.customerGroup.showGross;
        }

        function getAdditionalElementInformation(sectionId, elementId) {
            if (
                !redux.statePrice.sections[sectionId] ||
                !redux.statePrice.sections[sectionId].elements[elementId] ||
                !redux.statePrice.sections[sectionId].elements[elementId].additionalInformation
            ) {
                return {};
            }

            return redux.statePrice.sections[sectionId].elements[elementId].additionalInformation;
        }

        function getSelectedElementBackground(sectionId) {
            if (typeof sectionId === "undefined") {
                return false;
            }

            for (let elementId in redux.configurationState[sectionId]['elements']) {
                if (
                    redux.configurationState[sectionId]['elements'].hasOwnProperty(elementId) &&
                    elementIsSelected(sectionId, elementId) === true
                ) {
                    if (redux.configurationState[sectionId]['elements'][elementId]['previewImage'] !== null) {
                        return redux.configurationState[sectionId]['elements'][elementId]['previewImage']['fileUrl'];
                    }
                }
            }
            return false;
        }

        function getElementPropertyValue(sectionId, elementId, property) {
            if (
                !sectionId ||
                !elementId ||
                !property ||
                !elementIsSelected(sectionId, elementId)
            ) {
                return null;
            }

            if (
                redux.initialized &&
                typeof redux.configurationState[sectionId].elements[elementId].state.values[property] !== "undefined"
            ) {
                return redux.configurationState[sectionId].elements[elementId].state.values[property];
            }

            return null;
        }

        function getElementSelectedValues(sectionId, elementId) {
            if (
                !sectionId ||
                !elementId ||
                !elementIsSelected(sectionId, elementId)
            ) {
                return null;
            }

            if (redux.initialized) {
                return angular.copy(redux.configurationState[sectionId].elements[elementId].state.values);
            }

            return null;
        }

        function fetchElementComputableValues(sectionId, elementId) {
            if (
                !sectionId ||
                !elementId ||
                !elementIsSelected(sectionId, elementId)
            ) {
                return null;
            }

            return redux.fetchElementComputableValues(
                self.getCompressedState(redux.configurationState, redux.quantity),
                sectionId,
                elementId
            );
        }

        function elementIsSelected(sectionId, elementId) {
            if (
                redux.initialized &&
                self.stateElementIsSelected(redux.configurationState, sectionId, elementId)
            ) {
                return true;
            }
            return false;
        }

        function elementIsDisabled(sectionId, elementId) {
            if(typeof sectionId === "undefined" || typeof elementId === "undefined"){
                return false;
            }

            if (redux.initialized) {
                return redux.configurationState[sectionId]['elements'][elementId]['state']['disabled'];
            }
            return false;
        }

        function sectionIsSelected(sectionId) {
            if(typeof sectionId === "undefined"){
                return false;
            }

            if (
                redux.initialized &&
                self.stateSectionIsSelected(redux.configurationState, sectionId)
            ) {
                return true;
            }
            return false;
        }

        function sectionIsComplete(sectionId) {
            if(typeof sectionId === "undefined"){
                return false;
            }

            if (redux.initialized) {
                return redux.configurationState[sectionId]['state']['complete'];
            }
            return false;
        }

        function sectionIsDisabled(sectionId) {
            if(typeof sectionId === "undefined"){
                return false;
            }

            if (redux.initialized) {
                return redux.configurationState[sectionId]['state']['disabled'];
            }
            return false;
        }

        function configurationIsValid() {
            if (!redux.initialized) {
                return false;
            }

            if (redux.failedRules.length > 0) {
                let onlySoftRules = true;
                for(let i; i < redux.failedRules.length; i++) {
                    if(!redux.failedRules[i].softRule) {
                        onlySoftRules = false;
                    }
                }
                if(!onlySoftRules) {
                    return false;
                }
            }

            for (let sectionId in redux.configurationState) {
                if(!redux.configurationState.hasOwnProperty(sectionId)) {
                    continue;
                }

                if (!sectionIsValid(sectionId)) {
                    return false;
                }
            }

            return true;
        }

        function sectionIsValid(sectionId) {
            // check configuration is initialized
            if (
                !redux.initialized ||
                !redux.configurationState[sectionId]
            ) {
                return false;
            }

            // check disabled section
            if (sectionIsDisabled(sectionId)) {
                return true;
            }

            // set section
            const section = redux.configurationState[sectionId];

            // check allow multiple section
            if (section.allowMultiple) {
                for (let elementId in section.elements) {
                    if (!section.elements.hasOwnProperty(elementId)) {
                        continue;
                    }

                    const element = section.elements[elementId];
                    if (
                        element.state.disabled === false &&
                        element.state.active === false &&
                        element.isMandatory === true
                    ) {
                        return false;
                    }
                }
            }

            // check section mandatory field
            if (section.isMandatory && !sectionIsSelected(sectionId)) {
                return false;
            }

            // section is valid
            return true;
        }

        function sectionIsHidden(sectionId) {
            if (
                typeof sectionId === "undefined" ||
                !redux.initialized ||
                !redux.configurationState[sectionId]
            ) {
                return false;
            }

            return redux.configurationState[sectionId]['isHidden'];
        }

        function redirectByState(routeState) {
            // add base url
            let redirectUrl = '';

            // set redirectUrl
            if (routeState.configurationId) {
                redirectUrl = '/configuration/' + routeState.configurationType + '/' + routeState.configurationId;
            } else {
                redirectUrl = '/product/' + (routeState.productSeoUrl ? routeState.productSeoUrl : routeState.productId);
            }

            $location.url(redirectUrl);
        }

        function addToBasket(quantity, perspectives, additionalData) {
            let promise;

            if (!additionalData) {
                additionalData = {};
            }

            // add inline additional data
            if (APTO_INLINE && APTO_INLINE.additionalData && (APTO_INLINE.additionalData.swProductId || APTO_INLINE.additionalData.shopProductId)) {
                if (APTO_INLINE.additionalData.swProductId) {
                    additionalData.swProductId = APTO_INLINE.additionalData.swProductId;
                }
                if (APTO_INLINE.additionalData.shopProductId) {
                    additionalData.shopProductId = APTO_INLINE.additionalData.shopProductId;
                }
            }

            if (APTO_INLINE && APTO_INLINE.additionalData && (APTO_INLINE.additionalData.swProductUrl || APTO_INLINE.additionalData.shopProductUrl)) {
                if (APTO_INLINE.additionalData.swProductUrl) {
                    additionalData.swProductUrl = APTO_INLINE.additionalData.swProductUrl;
                }
                if (APTO_INLINE.additionalData.shopProductUrl) {
                    additionalData.shopProductUrl = APTO_INLINE.additionalData.shopProductUrl;
                }
            }

            // add element attachments to additionalData
            additionalData.attachments = {};
            for (let h = 0; h < redux.productDetail.sections.length; h++) {
                let sectionId = redux.productDetail.sections[h].id;
                if (redux.configurationState.hasOwnProperty(sectionId) && !sectionIsSelected(sectionId)) {
                    continue;
                }
                for (let u = 0; u < redux.productDetail.sections[h].elements.length; u++) {
                    let elementId = redux.productDetail.sections[h].elements[u].id;
                    if (redux.configurationState[sectionId]['elements'].hasOwnProperty(elementId) && !elementIsSelected(sectionId, elementId)) {
                        continue;
                    }
                    for (let i = 0; i < redux.productDetail.sections[h].elements[u].attachments.length; i++) {
                        let fileUrl = APTO_API.media + redux.productDetail.sections[h].elements[u].attachments[i].fileUrl;
                        let name = redux.productDetail.sections[h].elements[u].attachments[i].name;
                        if (!additionalData.attachments.hasOwnProperty(elementId)) {
                            additionalData.attachments[elementId] = [];
                        }
                        additionalData.attachments[elementId].push({
                            fileUrl : fileUrl,
                            name: name
                        })
                    }
                }
            }

            // send add/update basket command
            if (null === redux.configurationId || 'basket' !== redux.configurationType) {
                promise = redux.addBasketConfiguration(
                    redux.productId,
                    self.getCompressedState(redux.configurationState, redux.quantity),
                    redux.shopSession.sessionCookies,
                    quantity,
                    perspectives,
                    additionalData
                );
            } else {
                promise = redux.updateBasketConfiguration(
                    redux.productId,
                    redux.configurationId,
                    self.getCompressedState(redux.configurationState, redux.quantity),
                    redux.shopSession.sessionCookies,
                    quantity,
                    perspectives,
                    additionalData
                );
            }

            return promise.then(() => {
                redux.shopSessionFetch(redux.activeLanguage.isocode);
                $document[0].dispatchEvent(cartEvent);
                //TODO: Erst aufrufen, wenn der Artikel wirklich im Warenkorb ist
                if (APTO_INLINE) {
                    openShopware6Cart();
                } else {
                    redux.openSidebarRight();
                }
            });
        }

        function openShopware6Cart() {
            if (!$window.PluginManager || !$window.PluginManager.getPluginInstances) {
                $window.location.replace(this.cartUrl);
            }

            const offCanvasCartInstances = $window.PluginManager.getPluginInstances('OffCanvasCart');
            for (let i = 0; i < offCanvasCartInstances.length; i++) {
                offCanvasCartInstances[i].openOffCanvas($window.router['frontend.cart.offcanvas'], false);
            }
        }

        function addProposedConfiguration() {
            redux.addProposedConfiguration(
                redux.productId,
                self.getCompressedState(redux.configurationState, redux.quantity)
            ).then(() => {
                redux.fetchProposedConfigurations(redux.productId);
            });
        }

        function addGuestConfiguration(email, name, sendMail, id, payload) {
            if (!sendMail && sendMail !== false) {
                sendMail = true;
            }

            if (!id) {
                id = '';
            }

            if (!payload) {
                payload = {};
            }

            // add inline additional data
            if (APTO_INLINE && APTO_INLINE.additionalData && (APTO_INLINE.additionalData.swProductId || APTO_INLINE.additionalData.shopProductId)) {
                if (APTO_INLINE.additionalData.swProductId) {
                    payload.swProductId = APTO_INLINE.additionalData.swProductId;
                }
                if (APTO_INLINE.additionalData.shopProductId) {
                    payload.shopProductId = APTO_INLINE.additionalData.shopProductId;
                }
            }

            if (APTO_INLINE && APTO_INLINE.additionalData && (APTO_INLINE.additionalData.swProductUrl || APTO_INLINE.additionalData.shopProductUrl)) {
                if (APTO_INLINE.additionalData.swProductUrl) {
                    payload.swProductUrl = APTO_INLINE.additionalData.swProductUrl;
                }
                if (APTO_INLINE.additionalData.shopProductUrl) {
                    payload.shopProductUrl = APTO_INLINE.additionalData.shopProductUrl;
                }
            }

            return redux.addGuestConfiguration(
                redux.productId,
                self.getCompressedState(redux.configurationState, redux.quantity),
                email,
                name,
                sendMail,
                id,
                payload
            );
        }

        function addOfferConfiguration(email, name, payload) {
            if (!payload) {
                payload = {};
            }

            // add inline additional data
            if (APTO_INLINE && APTO_INLINE.additionalData && (APTO_INLINE.additionalData.swProductId || APTO_INLINE.additionalData.shopProductId)) {
                if (APTO_INLINE.additionalData.swProductId) {
                    payload.swProductId = APTO_INLINE.additionalData.swProductId;
                }
                if (APTO_INLINE.additionalData.shopProductId) {
                    payload.shopProductId = APTO_INLINE.additionalData.shopProductId;
                }
            }

            if (APTO_INLINE && APTO_INLINE.additionalData && (APTO_INLINE.additionalData.swProductUrl || APTO_INLINE.additionalData.shopProductUrl)) {
                if (APTO_INLINE.additionalData.swProductUrl) {
                    payload.swProductUrl = APTO_INLINE.additionalData.swProductUrl;
                }
                if (APTO_INLINE.additionalData.shopProductUrl) {
                    payload.shopProductUrl = APTO_INLINE.additionalData.shopProductUrl;
                }
            }

            return redux.addOfferConfiguration(
                redux.productId,
                self.getCompressedState(redux.configurationState, redux.quantity),
                email,
                name,
                payload
            );
        }

        function addCodeConfiguration(id) {
            return redux.addCodeConfiguration(
                redux.productId,
                self.getCompressedState(redux.configurationState, redux.quantity),
                id
            );
        }

        function handleException(e, title = null) {
            // set dialog width
            let dialogWidth = '500px';
            if ($window.matchMedia("(max-width: 519px)").matches) {
                dialogWidth = ($window.innerWidth - 20) + 'px';
            }

            if (title === null) {
                title = 'Option konnte nicht hinzugefügt werden:';
            }

            ngDialog.open({
                data: {
                    title: title,
                    messages: e.messages,
                    translate: LanguageFactory.translate,
                    header: SnippetFactory.get('aptoConfigurationErrors.dialogHeader')
                },
                template:
                '<div>' +
                    '<h3 ng-if="ngDialogData.header">{{ ngDialogData.header }}</h3>' +
                    '<h3 ng-if="!ngDialogData.header">{{ ngDialogData.title }}</h3>' +
                    '<div ng-repeat="message in ngDialogData.messages">' +
                        '<div ng-bind-html="ngDialogData.translate(message) | nl2br"></div>' +
                    '</div>' +
                '</div>',
                plain: true,
                className: 'ngdialog-theme-default',
                width: dialogWidth
            });
        }

        function handleSoftRuleException(e) {
            return ngDialog.open({
                data: {
                    messages: e.messages
                },
                template: AptoSoftRuleDialogTemplate,
                plain: true,
                controller: AptoSoftRuleDialogController,
                className: 'ngdialog-theme-default soft-rule-dialog',
                width: '360px',
                showClose: false,
                closeByEscape: false,
                closeByNavigation: false,
                closeByDocument: false
            }).closePromise;
        }

        function getBasketArticleByConfigId(configId) {
            let articles = redux.shopSession.basket.articles;
            for (let i in articles) {
                if (articles.hasOwnProperty(i)) {
                    let article = articles[i];
                    if (article.configId === configId) {
                        return article;
                    }
                }
            }
            return null;
        }

        function clearNextSections(sectionId) {
            let sectionsToRemove = getAllNextSections(sectionId);
            sectionsToRemove.push(sectionId);

            return batchUpdate({
                removeSections: sectionsToRemove,
                complete: getCompletedSections(sectionsToRemove)
            });
        }

        function getFirstNotCompleteSection() {
            for (let i = 0; i < redux.sortOrderSections.length; i++) {
                const sectionId = redux.sortOrderSections[i];

                if (
                    !redux.configurationState.hasOwnProperty(sectionId) ||
                    sectionIsDisabled(sectionId) ||
                    redux.configurationState[sectionId].isHidden
                ) {
                    continue;
                }

                if (!sectionIsComplete(sectionId)) {
                    return sectionId;
                }
            }

            return null;
        }

        // @todo check if this function is used in any project, otherwise remove it
        function getNextActiveOrCompleteSections(selectedSectionId) {
            let activeSections = [];
            let isNextSection = false;

            for (let i = 0; i < redux.sortOrderSections.length; i++) {
                const sectionId = redux.sortOrderSections[i];

                if (
                    !redux.configurationState.hasOwnProperty(sectionId) ||
                    sectionIsDisabled(sectionId) ||
                    redux.configurationState[sectionId].isHidden
                ) {
                    continue;
                }

                if (sectionId === selectedSectionId) {
                    isNextSection = true;
                    continue;
                }

                if (false === isNextSection) {
                    continue;
                }

                if (sectionIsSelected(sectionId) || sectionIsComplete(sectionId)) {
                    activeSections.push(sectionId);
                } else {
                    return activeSections;
                }
            }

            return activeSections;
        }

        function getAllNextSections(selectedSectionId) {
            let nextSections = [];
            let isNextSection = false;

            for (let i = 0; i < redux.sortOrderSections.length; i++) {
                const sectionId = redux.sortOrderSections[i];

                if (
                    !redux.configurationState.hasOwnProperty(sectionId) ||
                    redux.configurationState[sectionId].isHidden
                ) {
                    continue;
                }

                if (sectionId === selectedSectionId) {
                    isNextSection = true;
                    continue;
                }

                if (false === isNextSection) {
                    continue;
                }

                nextSections.push(sectionId);
            }

            return nextSections;
        }

        function getActiveSectionNames() {
            let sectionNames = [];

            for (let sectionId in redux.configurationState) {
                if (redux.configurationState.hasOwnProperty(sectionId)) {
                    if (sectionIsSelected(sectionId)) {
                        sectionNames.push({
                            name: redux.configurationState[sectionId].name,
                        });
                    }
                }
            }

            return sectionNames;
        }

        function getActiveSectionElements(sectionId) {
            const elements = redux.configurationState[sectionId]['elements'];
            let activeElementIds = [];

            for (let elementId in elements) {
                if (elements.hasOwnProperty(elementId)) {
                    if (elementIsSelected(sectionId, elementId)) {
                        activeElementIds.push(elementId);
                    }
                }
            }

            return activeElementIds;
        }

        function getElementById(sectionId, elementId) {
            return {
                element: redux.configurationState[sectionId]['elements'][elementId],
                humanReadableState: redux.humanReadableState[elementId]
            };
        }

        function getCompressedState() {
            return self.getCompressedState(redux.configurationState, redux.quantity);
        }

        function getStateSummary() {
            // @todo maybe we can add the stateSummary to our configuration reducer and let it update with every state changes
            let stateSummary = {
                productId: redux.productId,
                sections: []
            };

            for (let sectionId in redux.configurationState) {
                if (redux.configurationState.hasOwnProperty(sectionId)) {
                    if (sectionIsDisabled(sectionId) || redux.configurationState[sectionId].isHidden) {
                        continue;
                    }

                    if (sectionIsSelected(sectionId) || sectionIsComplete(sectionId)) {
                        const elements = redux.configurationState[sectionId]['elements'];
                        let elementsSummary = [];

                        for (let elementId in elements) {
                            if (elements.hasOwnProperty(elementId)) {
                                if (elementIsSelected(sectionId, elementId)) {
                                    elementsSummary.push({
                                        id: elementId,
                                        name: elements[elementId].name,
                                        previewImage: elements[elementId].previewImage
                                    });
                                }
                            }
                        }

                        stateSummary.sections.push({
                            id: sectionId,
                            name: angular.copy(redux.configurationState[sectionId].name),
                            elements: elementsSummary
                        });
                    }
                }
            }

            return stateSummary;
        }

        function continueWithNextSection() {
            if (!redux.useStepByStep) {
                return;
            }

            const firstNotCompleteSection = getFirstNotCompleteSection();
            if (null === firstNotCompleteSection) {
                $rootScope.$emit('CONTINUE_WITH_NEXT_SECTION');
                redux.setProductView('summary');
            } else {
                if (!redux.selectedSection || redux.selectedSection.id !== firstNotCompleteSection) {
                    $rootScope.$emit('CONTINUE_WITH_NEXT_SECTION');
                    redux.setSelectedSection(firstNotCompleteSection);
                }
            }
        }

        function handleOnConfigurationChanged() {
            $rootScope.$emit('APTO_CONFIGURATION_SERVICE_ON_CONFIGURATION_CHANGED');
        }

        return {
            init: init,
            initShopSession: initShopSession,
            isInitialized: isInitialized,
            isShopSessionInitialized: isShopSessionInitialized,
            setSingleValue: setSingleValue,
            setElementProperties: setElementProperties,
            removeElement: removeElement,
            removeSection: removeSection,
            batchUpdate: batchUpdate,
            setCurrentPerspective: setCurrentPerspective,
            previousStateAvailable: previousStateAvailable,
            nextStateAvailable: nextStateAvailable,
            previousState: previousState,
            nextState: nextState,
            getSelectedElementBackground: getSelectedElementBackground,
            getElementPropertyValue: getElementPropertyValue,
            getElementSelectedValues: getElementSelectedValues,
            fetchElementComputableValues: fetchElementComputableValues,
            elementIsSelected: elementIsSelected,
            elementIsDisabled: elementIsDisabled,
            sectionIsSelected: sectionIsSelected,
            sectionIsComplete: sectionIsComplete,
            sectionIsDisabled: sectionIsDisabled,
            sectionIsValid: sectionIsValid,
            sectionIsHidden: sectionIsHidden,
            configurationIsValid: configurationIsValid,
            redirectByState: redirectByState,
            getFormattedStatePrice: getFormattedStatePrice,
            getFormattedProductPrice: getFormattedProductPrice,
            getFormattedSectionPrice: getFormattedSectionPrice,
            getFormattedElementPrice: getFormattedElementPrice,
            getProductDiscountName: getProductDiscountName,
            getSectionDiscountName: getSectionDiscountName,
            getElementDiscountName: getElementDiscountName,
            getShowGross: getShowGross,
            getAdditionalElementInformation: getAdditionalElementInformation,
            nextPerspective: nextPerspective,
            previousPerspective: previousPerspective,
            addToBasket: addToBasket,
            addProposedConfiguration: addProposedConfiguration,
            addGuestConfiguration: addGuestConfiguration,
            addOfferConfiguration: addOfferConfiguration,
            addCodeConfiguration: addCodeConfiguration,
            fetchComputedProductValues: fetchComputedProductValues,
            fetchCurrentStatePrice: fetchCurrentStatePrice,
            fetchCurrentStatePriceWithCustomState: fetchCurrentStatePriceWithCustomState,
            clearNextSections: clearNextSections,
            getFirstNotCompleteSection: getFirstNotCompleteSection,
            getNextActiveOrCompleteSections: getNextActiveOrCompleteSections,
            getActiveSectionNames: getActiveSectionNames,
            getActiveSectionElements: getActiveSectionElements,
            getElementById: getElementById,
            getStateSummary: getStateSummary,
            getCompressedState: getCompressedState,
            continueWithNextSection: continueWithNextSection,
            hasStatePseudoPrice: hasStatePseudoPrice,
            hasProductPseudoPrice: hasProductPseudoPrice,
            hasSectionPseudoPrice: hasSectionPseudoPrice,
            hasElementPseudoPrice: hasElementPseudoPrice,
            handleException: handleException,
            setSectionComplete: setSectionComplete
        }
    };
    self.$get.$inject = self.$getInject;
};

ConfigurationProvider.$inject = ConfigurationProviderInject;

export default ['ConfigurationService', ConfigurationProvider];
