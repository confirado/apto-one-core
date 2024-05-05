const ProductActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const ProductActions = function($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_PRODUCT_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function productsFetch(pageNumber, recordsPerPage, searchString) {
        return (dispatch) => {
            if (typeof searchString === "undefined") {
                searchString = '';
            }

            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            dispatch(PageHeaderActions.setPageNumber(TYPE_NS)(pageNumber));
            dispatch(PageHeaderActions.setRecordsPerPage(TYPE_NS)(recordsPerPage));
            dispatch({ type: getType('PRODUCTS_FETCH') });

            return MessageBusFactory.query('FindProducts', [pageNumber, recordsPerPage, searchString]).then(
                (response) => {
                    if (response.data.result.numberOfPages > 0 && response.data.result.data.length < 1) {
                        dispatch(productsFetch(response.data.result.numberOfPages, recordsPerPage, searchString));
                    }
                    else {
                        dispatch(productsReceived(response.data.result.data));
                        dispatch(PageHeaderActions.setNumberOfPages(TYPE_NS)(response.data.result.numberOfPages));
                        dispatch(PageHeaderActions.setNumberOfRecords(TYPE_NS)(response.data.result.numberOfRecords));
                    }
                },
                (error) => {
                    dispatch(productsFetchError(error));
                    throw error;
                }
            )
        }
    }

    function productsFetchByFilter(pageNumber, recordsPerPage, filter) {
        return (dispatch) => {
            if (typeof filter === "undefined") {
                filter = {
                    searchString: ''
                };
            }

            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(filter.searchString));
            dispatch(PageHeaderActions.setPageNumber(TYPE_NS)(pageNumber));
            dispatch(PageHeaderActions.setRecordsPerPage(TYPE_NS)(recordsPerPage));
            dispatch({ type: getType('PRODUCTS_FETCH') });

            return MessageBusFactory.query('FindProductsByFilterPagination', [pageNumber, recordsPerPage, filter]).then(
                (response) => {
                    if (response.data.result.numberOfPages > 0 && response.data.result.data.length < 1) {
                        dispatch(productsFetchByFilter(response.data.result.numberOfPages, recordsPerPage, filter));
                    }
                    else {
                        dispatch(productsReceived(response.data.result.data));
                        dispatch(PageHeaderActions.setNumberOfPages(TYPE_NS)(response.data.result.numberOfPages));
                        dispatch(PageHeaderActions.setNumberOfRecords(TYPE_NS)(response.data.result.numberOfRecords));
                    }
                },
                (error) => {
                    dispatch(productsFetchError(error));
                    throw error;
                }
            )
        }
    }

    function productsReceived(payload) {
        return {
            type: getType('PRODUCTS_RECEIVED'),
            payload: payload
        }
    }

    function productsFetchError(payload) {
        return {
            type: getType('PRODUCTS_FETCH_ERROR'),
            payload: payload
        }
    }

    function productDetailFetch(id) {
        return {
            type: getType('PRODUCT_DETAIL_FETCH'),
            payload: MessageBusFactory.query('FindProduct', [id])
        }
    }

    function availableCategoriesFetch() {
        return {
            type: getType('AVAILABLE_CATEGORIES_FETCH'),
            payload: MessageBusFactory.query('FindCategoryTree', [''])
        }
    }

    function fetchCategories() {
        return {
            type: getType('FETCH_CATEGORIES'),
            payload: MessageBusFactory.query('FindCategories', [''])
        }
    }

    function availableShopsFetch() {
        return {
            type: getType('AVAILABLE_SHOPS_FETCH'),
            payload: MessageBusFactory.query('FindShops', [''])
        }
    }

    function availableCustomerGroupsFetch() {
        return {
            type: getType('AVAILABLE_CUSTOMER_GROUPS_FETCH'),
            payload: MessageBusFactory.query('FindCustomerGroups', [''])
        }
    }

    function availablePriceCalculatorsFetch() {
        return {
            type: getType('AVAILABLE_PRICE_CALCULATORS_FETCH'),
            payload: MessageBusFactory.query('FindPriceCalculators')
        }
    }

    function productDetailAssignShops(shops) {
        return {
            type: getType('PRODUCT_DETAIL_ASSIGN_SHOPS'),
            payload: shops
        }
    }

    function productDetailAssignCategories(categories) {
        return {
            type: getType('PRODUCT_DETAIL_ASSIGN_CATEGORIES'),
            payload: categories
        }
    }

    function addProductCustomProperty(productId, key, value, translatable) {
        return {
            type: getType('ADD_PRODUCT_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('AddProductCustomProperty', [productId, key, value, translatable])
        }
    }

    function removeProductCustomProperty(productId, key) {
        return {
            type: getType('REMOVE_PRODUCT_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('RemoveProductCustomProperty', [productId, key])
        }
    }

    function productDetailSave(productDetail) {
        return dispatch => {
            let commandArguments = [];

            if(typeof productDetail.shops === "undefined") {
                productDetail.shops = [];
            }

            if(typeof productDetail.categories === "undefined") {
                productDetail.categories = [];
            }

            if(typeof productDetail.active === "undefined") {
                productDetail.active = false;
            }

            if(typeof productDetail.hidden === "undefined") {
                productDetail.hidden = false;
            }

            if(typeof productDetail.useStepByStep === "undefined") {
                productDetail.useStepByStep = false;
            }

            if(typeof productDetail.stock === "undefined") {
                productDetail.stock = 0;
            }

            if(typeof productDetail.minPurchase === "undefined") {
                productDetail.minPurchase = 0;
            }

            if(typeof productDetail.maxPurchase === "undefined") {
                productDetail.maxPurchase = 0;
            }

            if(typeof productDetail.deliveryTime === "undefined") {
                productDetail.deliveryTime = '';
            }

            if(typeof productDetail.weight === "undefined") {
                productDetail.weight = 0;
            }

            if(typeof productDetail.taxRate === "undefined") {
                productDetail.taxRate = 0;
            }

            if(!productDetail.articleNumber) {
                productDetail.articleNumber = '';
            }

            if(!productDetail.seoUrl) {
                productDetail.seoUrl = '';
            }

            if(!productDetail.priceCalculatorId) {
                productDetail.priceCalculatorId = '';
            }

            if(typeof productDetail.previewImage === "undefined") {
                productDetail.previewImage = null;
            }

            if(typeof productDetail.position === "undefined") {
                productDetail.position = 0;
            }

            if(typeof productDetail.keepSectionOrder === "undefined") {
                productDetail.keepSectionOrder = true;
            }

            if (typeof productDetail.filterProperties === "undefined") {
                productDetail.filterProperties = [];
            }
            if (typeof productDetail.domainProperties === "undefined") {
                productDetail.domainProperties = [];
            }

            commandArguments.push(productDetail.identifier);
            commandArguments.push(productDetail.name);
            commandArguments.push(productDetail.description);
            commandArguments.push(productDetail.shops);
            commandArguments.push(productDetail.categories);
            commandArguments.push(productDetail.active);
            commandArguments.push(productDetail.hidden);
            commandArguments.push(productDetail.useStepByStep);
            commandArguments.push(productDetail.articleNumber);
            commandArguments.push(productDetail.metaTitle);
            commandArguments.push(productDetail.metaDescription);
            commandArguments.push(productDetail.stock);
            commandArguments.push(productDetail.deliveryTime);
            commandArguments.push(productDetail.weight);
            commandArguments.push(productDetail.taxRate);
            commandArguments.push(productDetail.seoUrl);
            commandArguments.push(productDetail.priceCalculatorId);
            commandArguments.push(productDetail.minPurchase);
            commandArguments.push(productDetail.maxPurchase);
            commandArguments.push(productDetail.previewImage);
            commandArguments.push(productDetail.position);
            commandArguments.push(productDetail.keepSectionOrder);

            if(typeof productDetail.id !== "undefined") {
                commandArguments.unshift(productDetail.id);
                commandArguments.push(productDetail.filterProperties);
                commandArguments.push(productDetail.domainProperties);
                return dispatch({
                    type: getType('PRODUCT_DETAIL_UPDATE'),
                    payload: MessageBusFactory.command('UpdateProduct', commandArguments)
                });
            }

            return dispatch({
                type: getType('PRODUCT_DETAIL_ADD'),
                payload: MessageBusFactory.command('AddProduct', commandArguments)
            });
        }
    }

    function productRemove(id) {
        return {
            type: getType('PRODUCT_REMOVE'),
            payload: MessageBusFactory.command('RemoveProduct', [id])
        }
    }

    function productCopy(id) {
        return {
            type: getType('PRODUCT_COPY'),
            payload: MessageBusFactory.command('CopyProduct', [id])
        }
    }

    function productDetailReset() {
        return {
            type: getType('PRODUCT_DETAIL_RESET')
        }
    }

    function fetchSections(productId) {
        return {
            type: getType('FETCH_SECTIONS'),
            payload: MessageBusFactory.query('FindProductSections', [productId])
        }
    }

    function fetchSectionsElements(productId) {
        return {
            type: getType('FETCH_SECTIONS_ELEMENTS'),
            payload: MessageBusFactory.query('FindProductSectionsElements', [productId])
        }
    }

    function addProductSection(productId, section) {
        return {
            type: getType('ADD_PRODUCT_SECTION'),
            payload: MessageBusFactory.command('AddProductSection', [productId, null, section.value, false, section.addDefaultElement])
        }
    }

    function copyProductSection(productId, sectionId) {
        return {
            type: getType('COPY_PRODUCT_SECTION'),
            payload: MessageBusFactory.command('CopyProductSection', [productId, sectionId])
        }
    }

    function addProductSectionPrice(productId, sectionId, amount, currencyCode, customerGroupId, productConditionId) {
        return {
            type: getType('ADD_PRODUCT_SECTION_PRICE'),
            payload: MessageBusFactory.command('AddProductSectionPrice', [productId, sectionId, amount, currencyCode, customerGroupId, productConditionId])
        }
    }

    function updateProductSection(productId, sectionId, section, groupId) {
        let isZoomable = section.isZoomable;
        if (typeof isZoomable === "undefined") {
            isZoomable = null;
        }
        return {
            type: getType('UPDATE_PRODUCT_SECTION'),
            payload: MessageBusFactory.command('UpdateProductSection', [
                productId,
                sectionId,
                section.identifier ? section.identifier : null,
                section.name,
                section.description,
                section.previewImage,
                section.allowMultiple,
                {
                    type: section.repeatableType,
                    calculatedValueName: section.repeatableCalculatedValueName,
                },
                groupId,
                section.isHidden,
                section.position,
                section.isZoomable
            ])
        }
    }

    function setProductSectionIsActive(productId, sectionId, isActive) {
        return {
            type: getType('SET_PRODUCT_SECTION_IS_ACTIVE'),
            payload: MessageBusFactory.command('SetProductSectionIsActive', [productId, sectionId, isActive])
        }
    }

    function setProductSectionAllowMulti(productId, sectionId, allowMulti) {
        return {
            type: getType('SET_PRODUCT_SECTION_ALLOW_MULTI'),
            payload: MessageBusFactory.command('SetProductSectionAllowMulti', [productId, sectionId, allowMulti])
        }
    }

    function setProductSectionIsMandatory(productId, sectionId, isMandatory) {
        return {
            type: getType('SET_PRODUCT_SECTION_IS_MANDATORY'),
            payload: MessageBusFactory.command('SetProductSectionIsMandatory', [productId, sectionId, isMandatory])
        }
    }

    function removeProductSection(productId, sectionId) {
        return {
            type: getType('REMOVE_PRODUCT_SECTION'),
            payload: MessageBusFactory.command('RemoveProductSection', [productId, sectionId])
        }
    }

    function removeProductSectionPrice(productId, sectionId, priceId) {
        return {
            type: getType('REMOVE_PRODUCT_SECTION_PRICE'),
            payload: MessageBusFactory.command('RemoveProductSectionPrice', [productId, sectionId, priceId])
        }
    }

    function addProductSectionDiscount(productId, sectionId, discount, customerGroupId, name) {
        return {
            type: getType('ADD_PRODUCT_SECTION_DISCOUNT'),
            payload: MessageBusFactory.command('AddProductSectionDiscount', [productId, sectionId, discount, customerGroupId, name])
        }
    }

    function removeProductSectionDiscount(productId, sectionId, discountId) {
        return {
            type: getType('REMOVE_PRODUCT_SECTION_DISCOUNT'),
            payload: MessageBusFactory.command('RemoveProductSectionDiscount', [productId, sectionId, discountId])
        }
    }

    function addProductSectionCustomProperty(productId, sectionId, key, value, translatable) {
        return {
            type: getType('ADD_PRODUCT_SECTION_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('AddProductSectionCustomProperty', [productId, sectionId, key, value, translatable])
        }
    }

    function removeProductSectionCustomProperty(productId, sectionId, key) {
        return {
            type: getType('REMOVE_PRODUCT_SECTION_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('RemoveProductSectionCustomProperty', [productId, sectionId, key])
        }
    }

    function addProductElement(productId, sectionId, elementName) {
        return {
            type: getType('ADD_PRODUCT_ELEMENT'),
            payload: MessageBusFactory.command('AddProductElement', [productId, sectionId, null, elementName])
        }
    }

    function copyProductElement(productId, sectionId, elementId) {
        return {
            type: getType('COPY_PRODUCT_ELEMENT'),
            payload: MessageBusFactory.command('CopyProductElement', [productId, sectionId, elementId])
        }
    }

    function addProductElementPrice(productId, sectionId, elementId, amount, currencyCode, customerGroupId, productConditionId) {
        return {
            type: getType('ADD_PRODUCT_ELEMENT_PRICE'),
            payload: MessageBusFactory.command('AddProductElementPrice', [productId, sectionId, elementId, amount, currencyCode, customerGroupId, productConditionId])
        }
    }

    function addProductElementPriceFormula(productId, sectionId, elementId, formula, currencyCode, customerGroupId, productConditionId) {
        return {
            type: getType('ADD_PRODUCT_ELEMENT_PRICE_FORMULA'),
            payload: MessageBusFactory.command('AddProductElementPriceFormula', [productId, sectionId, elementId, formula, currencyCode, customerGroupId, productConditionId])
        }
    }

    function addProductElementRenderImage(productId, sectionId, elementId, renderImageOptions, offsetOptions) {
        return {
            type: getType('ADD_PRODUCT_ELEMENT_RENDER_IMAGE'),
            payload: MessageBusFactory.command('AddProductElementRenderImage', [productId, sectionId, elementId, renderImageOptions, offsetOptions])
        }
    }

    function addProductElementAttachment(productId, sectionId, elementId, attachment) {
        return {
            type: getType('ADD_PRODUCT_ELEMENT_ATTACHMENT'),
            payload: MessageBusFactory.command('AddProductElementAttachment', [productId, sectionId, elementId, attachment])
        }
    }

    function addProductElementGallery(productId, sectionId, elementId, gallery) {
        return {
            type: getType('ADD_PRODUCT_ELEMENT_GALLERY'),
            payload: MessageBusFactory.command('AddProductElementGallery', [productId, sectionId, elementId, gallery])
        }
    }

    function addProductElementCustomProperty(productId, sectionId, elementId, key, value, translatable) {
        return {
            type: getType('ADD_PRODUCT_ELEMENT_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('AddProductElementCustomProperty', [productId, sectionId, elementId, key, value, translatable])
        }
    }

    function removeProductElementCustomProperty(productId, sectionId, elementId, key) {
        return {
            type: getType('REMOVE_PRODUCT_ELEMENT_CUSTOM_PROPERTY'),
            payload: MessageBusFactory.command('RemoveProductElementCustomProperty', [productId, sectionId, elementId, key])
        }
    }

    function updateProductElement(productId, sectionId, elementId, element, definition) {
        if (typeof definition === "undefined") {
            definition = [];
        }

        let isActive = element.isActive;
        let isMandatory = element.isMandatory;
        let isNotAvailable = element.isNotAvailable;
        let isZoomable = element.isZoomable;
        let zoomFunction = element.zoomFunction;
        let openLinksInDialog = element.openLinksInDialog;
        let isDefault = element.isDefault;
        let priceMatrixActive = element.priceMatrixActive;
        let priceMatrixId = element.priceMatrix ? element.priceMatrix.id : null;
        let priceMatrixRow = element.priceMatrixRow;
        let priceMatrixColumn = element.priceMatrixColumn;
        let extendedPriceCalculationActive = element.extendedPriceCalculationActive;
        let extendedPriceCalculationFormula = element.extendedPriceCalculationFormula;

        if (typeof isActive === "undefined") {
            isActive = null;
        }

        if (typeof isMandatory === "undefined") {
            isMandatory = null;
        }

        if (typeof isNotAvailable === "undefined") {
            isNotAvailable = null;
        }

        if (typeof isZoomable === "undefined") {
            isZoomable = null;
        }

        if (typeof zoomFunction === "undefined") {
            zoomFunction = null;
        }

        if (typeof openLinksInDialog === "undefined") {
            openLinksInDialog = false;
        }

        if (typeof isDefault === "undefined") {
            isDefault = null;
        }

        if (typeof priceMatrixActive === "undefined") {
            priceMatrixActive = false;
        }

        if (typeof priceMatrixRow === "undefined") {
            priceMatrixRow = null;
        }

        if (typeof priceMatrixColumn === "undefined") {
            priceMatrixColumn = null;
        }

        if (typeof extendedPriceCalculationActive === "undefined") {
            extendedPriceCalculationActive = false;
        }

        if (typeof extendedPriceCalculationFormula === "undefined") {
            extendedPriceCalculationFormula = '';
        }

        return {
            type: getType('UPDATE_PRODUCT_ELEMENT'),
            payload: MessageBusFactory.command('UpdateProductElement', [
                productId,
                sectionId,
                elementId,
                element.identifier ? element.identifier : null,
                element.name,
                element.description,
                element.errorMessage,
                definition,
                element.previewImage,
                element.position,
                element.percentageSurcharge,
                isActive,
                isMandatory,
                isZoomable,
                isDefault,
                priceMatrixActive,
                priceMatrixId,
                priceMatrixRow,
                priceMatrixColumn,
                extendedPriceCalculationActive,
                extendedPriceCalculationFormula,
                isNotAvailable,
                zoomFunction,
                openLinksInDialog
            ])
        }
    }

    function setProductElementIsDefault(productId, sectionId, elementId, isDefault) {
        return {
            type: getType('SET_PRODUCT_ELEMENT_IS_DEFAULT'),
            payload: MessageBusFactory.command('SetProductElementIsDefault', [productId, sectionId, elementId, isDefault])
        }
    }

    function setProductElementIsActive(productId, sectionId, elementId, isActive) {
        return {
            type: getType('SET_PRODUCT_ELEMENT_IS_ACTIVE'),
            payload: MessageBusFactory.command('SetProductElementIsActive', [productId, sectionId, elementId, isActive])
        }
    }

    function setProductElementIsMandatory(productId, sectionId, elementId, isMandatory) {
        return {
            type: getType('SET_PRODUCT_ELEMENT_IS_MANDATORY'),
            payload: MessageBusFactory.command('SetProductElementIsMandatory', [productId, sectionId, elementId, isMandatory])
        }
    }

    function removeProductElement(productId, sectionId, elementId) {
        return {
            type: getType('REMOVE_PRODUCT_ELEMENT'),
            payload: MessageBusFactory.command('RemoveProductElement', [productId, sectionId, elementId])
        }
    }

    function removeProductElementPrice(productId, sectionId, elementId, priceId) {
        return {
            type: getType('REMOVE_PRODUCT_ELEMENT_PRICE'),
            payload: MessageBusFactory.command('RemoveProductElementPrice', [productId, sectionId, elementId, priceId])
        }
    }

    function removeProductElementPriceFormula(productId, sectionId, elementId, priceFormulaId) {
        return {
            type: getType('REMOVE_PRODUCT_ELEMENT_PRICE_FORMULA'),
            payload: MessageBusFactory.command('RemoveProductElementPriceFormula', [productId, sectionId, elementId, priceFormulaId])
        }
    }

    function removeProductElementRenderImage(productId, sectionId, elementId, renderImageId) {
        return {
            type: getType('REMOVE_PRODUCT_ELEMENT_RENDER_IMAGE'),
            payload: MessageBusFactory.command('RemoveProductElementRenderImage', [productId, sectionId, elementId, renderImageId])
        }
    }

    function removeProductElementAttachment(productId, sectionId, elementId, attachmentId) {
        return {
            type: getType('REMOVE_PRODUCT_ELEMENT_ATTACHMENT'),
            payload: MessageBusFactory.command('RemoveProductElementAttachment', [productId, sectionId, elementId, attachmentId])
        }
    }

    function removeProductElementGallery(productId, sectionId, elementId, galleryId) {
        return {
            type: getType('REMOVE_PRODUCT_ELEMENT_GALLERY'),
            payload: MessageBusFactory.command('RemoveProductElementGallery', [productId, sectionId, elementId, galleryId])
        }
    }

    function addProductElementDiscount(productId, sectionId, elementId, discount, customerGroupId, name) {
        return {
            type: getType('ADD_PRODUCT_ELEMENT_DISCOUNT'),
            payload: MessageBusFactory.command('AddProductElementDiscount', [productId, sectionId, elementId, discount, customerGroupId, name])
        }
    }

    function removeProductElementDiscount(productId, sectionId, elementId, discountId) {
        return {
            type: getType('REMOVE_PRODUCT_ELEMENT_DISCOUNT'),
            payload: MessageBusFactory.command('RemoveProductElementDiscount', [productId, sectionId, elementId, discountId])
        }
    }

    function fetchRules(productId) {
        return {
            type: getType('FETCH_RULES'),
            payload: MessageBusFactory.query('FindProductRules', [productId])
        }
    }

    function fetchPrices(productId) {
        return {
            type: getType('FETCH_PRICES'),
            payload: MessageBusFactory.query('FindProductPrices', [productId])
        }
    }

    function fetchDiscounts(productId) {
        return {
            type: getType('FETCH_DISCOUNTS'),
            payload: MessageBusFactory.query('FindProductDiscounts', [productId])
        }
    }

    function fetchCustomProperties(productId) {
        return {
            type: getType('FETCH_CUSTOM_PROPERTIES'),
            payload: MessageBusFactory.query('FindProductCustomProperties', [productId])
        }
    }

    function addProductPrice(productId, amount, currencyCode, customerGroupId, productConditionId) {
        return {
            type: getType('ADD_PRODUCT_PRICE'),
            payload: MessageBusFactory.command('AddProductPrice', [productId, amount, currencyCode, customerGroupId, productConditionId])
        }
    }

    function removeProductPrice(productId, priceId) {
        return {
            type: getType('REMOVE_PRODUCT_PRICE'),
            payload: MessageBusFactory.command('RemoveProductPrice', [productId, priceId])
        }
    }

    function addProductDiscount(productId, discount, customerGroupId, name) {
        return {
            type: getType('ADD_PRODUCT_DISCOUNT'),
            payload: MessageBusFactory.command('AddProductDiscount', [productId, discount, customerGroupId, name])
        }
    }

    function removeProductDiscount(productId, discountId) {
        return {
            type: getType('REMOVE_PRODUCT_DISCOUNT'),
            payload: MessageBusFactory.command('RemoveProductDiscount', [productId, discountId])
        }
    }

    function addProductRule(productId, ruleName) {
        return {
            type: getType('ADD_PRODUCT_RULE'),
            payload: MessageBusFactory.command('AddProductRule', [productId, ruleName])
        }
    }

    function addProductRuleCondition(productId, condition) {
        return {
            type: getType('ADD_PRODUCT_RULE_CONDITION'),
            payload: MessageBusFactory.command('AddProductRuleCondition', [
                productId,
                condition.ruleId,
                condition.type,
                condition.sectionId,
                condition.elementId,
                condition.property,
                condition.computedValue,
                condition.operator,
                condition.value
            ])
        }
    }

    function addProductRuleImplication(productId, implication) {
        return {
            type: getType('ADD_PRODUCT_RULE_IMPLICATION'),
            payload: MessageBusFactory.command('AddProductRuleImplication', [
                productId,
                implication.ruleId,
                implication.type,
                implication.sectionId,
                implication.elementId,
                implication.property,
                implication.computedValue,
                implication.operator,
                implication.value
            ])
        }
    }

    function updateProductRule(productId, ruleId, ruleName, ruleActive, errorMessage, conditionsOperator, implicationsOperator, softRule, description, position) {
        let commandArguments = [];

        commandArguments.push(productId);
        commandArguments.push(ruleId);
        commandArguments.push(ruleName);
        commandArguments.push(ruleActive);
        if (errorMessage === null) {
            errorMessage = [];
        }
        commandArguments.push(errorMessage);
        commandArguments.push(conditionsOperator);
        commandArguments.push(implicationsOperator);
        commandArguments.push(softRule);
        commandArguments.push(description);
        commandArguments.push(position);

        return {
            type: getType('UPDATE_PRODUCT_RULE'),
            payload: MessageBusFactory.command('UpdateProductRule', commandArguments)
        }
    }

    function removeProductRule(productId, ruleId) {
        return {
            type: getType('REMOVE_PRODUCT_RULE'),
            payload: MessageBusFactory.command('RemoveProductRule', [productId, ruleId])
        }
    }

    function copyProductRule(productId, ruleId) {
        return {
            type: getType('COPY_PRODUCT_RULE'),
            payload: MessageBusFactory.command('CopyProductRule', [productId, ruleId])
        }
    }

    function updateProductRuleCondition(productId, ruleId, condition) {
        let commandArguments = [];

        commandArguments.push(productId);
        commandArguments.push(ruleId);
        commandArguments.push(condition.id);
        commandArguments.push(condition.typeId);
        commandArguments.push(condition.operatorId);
        commandArguments.push(condition.value);
        commandArguments.push(condition.computedProductValueId);
        commandArguments.push(condition.sectionId);
        commandArguments.push(condition.elementId);
        commandArguments.push(condition.property);

        return {
            type: getType('UPDATE_PRODUCT_RULE_CONDITION'),
            payload: MessageBusFactory.command('UpdateProductRuleCondition', commandArguments)
        }
    }

    function copyProductRuleCondition(productId, ruleId, conditionId) {
        return {
            type: getType('COPY_PRODUCT_RULE_CONDITION'),
            payload: MessageBusFactory.command('CopyProductRuleCondition', [productId, ruleId, conditionId])
        }
    }

    function removeProductRuleCondition(productId, ruleId, conditionId) {
        return {
            type: getType('REMOVE_PRODUCT_RULE_CONDITION'),
            payload: MessageBusFactory.command('RemoveProductRuleCondition', [productId, ruleId, conditionId])
        }
    }

    function updateProductRuleImplication(productId, ruleId, implication) {
        let commandArguments = [];

        commandArguments.push(productId);
        commandArguments.push(ruleId);
        commandArguments.push(implication.id);
        commandArguments.push(implication.typeId);
        commandArguments.push(implication.operatorId);
        commandArguments.push(implication.value);
        commandArguments.push(implication.computedProductValueId);
        commandArguments.push(implication.sectionId);
        commandArguments.push(implication.elementId);
        commandArguments.push(implication.property);

        return {
            type: getType('UPDATE_PRODUCT_RULE_IMPLICATION'),
            payload: MessageBusFactory.command('UpdateProductRuleImplication', commandArguments)
        }
    }

    function copyProductRuleImplication(productId, ruleId, implicationId) {
        return {
            type: getType('COPY_PRODUCT_RULE_IMPLICATION'),
            payload: MessageBusFactory.command('CopyProductRuleImplication', [productId, ruleId, implicationId])
        }
    }

    function removeProductRuleImplication(productId, ruleId, implicationId) {
        return {
            type: getType('REMOVE_PRODUCT_RULE_IMPLICATION'),
            payload: MessageBusFactory.command('RemoveProductRuleImplication', [productId, ruleId, implicationId])
        }
    }

    function addComputedProductValue(productId, name) {
        return {
            type: getType('ADD_COMPUTED_PRODUCT_VALUE'),
            payload: MessageBusFactory.command('AddProductComputedProductValue', [productId, name])
        }
    }

    function removeComputedProductValue(productId, id) {
        return {
            type: getType('REMOVE_COMPUTED_PRODUCT_VALUE'),
            payload: MessageBusFactory.command('RemoveProductComputedProductValue', [productId, id])
        }
    }

    function fetchComputedValueDetail(valueId) {
        return {
            type: getType('FETCH_COMPUTED_VALUE'),
            payload: valueId
        }
    }

    function addComputedProductValueAlias(productId, computedValueId, sectionId, elementId, name, property, isCP) {
        return {
            type: getType('ADD_COMPUTED_PRODUCT_VALUE_ALIAS'),
            payload: MessageBusFactory.command('AddProductComputedProductValueAlias', [productId, computedValueId, sectionId, elementId, name, property, isCP])
        }
    }

    function removeComputedProductValueAlias(productId, computedValueId, id) {
        return {
            type: getType('REMOVE_COMPUTED_PRODUCT_VALUE_ALIAS'),
            payload: MessageBusFactory.command('RemoveProductComputedProductValueAlias', [productId, computedValueId, id])
        }
    }

    function updateComputedProductValue(productId, computedValueId, name, formula) {
        return {
            type: getType('UPDATE_COMPUTED_PRODUCT_VALUE'),
            payload: MessageBusFactory.command('UpdateProductComputedProductValue', [productId, computedValueId, name, formula])
        }
    }

    function fetchComputedProductValues(productId) {
        return {
            type: getType('FETCH_COMPUTED_VALUES'),
            payload: MessageBusFactory.query('FindProductComputedValues', [productId])
        }
    }

    function setDetailValue(key, value) {
        return {
            type: getType('SET_DETAIL_VALUE'),
            payload: {
                key: key,
                value: value
            }
        }
    }

    function getNextPosition(type, productId, sectionId) {
        if (typeof productId === 'undefined') {
            productId = '';
        }

        if (typeof sectionId === 'undefined') {
            sectionId = '';
        }

        return {
            type: getType('GET_NEXT_POSITION'),
            payload: MessageBusFactory.query('FindNextAvailablePosition', [])
        }
    }
    function productDetailAssignProperties(filterProperties) {
        return {
            type: getType('PRODUCT_DETAIL_ASSIGN_PROPERTIES'),
            payload: filterProperties
        }
    }

    function fetchConditionSets(productId) {
        return {
            type: getType('FETCH_CONDITION_SETS'),
            payload: MessageBusFactory.query('FindConditionSets', [productId])
        }
    }

    function addProductConditionSet(productId, identifier) {
        return {
            type: getType('ADD_CONDITION_SET'),
            payload: MessageBusFactory.command('AddConditionSet', [productId, identifier])
        }
    }


    function updateProductConditionSet(productId, conditionSetId, identifier, conditionsOperator) {
        let commandArguments = [];

        commandArguments.push(productId);
        commandArguments.push(conditionSetId);
        commandArguments.push(identifier);
        commandArguments.push(conditionsOperator);

        return {
            type: getType('UPDATE_CONDITION_SET'),
            payload: MessageBusFactory.command('UpdateConditionSet', commandArguments)
        }
    }

    function removeProductConditionSet(productId, conditionSetId) {
        return {
            type: getType('REMOVE_CONDITION_SET'),
            payload: MessageBusFactory.command('RemoveConditionSet', [productId, conditionSetId])
        }
    }

    function addProductConditionSetCondition(productId, condition) {
        return {
            type: getType('ADD_CONDITION_SET_CONDITION'),
            payload: MessageBusFactory.command('AddConditionSetCondition', [
                productId,
                condition.conditionSetId,
                condition.type,
                condition.sectionId,
                condition.elementId,
                condition.property,
                condition.computedValue,
                condition.operator,
                condition.value
            ])
        }
    }

    function updateProductConditionSetCondition(productId, conditionSetId, condition) {
        let commandArguments = [];

        commandArguments.push(productId);
        commandArguments.push(conditionSetId);
        commandArguments.push(condition.id);
        commandArguments.push(condition.typeId);
        commandArguments.push(condition.operatorId);
        commandArguments.push(condition.value);
        commandArguments.push(condition.computedProductValueId);
        commandArguments.push(condition.sectionId);
        commandArguments.push(condition.elementId);
        commandArguments.push(condition.property);

        return {
            type: getType('UPDATE_CONDITION_SET_CONDITION'),
            payload: MessageBusFactory.command('UpdateConditionSetCondition', commandArguments)
        }
    }

    function copyProductConditionSetCondition(productId, conditionSetId, conditionId) {
        return {
            type: getType('COPY_CONDITION_SET_CONDITION'),
            payload: MessageBusFactory.command('CopyConditionSetCondition', [productId, conditionSetId, conditionId])
        }
    }

    function removeProductConditionSetCondition(productId, conditionSetId, conditionId) {
        return {
            type: getType('REMOVE_CONDITION_SET_CONDITION'),
            payload: MessageBusFactory.command('RemoveConditionSetCondition', [productId, conditionSetId, conditionId])
        }
    }

    function fetchConditions(productId) {
        return {
            type: getType('FETCH_CONDITIONS'),
            payload: MessageBusFactory.query('FindConditions', [productId])
        }
    }

    function addCondition(productId, condition) {
        return {
            type: getType('ADD_CONDITION'),
            payload: MessageBusFactory.command('AddCondition', [
                productId,
                condition.identifier,
                condition.type,
                condition.operator,
                condition.value,
                condition.sectionId,
                condition.elementId,
                condition.property,
                condition.computedValue,
            ])
        }
    }

    function updateCondition(productId, condition) {
        let commandArguments = [];

        commandArguments.push(productId);
        commandArguments.push(condition.id);
        commandArguments.push(condition.identifier);
        commandArguments.push(condition.typeId);
        commandArguments.push(condition.operatorId);
        commandArguments.push(condition.value);
        commandArguments.push(condition.sectionId);
        commandArguments.push(condition.elementId);
        commandArguments.push(condition.property);
        commandArguments.push(condition.computedProductValueId);

        return {
            type: getType('UPDATE_CONDITION'),
            payload: MessageBusFactory.command('UpdateCondition', commandArguments)
        }
    }

    function copyCondition(productId, conditionId) {
        return {
            type: getType('COPY_CONDITION'),
            payload: MessageBusFactory.command('CopyCondition', [productId, conditionId])
        }
    }

    function removeCondition(productId, conditionId) {
        return {
            type: getType('REMOVE_CONDITION'),
            payload: MessageBusFactory.command('RemoveCondition', [productId, conditionId])
        }
    }

    return {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        availableCategoriesFetch: availableCategoriesFetch,
        fetchCategories: fetchCategories,
        availableShopsFetch: availableShopsFetch,
        availableCustomerGroupsFetch: availableCustomerGroupsFetch,
        availablePriceCalculatorsFetch: availablePriceCalculatorsFetch,
        productDetailAssignShops: productDetailAssignShops,
        productDetailAssignCategories: productDetailAssignCategories,
        productDetailFetch: productDetailFetch,
        productDetailSave: productDetailSave,
        productDetailReset: productDetailReset,
        productRemove: productRemove,
        productCopy: productCopy,
        productsFetch: productsFetch,
        addProductCustomProperty: addProductCustomProperty,
        removeProductCustomProperty: removeProductCustomProperty,
        fetchSections: fetchSections,
        fetchSectionsElements: fetchSectionsElements,
        addProductSection: addProductSection,
        copyProductSection: copyProductSection,
        addProductSectionPrice: addProductSectionPrice,
        addProductSectionCustomProperty: addProductSectionCustomProperty,
        removeProductSectionCustomProperty: removeProductSectionCustomProperty,
        updateProductSection: updateProductSection,
        setProductSectionIsActive: setProductSectionIsActive,
        setProductSectionAllowMulti: setProductSectionAllowMulti,
        setProductSectionIsMandatory: setProductSectionIsMandatory,
        removeProductSection: removeProductSection,
        removeProductSectionPrice: removeProductSectionPrice,
        addProductSectionDiscount: addProductSectionDiscount,
        removeProductSectionDiscount: removeProductSectionDiscount,
        addProductElement: addProductElement,
        copyProductElement: copyProductElement,
        addProductElementPrice: addProductElementPrice,
        addProductElementPriceFormula: addProductElementPriceFormula,
        addProductElementRenderImage: addProductElementRenderImage,
        addProductElementAttachment: addProductElementAttachment,
        addProductElementGallery: addProductElementGallery,
        addProductElementCustomProperty: addProductElementCustomProperty,
        removeProductElementPrice: removeProductElementPrice,
        removeProductElementPriceFormula: removeProductElementPriceFormula,
        removeProductElementRenderImage: removeProductElementRenderImage,
        removeProductElementAttachment: removeProductElementAttachment,
        removeProductElementGallery: removeProductElementGallery,
        removeProductElementCustomProperty: removeProductElementCustomProperty,
        addProductElementDiscount: addProductElementDiscount,
        removeProductElementDiscount: removeProductElementDiscount,
        updateProductElement: updateProductElement,
        setProductElementIsDefault: setProductElementIsDefault,
        setProductElementIsActive: setProductElementIsActive,
        setProductElementIsMandatory: setProductElementIsMandatory,
        removeProductElement: removeProductElement,
        fetchRules: fetchRules,
        fetchPrices: fetchPrices,
        fetchDiscounts: fetchDiscounts,
        fetchCustomProperties: fetchCustomProperties,
        addProductPrice: addProductPrice,
        removeProductPrice: removeProductPrice,
        addProductDiscount: addProductDiscount,
        removeProductDiscount: removeProductDiscount,
        addProductRule: addProductRule,
        updateProductRule: updateProductRule,
        removeProductRule: removeProductRule,
        copyProductRule: copyProductRule,
        addProductRuleCondition: addProductRuleCondition,
        addProductRuleImplication: addProductRuleImplication,

        updateProductRuleCondition: updateProductRuleCondition,
        copyProductRuleCondition: copyProductRuleCondition,
        removeProductRuleCondition: removeProductRuleCondition,

        updateProductRuleImplication: updateProductRuleImplication,
        copyProductRuleImplication: copyProductRuleImplication,
        removeProductRuleImplication: removeProductRuleImplication,

        setDetailValue: setDetailValue,
        productsFetchByFilter: productsFetchByFilter,
        getNextPosition: getNextPosition,
        productDetailAssignProperties: productDetailAssignProperties,
        addComputedProductValue: addComputedProductValue,
        updateComputedProductValue: updateComputedProductValue,
        addComputedProductValueAlias: addComputedProductValueAlias,
        fetchComputedProductValues: fetchComputedProductValues,
        fetchComputedValueDetail: fetchComputedValueDetail,
        removeComputedProductValueAlias: removeComputedProductValueAlias,
        removeComputedProductValue: removeComputedProductValue,

        fetchConditionSets: fetchConditionSets,
        addProductConditionSet: addProductConditionSet,
        updateProductConditionSet: updateProductConditionSet,
        removeProductConditionSet: removeProductConditionSet,
        addProductConditionSetCondition: addProductConditionSetCondition,
        updateProductConditionSetCondition: updateProductConditionSetCondition,
        copyProductConditionSetCondition: copyProductConditionSetCondition,
        removeProductConditionSetCondition: removeProductConditionSetCondition,

        fetchConditions: fetchConditions,
        addCondition: addCondition,
        updateCondition: updateCondition,
        copyCondition: copyCondition,
        removeCondition: removeCondition,
    };
};

ProductActions.$inject = ProductActionsInject;

export default ['ProductActions', ProductActions];
