import ElementTab from './element-tab.html';
import PriceTab from './price-tab.html';
import DefinitionTab from './definition-tab.html';
import RenderImageTab from './render-image-tab.html';
import AttachmentTab from './attachment-tab.html';
import GalleryTab from './gallery-tab.html';
import CustomPropertiesTab from './custom-properties-tab.html';
import DiscountTab from './discount-tab.html';

const ElementDetailControllerInject = ['$scope', '$document', '$templateCache', '$mdDialog', '$ngRedux', 'LanguageFactory', 'ProductActions', 'SectionActions', 'ElementActions', 'targetEvent', 'productId', 'sectionId', 'elementId', 'closeSection', 'elements', 'editElement', 'APTO_RENDER_IMAGE_PERSPECTIVES'];
const ElementDetailController = function($scope, $document, $templateCache, $mdDialog, $ngRedux, LanguageFactory, ProductActions, SectionActions, ElementActions, targetEvent, productId, sectionId, elementId, closeSection, elements, editElement, APTO_RENDER_IMAGE_PERSPECTIVES) {
    $templateCache.put('catalog/pages/product/element/element-tab.html', ElementTab);
    $templateCache.put('catalog/pages/product/element/price-tab.html', PriceTab);
    $templateCache.put('catalog/pages/product/element/definition-tab.html', DefinitionTab);
    $templateCache.put('catalog/pages/product/element/render-image-tab.html', RenderImageTab);
    $templateCache.put('catalog/pages/product/element/attachment-tab.html', AttachmentTab);
    $templateCache.put('catalog/pages/product/element/gallery-tab.html', GalleryTab);
    $templateCache.put('catalog/pages/product/element/custom-properties-tab.html', CustomPropertiesTab);
    $templateCache.put('catalog/pages/product/element/discount-tab.html', DiscountTab);

    $scope.mapStateToThis = function(state) {
        return {
            availableCustomerGroups: state.product.availableCustomerGroups,
            detail: state.element.detail,
            productDetail: state.product.productDetail,
            sectionDetail: state.section.detail,
            registeredDefinitions: state.element.registeredDefinitions,
            definition: state.element.definition,
            prices: state.element.prices,
            priceFormulas: state.element.priceFormulas,
            discounts: state.element.discounts,
            renderImages: state.element.renderImages,

            attachments: state.element.attachments,
            gallery: state.element.gallery,

            availablePriceMatrices: state.element.availablePriceMatrices,
            availableSections: state.element.sections,
            sectionIdentifiers: state.element.sectionIdentifiers,
            elementIdentifiers: state.element.elementIdentifiers,
            customProperties: state.element.customProperties,
            conditions: state.product.conditions,
        }
    };

    const subscribedActions = $ngRedux.connect($scope.mapStateToThis, {
        updateProductElement: ProductActions.updateProductElement,
        addProductElementPrice: ProductActions.addProductElementPrice,
        removeProductElementPrice: ProductActions.removeProductElementPrice,
        addProductElementPriceFormula: ProductActions.addProductElementPriceFormula,
        removeProductElementPriceFormula: ProductActions.removeProductElementPriceFormula,
        addProductElementDiscount: ProductActions.addProductElementDiscount,
        removeProductElementDiscount: ProductActions.removeProductElementDiscount,
        addProductElementRenderImage: ProductActions.addProductElementRenderImage,
        removeProductElementRenderImage: ProductActions.removeProductElementRenderImage,

        addProductElementAttachment: ProductActions.addProductElementAttachment,
        addProductElementGallery: ProductActions.addProductElementGallery,

        removeProductElementAttachment: ProductActions.removeProductElementAttachment,
        removeProductElementGallery: ProductActions.removeProductElementGallery,

        addProductElementCustomProperty: ProductActions.addProductElementCustomProperty,
        removeProductElementCustomProperty: ProductActions.removeProductElementCustomProperty,
        fetchSectionElements: SectionActions.fetchElements,
        fetchDetail: ElementActions.fetchDetail,
        fetchRegisteredDefinitions: ElementActions.fetchRegisteredDefinitions,
        fetchPrices: ElementActions.fetchPrices,
        fetchPriceFormulas: ElementActions.fetchPriceFormulas,
        fetchDiscounts: ElementActions.fetchDiscounts,
        fetchRenderImages: ElementActions.fetchRenderImages,

        fetchAttachments: ElementActions.fetchAttachments,
        fetchGallery: ElementActions.fetchGallery,

        fetchAvailablePriceMatrices: ElementActions.fetchAvailablePriceMatrices,
        fetchCustomProperties: ElementActions.fetchCustomProperties,
        setDefinitionClassName: ElementActions.setDefinitionClassName,
        setDetailValue: ElementActions.setDetailValue,
        resetDefinitionValues: ElementActions.resetDefinitionValues,
        resetStore: ElementActions.reset,
        fetchSections: ElementActions.fetchSections,
        fetchConditions: ProductActions.fetchConditions,
    })($scope);

    function init() {
        initOptions();
        $scope.fetchDetail(elementId).then(() => {
            $scope.fetchRegisteredDefinitions().then(() => {
                for (let i = 0; i < $scope.registeredDefinitions.length; i++) {
                    if ($scope.registeredDefinitions[i].className === $scope.detail.definition.class) {
                        $scope.selectedDefinition = $scope.registeredDefinitions[i];
                        onChangeSelectedDefinition();
                        break;
                    }
                }
            });
            $scope.fetchAvailablePriceMatrices('').then((response) => {
                if (!$scope.detail.priceMatrix) {
                    return;
                }
                for (let i = 0; i < $scope.availablePriceMatrices.length; i++) {
                    if ($scope.availablePriceMatrices[i].id === $scope.detail.priceMatrix.id) {
                        $scope.priceMatrix.priceMatrix = $scope.availablePriceMatrices[i];
                        break;
                    }
                }
            });
        });

        $scope.fetchPrices(elementId);
        $scope.fetchPriceFormulas(elementId);
        $scope.fetchDiscounts(elementId);
        $scope.fetchRenderImages(elementId);

        $scope.fetchAttachments(elementId);
        $scope.fetchGallery(elementId);

        $scope.fetchCustomProperties(elementId);
        $scope.perspectives = APTO_RENDER_IMAGE_PERSPECTIVES.perspectives;
        $scope.elementListOpen = false;

        $scope.productId = productId;
        $scope.fetchConditions(productId);
        $scope.sectionId = sectionId;

        $scope.fetchSections(productId).then(() => {
            updateAvailableElements(false, 'renderImageOptions');
            updateAvailableElements(false, 'offsetOptions');
            updateAvailableSelectableValues(false, 'renderImageOptions');
            updateAvailableSelectableValues(false, 'offsetOptions');
            updateAvailableComputableValues(false, 'renderImageOptions');
            updateAvailableComputableValues(false, 'offsetOptions');
        });
    }

    $scope.getConditionName = function (id) {
        const condition = $scope.conditions.find((c) => c.id === id);

        return condition ? condition.identifier : null;
    }

    function initOptions(renderImage, copy = false) {

        // todo make enum and read from there
        $scope.galleryOptions = [
            {
                value: 'deactivated',
                label: 'Deaktiviert'
            },
            {
                value: 'image_preview',
                label: 'Vorschaubild'
            },
            {
                value: 'gallery',
                label: 'Galerie'
            },
        ];

        $scope.editRenderImageId = null;
        $scope.renderImageOptions = {
            name: '',
            file: '',
            layer: '',
            perspective: '',
            elementValueRefs: [],
            availableElements:  [],
            availableSelectableValues: [],
            availableComputableValues: [],
            availableSelectableValueTypes: ['Selectable', 'Computable'],
            availableAliases: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
            types: ['Statisch', 'Wiederholbar'],
            input: {
                type: 'Statisch',
                formulaHorizontal: null,
                formulaVertical: null,
                elementValueRef: {
                    sectionId: null,
                    elementId: null,
                    selectableValue: null,
                    selectableValueType: null,
                    alias: null
                }
            },
        };

        $scope.offsetOptions = {
            offsetX: 0,
            offsetUnitX: 0,
            offsetY: 0,
            offsetUnitY: 0,
            elementValueRefs: [],
            availableElements:  [],
            availableSelectableValues: [],
            availableComputableValues: [],
            availableSelectableValueTypes: ['Selectable', 'Computable'],
            availableOffsetUnits: [
                {id: 0, title: '%'},
                {id: 1, title: 'px'}
            ],
            availableAliases: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
            types: ['Statisch', 'Berechnend'],
            input: {
                type: 'Statisch',
                formulaOffsetX: null,
                formulaOffsetY: null,
                elementValueRef: {
                    sectionId: null,
                    elementId: null,
                    selectableValue: null,
                    selectableValueType: null,
                    alias: null
                }
            },
        };

        if(renderImage) {
            $scope.editRenderImageId = copy ? null : renderImage.id;
            if (!renderImage.renderImageOptions) {
                let mediaFile = renderImage.mediaFile[0];
                $scope.renderImageOptions.file = (mediaFile.path ? mediaFile.path : '/') + '/' + mediaFile.filename + '.' + mediaFile.extension;
                $scope.renderImageOptions.layer = renderImage.layer;
                $scope.renderImageOptions.perspective = renderImage.perspective;
                $scope.offsetOptions.offsetX = parseFloat(renderImage.offsetX);
                $scope.offsetOptions.offsetUnitX = parseInt(renderImage.offsetUnitX);
                $scope.offsetOptions.offsetY = parseFloat(renderImage.offsetY);
                $scope.offsetOptions.offsetUnitY = parseInt(renderImage.offsetUnitY);
                return;
            }

            $scope.renderImageOptions.name = renderImage.renderImageOptions.renderImageOptions.name;
            $scope.renderImageOptions.file = renderImage.renderImageOptions.renderImageOptions.file;
            $scope.renderImageOptions.perspective = renderImage.renderImageOptions.renderImageOptions.perspective;
            $scope.renderImageOptions.layer = renderImage.renderImageOptions.renderImageOptions.layer;
            $scope.renderImageOptions.input.type = renderImage.renderImageOptions.renderImageOptions.type;
            $scope.renderImageOptions.input.formulaHorizontal = renderImage.renderImageOptions.renderImageOptions.formulaHorizontal;
            $scope.renderImageOptions.input.formulaVertical = renderImage.renderImageOptions.renderImageOptions.formulaVertical;
            $scope.renderImageOptions.elementValueRefs = renderImage.renderImageOptions.renderImageOptions.elementValueRefs;
            $scope.offsetOptions.offsetX = renderImage.renderImageOptions.offsetOptions.offsetX;
            $scope.offsetOptions.offsetUnitX = renderImage.renderImageOptions.offsetOptions.offsetUnitX;
            $scope.offsetOptions.offsetY = renderImage.renderImageOptions.offsetOptions.offsetY;
            $scope.offsetOptions.offsetUnitY = renderImage.renderImageOptions.offsetOptions.offsetUnitY;
            $scope.offsetOptions.input.type = renderImage.renderImageOptions.offsetOptions.type;
            $scope.offsetOptions.input.formulaOffsetX = renderImage.renderImageOptions.offsetOptions.formulaOffsetX;
            $scope.offsetOptions.input.formulaOffsetY = renderImage.renderImageOptions.offsetOptions.formulaOffsetY;
            $scope.offsetOptions.elementValueRefs = renderImage.renderImageOptions.offsetOptions.elementValueRefs;
        }

        setAvailableAliases('renderImageOptions');
        setAvailableAliases('offsetOptions');

    }

    function setAvailableAliases (options) {
        $scope[options].availableAliases = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

        for (let i = 0; i < $scope[options].elementValueRefs.length; i++) {
            const currentAlias = $scope[options].elementValueRefs[i].alias;
            const currentIndex = $scope[options].availableAliases.indexOf(currentAlias);
            $scope[options].availableAliases.splice(currentIndex, 1);
        }
    }

    function onChangeSelectedDefinition() {
        $scope.setDefinitionClassName($scope.selectedDefinition.className);
        $scope.resetDefinitionValues();
        $scope.definitionValidation = false;
    }

    function addPrice() {
        $scope.addProductElementPrice(
            productId,
            sectionId,
            elementId,
            $scope.newPrice.amount,
            $scope.newPrice.currencyCode,
            $scope.newPrice.customerGroupId,
            $scope.newPrice.productConditionId
        ).then(() => {
            $scope.newPrice = {
                amount: '',
                currencyCode: 'EUR',
                customerGroupId: '',
                productConditionId: null
            };
            $scope.fetchPrices(elementId);
        });
    }

    function removePrice(priceId) {
        $scope.removeProductElementPrice(
            productId,
            sectionId,
            elementId,
            priceId
        ).then(() => {
            $scope.fetchPrices(elementId);
        });
    }

    function addPriceFormula() {
        $scope.addProductElementPriceFormula(
            productId,
            sectionId,
            elementId,
            $scope.newPriceFormula.formula,
            $scope.newPriceFormula.currencyCode,
            $scope.newPriceFormula.customerGroupId
        ).then(() => {
            $scope.newPriceFormula = {
                formula: '',
                currencyCode: 'EUR',
                customerGroupId: ''
            };
            $scope.fetchPriceFormulas(elementId);
        });
    }

    function removePriceFormula(priceFormulaId) {
        $scope.removeProductElementPriceFormula(
            productId,
            sectionId,
            elementId,
            priceFormulaId
        ).then(() => {
            $scope.fetchPriceFormulas(elementId);
        });
    }

    function addDiscount() {
        $scope.addProductElementDiscount(
            productId,
            sectionId,
            elementId,
            $scope.newDiscount.discount,
            $scope.newDiscount.customerGroupId,
            $scope.newDiscount.name
        ).then(() => {
            $scope.newDiscount = {
                discount: '',
                customerGroupId: '',
                name: null
            };
            $scope.fetchDiscounts(elementId);
        });
    }

    function removeDiscount(discountId) {
        $scope.removeProductElementDiscount(
            productId,
            sectionId,
            elementId,
            discountId
        ).then(() => {
            $scope.fetchDiscounts(elementId);
        });
    }

    function addElementRenderImage() {
        $scope.addProductElementRenderImage(
            productId, sectionId, elementId,
            {
                name: $scope.renderImageOptions.name,
                file: $scope.renderImageOptions.file,
                perspective: $scope.renderImageOptions.perspective,
                layer: $scope.renderImageOptions.layer,
                type: $scope.renderImageOptions.input.type,
                formulaHorizontal: $scope.renderImageOptions.input.formulaHorizontal,
                formulaVertical: $scope.renderImageOptions.input.formulaVertical,
                elementValueRefs: $scope.renderImageOptions.elementValueRefs
            },
            {
                offsetX: $scope.offsetOptions.offsetX,
                offsetUnitX: $scope.offsetOptions.offsetUnitX,
                offsetY: $scope.offsetOptions.offsetY,
                offsetUnitY: $scope.offsetOptions.offsetUnitY,
                type: $scope.offsetOptions.input.type,
                formulaOffsetX: $scope.offsetOptions.input.formulaOffsetX,
                formulaOffsetY: $scope.offsetOptions.input.formulaOffsetY,
                elementValueRefs: $scope.offsetOptions.elementValueRefs
            }
        ).then(() => {
            if ($scope.editRenderImageId !== null) {
                removeRenderImage($scope.editRenderImageId);
            }
            initOptions();
            $scope.fetchRenderImages(elementId);
        });
    }

    function editRenderImage(renderImage, copy = false) {
        initOptions(renderImage, copy);
    }

    function removeRenderImage(renderImageId) {
        $scope.removeProductElementRenderImage(productId, sectionId, elementId, renderImageId).then(() => {
            $scope.fetchRenderImages(elementId);
        });
    }

    function addElementAttachment() {
        $scope.addProductElementAttachment(
            productId, sectionId, elementId,
            {
                name: $scope.newAttachment.name,
                file: $scope.newAttachment.file
            }
        ).then(() => {
            if ($scope.newAttachment.editAttachmentId !== null) {
                removeAttachment($scope.newAttachment.editAttachmentId);
            }
            $scope.newAttachment = {
                name: '',
                file: '',
                editAttachmentId: null
            };
            $scope.fetchAttachments(elementId);
        });
    }

    function addElementGallery() {
        $scope.addProductElementGallery(
            productId, sectionId, elementId,
            {
                name: $scope.newGallery.name,
                file: $scope.newGallery.file
            }
        ).then(() => {
            if ($scope.newGallery.editGalleryId !== null) {
                removeGallery($scope.newGallery.editGalleryId);
            }
            $scope.newGallery = {
                name: '',
                file: '',
                editGalleryId: null
            };
            $scope.fetchGallery(elementId);
        });
    }

    function editAttachment(attachment) {
        $scope.newAttachment.name = angular.copy(attachment.name);
        $scope.newAttachment.file = attachment.mediaFile[0].path + '/' + attachment.mediaFile[0].filename + '.' + attachment.mediaFile[0].extension;
        $scope.newAttachment.editAttachmentId = attachment.id;
    }

    function editGallery(gallery) {
        $scope.newGallery.name = angular.copy(gallery.name);
        $scope.newGallery.file = gallery.mediaFile[0].path + '/' + gallery.mediaFile[0].filename + '.' + gallery.mediaFile[0].extension;
        $scope.newGallery.editGalleryId = gallery.id;
    }

    function removeAttachment(attachmentId) {
        $scope.removeProductElementAttachment(productId, sectionId, elementId, attachmentId).then(() => {
            $scope.fetchAttachments(elementId);
        });
    }

    function removeGallery(galleryId) {
        $scope.removeProductElementGallery(productId, sectionId, elementId, galleryId).then(() => {
            $scope.fetchGallery(elementId);
        });
    }

    function isAttachmentNameValid() {
        if (!$scope.newAttachment.name) {
            return false;
        }
        for (let locale in $scope.newAttachment.name) {
            if (!$scope.newAttachment.name.hasOwnProperty(locale)) {
                continue;
            }
            if ($scope.newAttachment.name[locale]) {
                return true;
            }
        }
        return false;
    }

    function isGalleryNameValid() {
        if (!$scope.newGallery.name) {
            return false;
        }
        for (let locale in $scope.newGallery.name) {
            if (!$scope.newGallery.name.hasOwnProperty(locale)) {
                continue;
            }
            if ($scope.newGallery.name[locale]) {
                return true;
            }
        }
        return false;
    }

    function onSelectPriceMatrix() {
        $scope.setDetailValue('priceMatrix', $scope.priceMatrix.priceMatrix);
    }

    function addElementCustomProperty(key, value, translatable) {
        $scope.addProductElementCustomProperty(productId, sectionId, elementId, key, value, translatable).then(() => {
            $scope.fetchCustomProperties(elementId);
        });
    }

    function removeElementCustomProperty(key) {
        $scope.removeProductElementCustomProperty(productId, sectionId, elementId, key).then(() => {
            $scope.fetchCustomProperties(elementId);
        });
    }

    function setDefinitionValidation(definitionValidation) {
        $scope.definitionValidation = definitionValidation;
    }

    function onSelectPreviewImage(path) {
        $scope.setDetailValue('previewImage', path);
    }

    function onSelectRenderImageFile(path) {
        $scope.renderImageOptions.file = path;
    }

    function onSelectAttachmentFile(path) {
        $scope.newAttachment.file = path;
    }

    function onSelectGalleryFile(path) {
        $scope.newGallery.file = path;
    }

    function save(elementForm) {
        if($scope.definitionValidation !== false) {
            if($scope.definitionValidation.validate() === false) {
                return;
            }
        }

        if(elementForm.$valid && ($scope.detail.identifier || !$scope.languageFactory.isEmpty($scope.detail.name))) {
            $scope.updateProductElement(productId, sectionId, elementId, $scope.detail, $scope.definition, $scope.priceMatrix.priceMatrix).then(() => {
                $scope.fetchSectionElements(sectionId);
                $scope.close();
            });
        }
    }

    function updateAvailableElements(resetValuesAfter, options) {
        if (!$scope[options].input.elementValueRef.sectionId) {
            return;
        }

        let section = findById($scope.availableSections, $scope[options].input.elementValueRef.sectionId);

        $scope[options].availableElements = [];
        for (let i = 0; i < section.elements.length; i++) {
            const element = section.elements[i];

            if (element.definition.properties) {
                $scope[options].availableElements.push(angular.copy(element));
            }
        }

        if (resetValuesAfter) {
            $scope[options].input.elementValueRef.elementId = null;
            $scope[options].input.elementValueRef.selectableValue = null;
            $scope[options].input.elementValueRef.selectableValueType = null;
        }
    }

    function updateAvailableSelectableAndComputableValues(options) {
        updateAvailableSelectableValues(true, options);
        updateAvailableComputableValues(true, options);
    }

    function updateAvailableSelectableValues(resetValuesAfter, options) {
        if (!$scope[options].input.elementValueRef.elementId) {
            return;
        }

        let element = findById($scope[options].availableElements, $scope[options].input.elementValueRef.elementId);
        $scope[options].availableSelectableValues = Object.keys(element.definition.properties);

        if (resetValuesAfter) {
            $scope[options].input.elementValueRef.selectableValue = null;
        }
    }

    function updateAvailableComputableValues(resetValuesAfter, options) {
        if (!$scope[options].input.elementValueRef.elementId) {
            return;
        }

        let element = findById($scope[options].availableElements, $scope[options].input.elementValueRef.elementId);
        $scope[options].availableComputableValues = Object.values(element.definition.computableValues);

        if (resetValuesAfter) {
            $scope[options].input.elementValueRef.selectableValue = null;
        }
    }

    function addElementValueRef(options) {
        pushElementValueRef({
            sectionId: $scope[options].input.elementValueRef.sectionId,
            elementId: $scope[options].input.elementValueRef.elementId,
            selectableValue: $scope[options].input.elementValueRef.selectableValue,
            selectableValueType: $scope[options].input.elementValueRef.selectableValueType,
            alias: $scope[options].input.elementValueRef.alias
        }, options);
        setAvailableAliases(options);
    }

    function pushElementValueRef(value, options) {
        if (elementValueRefAlreadyExists(value, options)) {
            return;
        }
        $scope[options].elementValueRefs.push(value);
    }

    function removeElementValueRef(index, options) {
        $scope[options].elementValueRefs.splice(index, 1);
        setAvailableAliases(options);
    }

    function elementValueRefAlreadyExists(value, options) {
        for (let i = 0; i < $scope[options].elementValueRefs.length; i++) {
            const elementValueRef = $scope[options].elementValueRefs[i];

            if (
                elementValueRef.sectionId === value.sectionId &&
                elementValueRef.elementId === value.elementId &&
                elementValueRef.selectableValue === value.selectableValue &&
                elementValueRef.selectableValueType === value.selectableValueType &&
                elementValueRef.compareValueType === value.compareValueType
            ) {
                return true;
            }
        }
        return false;
    }

    function getSectionIdentifier(sectionId) {
        if ($scope.sectionIdentifiers[sectionId]) {
            return $scope.sectionIdentifiers[sectionId];
        }
        return sectionId;
    }

    function getElementIdentifier(elementId) {
        if ($scope.elementIdentifiers[elementId]) {
            return $scope.elementIdentifiers[elementId];
        }
        return elementId;
    }

    function findById(values, id) {
        for (let i in values) {
            if (values.hasOwnProperty(i) && values[i].id === id) {
                return values[i];
            }
        }
        return null;
    }

    function close() {
        $scope.resetStore();
        $mdDialog.cancel();
    }

    function closeSectionElement() {
        closeSection();
        close();
    }

    function closeElement() {
        close();
    }

    function getName() {
        if(typeof $scope.detail !== "undefined") {
            if(Object.keys($scope.detail.name).length !== 0) {
                return $scope.languageFactory.translate( $scope.detail.name )
            }
            return $scope.detail.identifier;
        }
        return '';
    }

    function getSectionName() {
        if(typeof $scope.sectionDetail !== "undefined") {
            if(Object.keys($scope.sectionDetail.name).length !== 0) {
                return $scope.languageFactory.translate( $scope.sectionDetail.name )
            }
            return $scope.sectionDetail.identifier;
        }
        return '';
    }

    function getElementName(element) {
        if(typeof element === "undefined") {
            return
        }

        if(element.name) {
            return $scope.languageFactory.translate( element.name )
        }

        return element.identifier;
    }

    function closeAndEditElement($event, elementId) {
        editElement($event, elementId);
        close();
    }

    function showElementList() {
        $scope.elementListOpen = !$scope.elementListOpen;
    }

    function getElementListPosition() {
        const productTitleHeader = angular.element('.product-title-header')
        const sectionTitleHeader = angular.element('.section-title-header')
        const left = productTitleHeader.outerWidth() + sectionTitleHeader.outerWidth();
        return left + "px";
    }

    init();

    $scope.newPrice = {
        amount: '',
        currencyCode: 'EUR',
        customerGroupId: ''
    };
    $scope.newPriceFormula = {
        formula: '',
        currencyCode: 'EUR',
        customerGroupId: ''
    };
    $scope.newDiscount = {
        discount: '',
        customerGroupId: '',
        name: null
    };
    $scope.newAttachment = {
        name: '',
        file: '',
        editAttachmentId: null
    };
    $scope.newGallery = {
        name: '',
        file: '',
        editGalleryId: null
    };
    $scope.priceMatrix = {
        priceMatrix: null,
        searchTerm: ''
    };
    $scope.definitionValidation = false;
    $scope.languageFactory = LanguageFactory;

    $scope.addPrice = addPrice;
    $scope.removePrice = removePrice;
    $scope.addPriceFormula = addPriceFormula;
    $scope.removePriceFormula = removePriceFormula;
    $scope.addDiscount = addDiscount;
    $scope.removeDiscount = removeDiscount;
    $scope.addElementRenderImage = addElementRenderImage;
    $scope.editRenderImage = editRenderImage;
    $scope.removeRenderImage = removeRenderImage;

    $scope.addElementAttachment = addElementAttachment;
    $scope.addElementGallery = addElementGallery;

    $scope.editAttachment = editAttachment;
    $scope.editGallery = editGallery;

    $scope.removeAttachment = removeAttachment;
    $scope.removeGallery = removeGallery;

    $scope.isAttachmentNameValid = isAttachmentNameValid;
    $scope.isGalleryNameValid = isGalleryNameValid;

    $scope.addElementCustomProperty = addElementCustomProperty;
    $scope.removeElementCustomProperty = removeElementCustomProperty;
    $scope.setDefinitionValidation = setDefinitionValidation;
    $scope.save = save;
    $scope.close = close;
    $scope.onChangeSelectedDefinition = onChangeSelectedDefinition;
    $scope.onSelectPreviewImage = onSelectPreviewImage;
    $scope.onSelectRenderImageFile = onSelectRenderImageFile;

    $scope.onSelectAttachmentFile = onSelectAttachmentFile;
    $scope.onSelectGalleryFile = onSelectGalleryFile;

    $scope.getName = getName;
    $scope.getSectionName = getSectionName;
    $scope.closeSectionElement = closeSectionElement;
    $scope.closeElement = closeElement;
    $scope.updateAvailableElements = updateAvailableElements;
    $scope.updateAvailableSelectableAndComputableValues = updateAvailableSelectableAndComputableValues;
    $scope.addElementValueRef = addElementValueRef;
    $scope.removeElementValueRef = removeElementValueRef;
    $scope.getSectionIdentifier = getSectionIdentifier;
    $scope.getElementIdentifier = getElementIdentifier;
    $scope.$on('$destroy', subscribedActions);
    $scope.getElementName = getElementName;
    $scope.elements = elements;
    $scope.elementId = elementId;
    $scope.editElement = closeAndEditElement;
    $scope.showElementList = showElementList;
    $scope.getElementListPosition = getElementListPosition;
    $scope.onSelectPriceMatrix = onSelectPriceMatrix;
};

ElementDetailController.$inject = ElementDetailControllerInject;

export default ElementDetailController;
