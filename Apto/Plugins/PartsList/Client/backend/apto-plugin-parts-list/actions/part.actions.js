const ActionsInject = ['$ngRedux', 'MessageBusFactory', 'PageHeaderActions', 'DataListActions'];
const Actions = function ($ngRedux, MessageBusFactory, PageHeaderActions, DataListActions) {
    const TYPE_NS = 'APTO_PLUGIN_PARTS_LIST_PART_';

    function getType(type) {
        return TYPE_NS + type;
    }

    function fetchList(pageNumber, recordsPerPage, searchString) {
        return dispatch => {
            if (typeof searchString === 'undefined') {
                searchString = '';
            }

            dispatch(PageHeaderActions.setSearchString(TYPE_NS)(searchString));
            dispatch(PageHeaderActions.setPageNumber(TYPE_NS)(pageNumber));
            dispatch(PageHeaderActions.setRecordsPerPage(TYPE_NS)(recordsPerPage));
            dispatch({type: getType('FETCH_LIST')});

            return MessageBusFactory.query('AptoPartsListFindParts', [pageNumber, recordsPerPage, searchString]).then(
                (response) => {
                    if (response.data.result.numberOfPages > 0 && response.data.result.data.length < 1) {
                        dispatch(fetchList(response.data.result.numberOfPages, recordsPerPage, searchString));
                    }
                    else {
                        dispatch(fetchListFulfilled(response));
                        dispatch(PageHeaderActions.setNumberOfPages(TYPE_NS)(response.data.result.numberOfPages));
                        dispatch(PageHeaderActions.setNumberOfRecords(TYPE_NS)(response.data.result.numberOfRecords));
                    }
                },
                (error) => {
                    dispatch(fetchListError(error));
                    throw error;
                }
            )
        }
    }

    function fetchListFulfilled(payload) {
        return {
            type: getType('FETCH_LIST_FULFILLED'),
            payload: payload
        }
    }

    function fetchListError(payload) {
        return {
            type: getType('FETCH_LIST_ERROR'),
            payload: payload
        }
    }

    function fetchDetails(id) {
        return {
            type: getType('FETCH_DETAILS'),
            payload: MessageBusFactory.query('AptoPartsListFindPart', [id])
        }
    }

    function fetchElementUsageDetails(usageId) {
        return {
            type: getType('FETCH_ELEMENT_USAGE_DETAILS'),
            payload: MessageBusFactory.query('AptoPartsListFindElementUsage', [usageId])
        }
    }

    function fetchRuleUsageDetails(usageId) {
        return {
            type: getType('FETCH_RULE_USAGE_DETAILS'),
            payload: MessageBusFactory.query('AptoPartsListFindRuleUsage', [usageId])
        }
    }

    function resetDetails() {
        return {
            type: getType('RESET_DETAILS')
        }
    }

    function resetElementUsageDetails() {
        return {
            type: getType('RESET_ELEMENT_USAGE_DETAILS')
        }
    }

    function resetRuleUsageDetails() {
        return {
            type: getType('RESET_RULE_USAGE_DETAILS')
        }
    }

    function saveDetails(details, unitId) {
        return dispatch => {
            let commandArguments = [];

            if(!details.active) {
                details.active = false;
            }

            if(!details.partNumber) {
                details.partNumber = '';
            }

            if(!unitId) {
                unitId = null;
            }

            if(!details.amount && details.amount !== 0) {
                details.amount = null;
            }

            if(!details.currencyCode) {
                details.currencyCode = null;
            }

            commandArguments.push(details.active);
            commandArguments.push(details.partNumber);
            commandArguments.push(unitId);
            commandArguments.push(details.name);
            commandArguments.push(details.description);
            commandArguments.push(details.amount);
            commandArguments.push(details.currencyCode);

            if (typeof details.id !== 'undefined') {
                commandArguments.unshift(details.id);
                return dispatch({
                    type: getType('UPDATE_DETAILS'),
                    payload: MessageBusFactory.command('AptoPartsListUpdatePart', commandArguments)
                });
            }

            return dispatch({
                type: getType('ADD_DETAILS'),
                payload: MessageBusFactory.command('AptoPartsListAddPart', commandArguments)
            });
        }
    }

    function removeDetails(id) {
        return {
            type: getType('REMOVE_DETAILS'),
            payload: MessageBusFactory.command('AptoPartsListRemovePart', [id])
        };
    }

    function fetchAvailableUnits() {
        return {
            type: getType('FETCH_AVAILABLE_UNITS'),
            payload: MessageBusFactory.query('AptoPartsListFindUnits', [1, 1000, ''])
        }
    }

    function fetchAvailableProducts(searchString) {
        if (!searchString) {
            searchString = '';
        }
        return {
            type: getType('FETCH_AVAILABLE_PRODUCTS'),
            payload: MessageBusFactory.query('AptoPartsListFindProducts', [searchString])
        }
    }

    function fetchAvailableSections(searchString) {
        if (!searchString) {
            searchString = '';
        }
        return {
            type: getType('FETCH_AVAILABLE_SECTIONS'),
            payload: MessageBusFactory.query('AptoPartsListFindSections', [searchString])
        }
    }

    function fetchAvailableElements(searchString) {
        if (!searchString) {
            searchString = '';
        }
        return {
            type: getType('FETCH_AVAILABLE_ELEMENTS'),
            payload: MessageBusFactory.query('AptoPartsListFindElements', [searchString])
        }
    }

    function fetchProductUsages(partId) {
        return {
            type: getType('FETCH_PRODUCT_USAGES'),
            payload: MessageBusFactory.query('AptoPartsListFindProductUsages', [partId])
        }
    }

    function fetchSectionUsages(partId) {
        return {
            type: getType('FETCH_SECTION_USAGES'),
            payload: MessageBusFactory.query('AptoPartsListFindSectionUsages', [partId])
        }
    }

    function fetchElementUsages(partId) {
        return {
            type: getType('FETCH_ELEMENT_USAGES'),
            payload: MessageBusFactory.query('AptoPartsListFindElementUsages', [partId])
        }
    }

    function fetchRuleUsages(partId) {
        return {
            type: getType('FETCH_RULE_USAGES'),
            payload: MessageBusFactory.query('AptoPartsListFindRuleUsages', [partId])
        }
    }

    function fetchPrices(partId) {
        return {
            type: getType('FETCH_PRICES'),
            payload: MessageBusFactory.query('AptoPartsListFindPartPrices', [partId])
        }
    }

    function fetchProductsSectionsElements() {
        return {
            type: getType('FETCH_PRODUCTS_SECTIONS_ELEMENTS'),
            payload: MessageBusFactory.query('AptoPartsListFindProductsSectionsElements', [])
        }
    }

    function availableCustomerGroupsFetch() {
        return {
            type: getType('AVAILABLE_CUSTOMER_GROUPS_FETCH'),
            payload: MessageBusFactory.query('FindCustomerGroups', [''])
        }
    }

    function addProductUsage(partId, usageForUuid, quantity) {
        return {
            type: getType('ADD_PRODUCT_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListAddProductUsage', [partId, usageForUuid, quantity])
        }
    }

    function addSectionUsage(partId, usageForUuid, quantity, productId) {
        return {
            type: getType('ADD_SECTION_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListAddSectionUsage', [partId, usageForUuid, quantity, productId])
        }
    }

    function addElementUsage(partId, usageForUuid, quantity, productId) {
        return {
            type: getType('ADD_ELEMENT_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListAddElementUsage', [partId, usageForUuid, quantity, productId])
        }
    }

    function addRuleUsage(partId, name, quantity) {
        return {
            type: getType('ADD_RULE_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListAddRuleUsage', [partId, name, quantity])
        }
    }

    function addRuleUsageCondition(partId, usageId, productId, operator, value, sectionId, elementId, property, computedValueId) {
        return {
            type: getType('ADD_RULE_USAGE_CONDITION'),
            payload: MessageBusFactory.command('AptoPartsListAddRuleUsageCondition', [partId, usageId, productId, operator, value, sectionId, elementId, property, computedValueId])
        }
    }

    function removeRuleUsageCondition(partId, usageId, conditionId) {
        return {
            type: getType('ADD_RULE_USAGE_CONDITION'),
            payload: MessageBusFactory.command('AptoPartsListRemoveRuleUsageCondition', [partId, usageId, conditionId])
        }
    }

    function updateRuleUsageCondition(conditionId, partId, usageId, productId, operator, value, sectionId, elementId, property, computedValueId) {
        return {
            type: getType('UPDATE_RULE_USAGE_CONDITION'),
            payload: MessageBusFactory.command('AptoPartsListUpdateRuleUsageCondition', [conditionId, partId, usageId, productId, operator, value, sectionId, elementId, property, computedValueId])
        }
    }

    function updateProductUsageQuantity(partId, usageId, quantity) {
        return {
            type: getType('UPDATE_PRODUCT_USAGE_QUANTITY'),
            payload: MessageBusFactory.command('AptoPartsListUpdateProductUsageQuantity', [partId, usageId, quantity])
        }
    }

    function updateSectionUsageQuantity(partId, usageId, quantity) {
        return {
            type: getType('UPDATE_SECTION_USAGE_QUANTITY'),
            payload: MessageBusFactory.command('AptoPartsListUpdateSectionUsageQuantity', [partId, usageId, quantity])
        }
    }

    function updateElementUsage(partId, usageId, quantity, quantityCalculation) {
        return {
            type: getType('UPDATE_ELEMENT_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListUpdateElementUsage', [partId, usageId, quantity, quantityCalculation])
        }
    }
    
    function updateRuleUsage(partId, usageId, quantity, active, name, operator) {
        return {
            type: getType('UPDATE_RULE_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListUpdateRuleUsage', [partId, usageId, quantity, active, name, operator])
        }
    }

    function removeProductUsage(partId, usageId) {
        return {
            type: getType('REMOVE_PRODUCT_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListRemoveProductUsage', [partId, usageId])
        }
    }

    function removeSectionUsage(partId, usageId) {
        return {
            type: getType('REMOVE_SECTION_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListRemoveSectionUsage', [partId, usageId])
        }
    }

    function removeElementUsage(partId, usageId) {
        return {
            type: getType('REMOVE_ELEMENT_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListRemoveElementUsage', [partId, usageId])
        }
    }
    
    function removeRuleUsage(partId, usageId) {
        return {
            type: getType('REMOVE_RULE_USAGE'),
            payload: MessageBusFactory.command('AptoPartsListRemoveRuleUsage', [partId, usageId])
        }
    }

    function addPartPrice(partId, amount, currencyCode, customerGroupId) {
        return {
            type: getType('ADD_PART_PRICE'),
            payload: MessageBusFactory.command('AptoPartsListAddPartPrice', [partId, amount, currencyCode, customerGroupId])
        }
    }

    function removePartPrice(partId, priceId) {
        return {
            type: getType('REMOVE_PART_PRICE'),
            payload: MessageBusFactory.command('AptoPartsListRemovePartPrice', [partId, priceId])
        }
    }

    return {
        setSearchString: PageHeaderActions.setSearchString(TYPE_NS),
        setListTemplate: PageHeaderActions.setListTemplate(TYPE_NS),
        setSelected: DataListActions.setSelected(TYPE_NS),
        fetchList: fetchList,
        fetchDetails: fetchDetails,
        saveDetails: saveDetails,
        resetDetails: resetDetails,
        removeDetails: removeDetails,
        fetchAvailableUnits: fetchAvailableUnits,
        fetchAvailableProducts: fetchAvailableProducts,
        fetchAvailableSections: fetchAvailableSections,
        fetchAvailableElements: fetchAvailableElements,
        fetchElementUsageDetails: fetchElementUsageDetails,
        fetchRuleUsageDetails: fetchRuleUsageDetails,
        fetchProductUsages: fetchProductUsages,
        fetchSectionUsages: fetchSectionUsages,
        fetchElementUsages: fetchElementUsages,
        fetchRuleUsages: fetchRuleUsages,
        fetchPrices: fetchPrices,
        addProductUsage: addProductUsage,
        addSectionUsage: addSectionUsage,
        addElementUsage: addElementUsage,
        addRuleUsage: addRuleUsage,
        updateProductUsageQuantity: updateProductUsageQuantity,
        updateSectionUsageQuantity: updateSectionUsageQuantity,
        updateElementUsage: updateElementUsage,
        updateRuleUsage: updateRuleUsage,
        removeProductUsage: removeProductUsage,
        removeSectionUsage: removeSectionUsage,
        removeElementUsage: removeElementUsage,
        removeRuleUsage: removeRuleUsage,
        resetElementUsageDetails: resetElementUsageDetails,
        resetRuleUsageDetails: resetRuleUsageDetails,
        fetchProductsSectionsElements: fetchProductsSectionsElements,
        addRuleUsageCondition: addRuleUsageCondition,
        removeRuleUsageCondition: removeRuleUsageCondition,
        updateRuleUsageCondition: updateRuleUsageCondition,
        availableCustomerGroupsFetch: availableCustomerGroupsFetch,
        addPartPrice: addPartPrice,
        removePartPrice: removePartPrice
    };
};

Actions.$inject = ActionsInject;

export default ['AptoPartsListPartActions', Actions];