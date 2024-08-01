<?php

namespace Apto\Plugins\ImportExport\Application\Backend\Commands\Import\Product;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoTranslatedValueItem;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValueNameNotValidException;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\InvalidAliasException;
use Apto\Catalog\Domain\Core\Model\Product\Element\RenderImageOptions;
use Apto\Catalog\Domain\Core\Model\Product\InvalidComputedValueNameException;
use Apto\Plugins\AreaElement\Domain\Core\Model\Product\Element\AreaElementDefinition;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas\CanvasRepository;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Product\Element\ImageUploadDefinition;
use Exception;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;

use Money\Currency;
use Money\Money;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroupRepository;
use Apto\Base\Domain\Core\Model\AptoDiscount\AptoDiscountDuplicateException;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;
use Apto\Base\Domain\Core\Service\StringSanitizer;

use Apto\Catalog\Application\Core\Service\PriceCalculator\SimplePriceCalculator;
use Apto\Catalog\Domain\Core\Model\Shop\ShopRepository;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\ElementDefinitionRegistry;
use Apto\Catalog\Domain\Core\Model\Product\Element\DefaultElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\IdentifierUniqueException;
use Apto\Catalog\Domain\Core\Model\Product\ProductShopCountException;
use Apto\Catalog\Domain\Core\Model\Product\ProductTaxRateException;
use Apto\Catalog\Domain\Core\Model\Product\ProductWeightException;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrix;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixRepository;

use Apto\Plugins\CustomText\Domain\Core\Model\Product\Element\CustomTextDefinition;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\PoolRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Product\Element\MaterialPickerElementDefinition;
use Apto\Plugins\WidthHeightElement\Domain\Core\Model\Product\Element\WidthHeightElementDefinition;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\Product\Element\PricePerUnitElementDefinition;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem\PricePerUnitItem;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem\PricePerUnitItemRepository;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\Product\Element\SelectBoxElementDefinition;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItem;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItemRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\Pool;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\Sanitizer\NameSanitizer;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\Product\Element\FloatInputElementDefinition;
use Apto\Plugins\ImportExport\Application\Backend\Commands\Import\AbstractImportDataTypeCommandHandler;

class ProductCommandHandler extends AbstractImportDataTypeCommandHandler
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var CustomerGroupRepository
     */
    private $customerGroupRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var PoolRepository
     */
    private $poolRepository;

    /**
     * @var CanvasRepository
     */
    private $canvasRepository;

    /**
     * @var PriceMatrixRepository
     */
    private $priceMatrixRepository;

    /**
     * @var PricePerUnitItemRepository
     */
    protected $pricePerUnitItemRepository;

    /**
     * @var SelectBoxItemRepository
     */
    protected $selectBoxItemRepository;

    /**
     * @var ElementDefinitionRegistry
     */
    private $elementDefinitionRegistry;

    /**
     * @var MediaFileRepository
     */
    private $mediaFileRepository;

    /**
     * @var MediaFileSystemConnector
     */
    private $mediaFileSystemConnector;

    /**
     * @var StringSanitizer
     */
    protected $sanitizer;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var AptoUuid
     */
    private $sectionId;

    /**
     * @var AptoUuid
     */
    private $elementId;

    /**
     * @param ShopRepository $shopRepository
     * @param CustomerGroupRepository $customerGroupRepository
     * @param ProductRepository $productRepository
     * @param PoolRepository $poolRepository
     * @param CanvasRepository $canvasRepository
     * @param PriceMatrixRepository $priceMatrixRepository
     * @param PricePerUnitItemRepository $pricePerUnitItemRepository
     * @param SelectBoxItemRepository $selectBoxItemRepository
     * @param ElementDefinitionRegistry $elementDefinitionRegistry
     * @param MediaFileRepository $mediaFileRepository
     * @param MediaFileSystemConnector $mediaFileSystemConnector
     * @param StringSanitizer $sanitizer
     */
    public function __construct(
        ShopRepository $shopRepository,
        CustomerGroupRepository $customerGroupRepository,
        ProductRepository $productRepository,
        PoolRepository $poolRepository,
        CanvasRepository $canvasRepository,
        PriceMatrixRepository $priceMatrixRepository,
        PricePerUnitItemRepository $pricePerUnitItemRepository,
        SelectBoxItemRepository $selectBoxItemRepository,
        ElementDefinitionRegistry $elementDefinitionRegistry,
        MediaFileRepository $mediaFileRepository,
        MediaFileSystemConnector $mediaFileSystemConnector,
        StringSanitizer $sanitizer
    ) {
        $this->shopRepository = $shopRepository;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->productRepository = $productRepository;
        $this->poolRepository = $poolRepository;
        $this->canvasRepository = $canvasRepository;
        $this->priceMatrixRepository = $priceMatrixRepository;
        $this->pricePerUnitItemRepository = $pricePerUnitItemRepository;
        $this->selectBoxItemRepository = $selectBoxItemRepository;
        $this->elementDefinitionRegistry = $elementDefinitionRegistry;
        $this->mediaFileRepository = $mediaFileRepository;
        $this->mediaFileSystemConnector = $mediaFileSystemConnector;
        $this->sanitizer = $sanitizer;
    }

    /**
     * @param ImportDefaultDataType $command
     * @throws Exception
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleImportDefaultDataType(ImportDefaultDataType $command)
    {
        // init element
        $this->initElement($command->getLocale(), $command->getDomain(), $command->getFields());

        // map definition values
        $definitionClass = DefaultElementDefinition::class;
        $definitionValues = [];

        // process data type
        $this->processDataType(
            $command->getLocale(),
            $command->getFields(),
            $definitionClass,
            $definitionValues
        );
    }

    /**
     * @param ImportMaterialPickerDataType $command
     * @throws Exception
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleImportMaterialPickerDataType(ImportMaterialPickerDataType $command)
    {
        // init element
        $this->initElement($command->getLocale(), $command->getDomain(), $command->getFields());

        // map definition values
        $definitionClass = MaterialPickerElementDefinition::class;
        $definitionValues = $this->mapMaterialPickerDefinitionValues($command->getLocale(), $command->getFields());

        // process data type
        $this->processDataType(
            $command->getLocale(),
            $command->getFields(),
            $definitionClass,
            $definitionValues
        );
    }

    /**
     * @param ImportImageUploadDataType $command
     * @return void
     * @throws AptoDiscountDuplicateException
     * @throws AptoPriceDuplicateException
     * @throws IdentifierUniqueException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleImportImageUploadDataType(ImportImageUploadDataType $command)
    {
        // init element
        $this->initElement($command->getLocale(), $command->getDomain(), $command->getFields());

        // map definition values
        $definitionClass = ImageUploadDefinition::class;
        $definitionValues = $this->mapImageUploadDefinitionValues($command->getFields());

        // process data type
        $this->processDataType(
            $command->getLocale(),
            $command->getFields(),
            $definitionClass,
            $definitionValues
        );
    }

    /**
     * @param ImportAreaElementDataType $command
     * @throws Exception
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleImportAreaElementDataType(ImportAreaElementDataType $command)
    {
        // init element
        $this->initElement($command->getLocale(), $command->getDomain(), $command->getFields());

        // map definition values
        $definitionClass = AreaElementDefinition::class;
        $definitionValues = $this->mapAreaElementDefinitionValues($command->getLocale(), $command->getFields());

        // process data type
        $this->processDataType(
            $command->getLocale(),
            $command->getFields(),
            $definitionClass,
            $definitionValues
        );
    }

    /**
     * @param ImportWidthHeightDataType $command
     * @throws Exception
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleImportWidthHeightDataType(ImportWidthHeightDataType $command)
    {
        // init element
        $this->initElement($command->getLocale(), $command->getDomain(), $command->getFields());

        // map definition values
        $definitionClass = WidthHeightElementDefinition::class;
        $definitionValues = $this->mapWidthHeightDefinitionValues($command->getLocale(), $command->getFields());

        // process data type
        $this->processDataType(
            $command->getLocale(),
            $command->getFields(),
            $definitionClass,
            $definitionValues
        );
    }

    /**
     * @param ImportCustomTextDataType $command
     * @throws AptoDiscountDuplicateException
     * @throws AptoPriceDuplicateException
     * @throws IdentifierUniqueException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleImportCustomTextDataType(ImportCustomTextDataType $command)
    {
        // init element
        $this->initElement($command->getLocale(), $command->getDomain(), $command->getFields());

        // map definition values
        $definitionClass = CustomTextDefinition::class;
        $definitionValues = $this->mapCustomTextDefinitionValues($command->getFields());

        // process data type
        $this->processDataType(
            $command->getLocale(),
            $command->getFields(),
            $definitionClass,
            $definitionValues
        );
    }

    /**
     * @param ImportPricePerUnitDataType $command
     * @throws Exception
     * @throws AptoPriceDuplicateException
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleImportPricePerUnitDataType(ImportPricePerUnitDataType $command)
    {
        // init element
        $this->initElement($command->getLocale(), $command->getDomain(), $command->getFields());

        // map definition values
        $definitionClass = PricePerUnitElementDefinition::class;
        $definitionValues = $this->mapPricePerUnitDefinitionValues($command->getFields());

        // process data type
        $this->processDataType(
            $command->getLocale(),
            $command->getFields(),
            $definitionClass,
            $definitionValues
        );

        // process price per unit prices
        $this->processPricePerUnitPrices($command->getFields());
    }

    /**
     * @param ImportFloatInputDataType $command
     * @throws Exception
     * @throws AptoPriceDuplicateException
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleImportFloatInputDataType(ImportFloatInputDataType $command)
    {
        // init element
        $this->initElement($command->getLocale(), $command->getDomain(), $command->getFields());

        // map definition values
        $definitionClass = FloatInputElementDefinition::class;
        $definitionValues = $this->mapFloatInputDefinitionValues($command->getLocale(), $command->getFields());

        // process data type
        $this->processDataType(
            $command->getLocale(),
            $command->getFields(),
            $definitionClass,
            $definitionValues
        );
    }

    /**
     * @param ImportSelectBoxDataType $command
     * @throws Exception
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    public function handleImportSelectBoxDataType(ImportSelectBoxDataType $command)
    {
        // init element
        $this->initElement($command->getLocale(), $command->getDomain(), $command->getFields());

        // map definition values
        $definitionClass = SelectBoxElementDefinition::class;
        $definitionValues = [];

        // process data type
        $this->processDataType(
            $command->getLocale(),
            $command->getFields(),
            $definitionClass,
            $definitionValues
        );

        // process select box items
        $this->processSelectBoxItems($command->getLocale(), $command->getFields());
    }

    /**
     * @param ImportProductComputedValueDataType $command
     * @throws ComputedProductValueNameNotValidException
     * @throws InvalidComputedValueNameException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     * @throws InvalidAliasException
     */
    public function handleImportProductComputedValueDataType(ImportProductComputedValueDataType $command)
    {
        // init product
        $fields = $command->getFields();
        $this->product = $this->getProduct($command->getLocale(), $command->getDomain(), $fields);

        // get existing computed value
        $value = null;
        $name = ComputedProductValue::sanitizeName($fields['name']);
        $computedProductValues = $this->product->getComputedProductValues();
        /** @var ComputedProductValue $computedProductValue */
        foreach ($computedProductValues as $computedProductValue) {
            if ($computedProductValue->getName() === $name) {
                $value = $computedProductValue;
                break;
            }
        }

        // add new computed value
        if (null === $value) {
            $value = new ComputedProductValue(
                new AptoUuid(),
                $name,
                $this->product
            );
            $this->product->addComputedProductValue($value);
        }

        // set formula
        $value->setFormula($fields['formula']);

        // add alias
        if (
            array_key_exists('section-identifier', $fields) &&
            array_key_exists('element-identifier', $fields) &&
            array_key_exists('alias', $fields) &&
            '' !== trim($fields['section-identifier']) &&
            '' !== trim($fields['element-identifier']) &&
            '' !== trim($fields['alias'])
        ) {
            // set section id
            $sectionIdentifier = new Identifier($fields['section-identifier']);
            $sectionId = $this->getSectionId($sectionIdentifier);
            if (null === $sectionId) {
                return;
            }

            // set element id
            $elementIdentifier = new Identifier($fields['element-identifier']);
            $elementId = $this->getElementId($sectionId, $elementIdentifier);
            if (null === $elementId) {
                return;
            }

            // set alias id
            $aliasId = $value->getAliasId(trim($fields['alias']));
            if (null !== $aliasId) {
                $value->removeAlias($aliasId->getId());
            }

            // set property
            $property = '';
            if (array_key_exists('property', $fields) && '' !== trim($fields['property'])) {
                $property = trim($fields['property']);
            }

            // add alias to computed value
            $value->addAlias(
                $sectionId,
                $elementId,
                trim($fields['alias']),
                $property,
                false
            );
        }
    }

    /**
     * @param string $locale
     * @param string $domain
     * @param array $fields
     * @throws Exception
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    protected function initElement(string $locale, string $domain, array $fields)
    {
        // init identifiers
        $sectionIdentifier = new Identifier($fields['section-identifier']);
        $elementIdentifier = new Identifier($fields['element-identifier']);

        // init product
        $this->product = $this->getProduct($locale, $domain, $fields);

        // init section
        $this->sectionId = $this->getSectionId($sectionIdentifier);
        if (null === $this->sectionId) {
            $this->addSection($sectionIdentifier, $locale);
            $this->sectionId = $this->getSectionId($sectionIdentifier);
        }

        // init element
        $this->elementId = $this->getElementId($this->sectionId, $elementIdentifier);
        if (null === $this->elementId) {
            $this->addElement($elementIdentifier, $locale);
            $this->elementId = $this->getElementId($this->sectionId, $elementIdentifier);
        }
    }

    /**
     * @param string $locale
     * @param array $fields
     * @param string $definitionClass
     * @param array $definitionValues
     * @throws AptoDiscountDuplicateException
     * @throws AptoPriceDuplicateException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    protected function processDataType(string $locale, array $fields, string $definitionClass, array $definitionValues)
    {
        $this->processProduct($locale, $fields);
        $this->processSection($locale, $fields);
        $this->processElement($locale, $fields, $definitionClass, $definitionValues);

        $this->product->publishEvents();
    }

    /**
     * @param string $locale
     * @param array $fields
     * @throws AptoDiscountDuplicateException
     * @throws AptoPriceDuplicateException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    private function processProduct(string $locale, array $fields)
    {
        // set product name
        if (array_key_exists('product-name', $fields)) {
            $this->product->setName(
                AptoTranslatedValue::addTranslation(
                    $this->product->getName() ?? new AptoTranslatedValue([]),
                    new AptoTranslatedValueItem(new AptoLocale($locale), $fields['product-name'])
                )
            );
        }

        // set product beschreibung
        if (array_key_exists('product-beschreibung', $fields)) {
            $this->product->setDescription(
                AptoTranslatedValue::addTranslation(
                    $this->product->getDescription() ?? new AptoTranslatedValue([]),
                    new AptoTranslatedValueItem(new AptoLocale($locale), $fields['product-beschreibung'])
                )
            );
        }

        // set product step by step
        if (array_key_exists('product-step-by-step', $fields)) {
            $this->product->setUseStepByStep(
                $this->convertToBool($fields['product-step-by-step'])
            );
        }

        // set product prices
        $prices = $this->getMultipleFieldValues($fields, 'product-price_', '|');
        if (count($prices) > 0) {
            $this->product->clearAptoPrices();

            foreach ($prices as $price) {
                $aptoPrice = $this->getAptoPriceFromPriceField($price);

                // if not all required fields are set continue
                if (null === $aptoPrice) {
                    continue;
                }

                // add price
                $this->product->addAptoPrice($aptoPrice[0], $aptoPrice[1]);
            }
        }

        // set product discounts
        $discounts = $this->getMultipleFieldValues($fields, 'product-discount_', '|');
        if (count($discounts) > 0) {
            $this->product->clearAptoDiscounts();

            foreach ($discounts as $discount) {
                $aptoDiscount = $this->getAptoDiscountFromPriceField($discount, $locale);

                // if not all required fields are set continue
                if (null === $aptoDiscount) {
                    continue;
                }

                $this->product->addAptoDiscount($aptoDiscount[0], $aptoDiscount[1], $aptoDiscount[2]);
            }
        }

        // set product preview image
        if (array_key_exists('product-preview-image', $fields) && trim($fields['product-preview-image'])) {
            $this->product
                ->setPreviewImage(
                    $this->getMediaFileFromPath(
                        $this->sanitizeFilename(trim($fields['product-preview-image'])),
                        $this->mediaFileRepository,
                        $this->mediaFileSystemConnector
                    )
                )
            ;
        }
    }

    /**
     * @param string $locale
     * @param array $fields
     * @throws AptoDiscountDuplicateException
     * @throws AptoPriceDuplicateException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    private function processSection(string $locale, array $fields)
    {
        // set section name
        if (array_key_exists('section-name', $fields)) {
            $this->product
                ->setSectionName(
                    $this->sectionId,
                    AptoTranslatedValue::addTranslation(
                        $this->product->getSectionName($this->sectionId) ?? new AptoTranslatedValue([]),
                        new AptoTranslatedValueItem(new AptoLocale($locale), $fields['section-name'])
                    )
                )
            ;
        }

        // set section beschreibung
        if (array_key_exists('section-beschreibung', $fields)) {
            $this->product
                ->setSectionDescription(
                    $this->sectionId,
                    AptoTranslatedValue::addTranslation(
                        $this->product->getSectionDescription($this->sectionId) ?? new AptoTranslatedValue([]),
                        new AptoTranslatedValueItem(new AptoLocale($locale), $fields['section-beschreibung'])
                    )
                )
            ;
        }

        // set section position
        if (array_key_exists('section-position', $fields)) {
            $this->product
                ->setSectionPosition(
                    $this->sectionId,
                    $this->convertToInt($fields['section-position'])
                )
            ;
        }

        // set section required
        if (array_key_exists('section-required', $fields)) {
            $this->product->setSectionIsMandatory(
                $this->sectionId,
                $this->convertToBool($fields['section-required'])
            );
        }

        // set section prices
        $prices = $this->getMultipleFieldValues($fields, 'section-price_', '|');
        if (count($prices) > 0) {
            $this->product->clearSectionPrices($this->sectionId);

            foreach ($prices as $price) {
                $aptoPrice = $this->getAptoPriceFromPriceField($price);

                // if not all required fields are set continue
                if (null === $aptoPrice) {
                    continue;
                }

                // add price
                $this->product->addSectionPrice($this->sectionId, $aptoPrice[0], $aptoPrice[1]);
            }
        }

        // set section discounts
        $discounts = $this->getMultipleFieldValues($fields, 'section-discount_', '|');
        if (count($discounts) > 0) {
            $this->product->clearSectionDiscounts($this->sectionId);

            foreach ($discounts as $discount) {
                $aptoDiscount = $this->getAptoDiscountFromPriceField($discount, $locale);

                // if not all required fields are set continue
                if (null === $aptoDiscount) {
                    continue;
                }

                $this->product->addSectionDiscount($this->sectionId, $aptoDiscount[0], $aptoDiscount[1], $aptoDiscount[2]);
            }
        }
    }

    /**
     * @param string $locale
     * @param array $fields
     * @param string $definitionClass
     * @param array $definitionValues
     * @throws AptoDiscountDuplicateException
     * @throws AptoPriceDuplicateException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    private function processElement(string $locale, array $fields, string $definitionClass, array $definitionValues)
    {
        // set element name
        if (array_key_exists('element-name', $fields)) {
            $this->product
                ->setElementName(
                    $this->sectionId, $this->elementId,
                    AptoTranslatedValue::addTranslation(
                        $this->product->getElementName($this->sectionId, $this->elementId) ?? new AptoTranslatedValue([]),
                        new AptoTranslatedValueItem(new AptoLocale($locale), $fields['element-name'])
                    )
                )
            ;
        }

        // set element beschreibung
        if (array_key_exists('element-beschreibung', $fields)) {
            $this->product->setElementDescription(
                $this->sectionId, $this->elementId,
                AptoTranslatedValue::addTranslation(
                    $this->product->getElementDescription($this->sectionId, $this->elementId) ?? new AptoTranslatedValue([]),
                    new AptoTranslatedValueItem(new AptoLocale($locale), $fields['element-beschreibung'])
                )
            );
        }

        // set element position
        if (array_key_exists('element-position', $fields)) {
            $this->product
                ->setElementPosition(
                    $this->sectionId, $this->elementId,
                    $this->convertToInt($fields['element-position'])
                )
            ;
        }

        // set element default
        if (array_key_exists('element-default', $fields)) {
            $this->product
                ->setElementIsDefault(
                    $this->sectionId, $this->elementId,
                    $this->convertToBool($fields['element-default'])
                )
            ;
        }

        // set element preview image
        if (array_key_exists('element-preview-image', $fields) && trim($fields['element-preview-image'])) {
            $this->product
                ->setElementPreviewImage(
                    $this->sectionId, $this->elementId,
                    $this->getMediaFileFromPath(
                        $this->sanitizeFilename(trim($fields['element-preview-image'])),
                        $this->mediaFileRepository,
                        $this->mediaFileSystemConnector
                    )
                )
            ;
        }

        // set element prices
        $prices = $this->getMultipleFieldValues($fields, 'element-price_', '|');
        if (count($prices) > 0) {
            $this->product->clearElementPrices($this->sectionId, $this->elementId);

            foreach ($prices as $price) {
                $aptoPrice = $this->getAptoPriceFromPriceField($price);

                // if not all required fields are set continue
                if (null === $aptoPrice) {
                    continue;
                }

                // add price
                $this->product->addElementPrice($this->sectionId, $this->elementId, $aptoPrice[0], $aptoPrice[1]);
            }
        }

        // set element extended price calculation
        if (array_key_exists('extended-price-calculation-active', $fields)) {
            $this->product->setElementExtendedPriceCalculationActive(
                $this->sectionId,
                $this->elementId,
                $this->convertToBool($fields['extended-price-calculation-active'])
            );
        }
        if (array_key_exists('extended-price-calculation-formula', $fields)) {
            $this->product->setElementExtendedPriceCalculationFormula(
                $this->sectionId,
                $this->elementId,
                trim($fields['extended-price-calculation-formula'])
            );
        }

        // set element discounts
        $discounts = $this->getMultipleFieldValues($fields, 'element-discount_', '|');
        if (count($discounts) > 0) {
            $this->product->clearElementDiscounts($this->sectionId, $this->elementId);

            foreach ($discounts as $discount) {
                $aptoDiscount = $this->getAptoDiscountFromPriceField($discount, $locale);

                // if not all required fields are set continue
                if (null === $aptoDiscount) {
                    continue;
                }

                $this->product->addElementDiscount($this->sectionId, $this->elementId, $aptoDiscount[0], $aptoDiscount[1], $aptoDiscount[2]);
            }
        }

        // set element render images
        $renderImages = $this->getMultipleFieldValues($fields, 'element-render-image_', '|');
        if (count($renderImages) > 0) {
            $this->product->clearElementRenderImages($this->sectionId, $this->elementId);
            foreach ($renderImages as $renderImage) {
                if (count($renderImage) < 3) {
                    continue;
                }

                $renderImageOptions = new RenderImageOptions([
                    'type' => 'Statisch',
                    'file' => $this->sanitizeFilename(trim($renderImage[0])),
                    'perspective' => trim($renderImage[1]),
                    'layer' => $this->convertToInt($renderImage[2]),
                    'name' => trim($renderImage[1]),
                    'formulaHorizontal' => null,
                    'formulaVertical' => null,
                    'elementValueRefs' => []
                ], [
                    'type' => 'Statisch',
                    'offsetUnitX' => 0,
                    'offsetUnitY' => 0,
                    'offsetX' => 0,
                    'offsetY' => 0,
                    'formulaOffsetX' => null,
                    'formulaOffsetY' => null,
                    'elementValueRefs' => []
                ]);

                $mediaFile = $this->getMediaFileFromPath(
                    $renderImageOptions->getFile(),
                    $this->mediaFileRepository,
                    $this->mediaFileSystemConnector
                );

                $this->product->addElementRenderImage(
                    $this->sectionId,
                    $this->elementId,
                    $renderImageOptions->getLayer(),
                    $renderImageOptions->getPerspective(),
                    $mediaFile,
                    $renderImageOptions->getOffsetX(),
                    $renderImageOptions->getOffsetUnitX(),
                    $renderImageOptions->getOffsetY(),
                    $renderImageOptions->getOffsetUnitY(),
                    $renderImageOptions

                );
            }
        }

        // set element definition
        $registeredDefinition = $this->elementDefinitionRegistry->getRegisteredElementDefinition($definitionClass);
        $this->product
            ->setElementDefinition(
                $this->sectionId, $this->elementId,
                $registeredDefinition->getElementDefinition($definitionValues)
            )
        ;
    }

    /**
     * @param string $locale
     * @param array $fields
     * @return array
     * @throws InvalidUuidException
     */
    private function mapMaterialPickerDefinitionValues(string $locale, array $fields): array
    {
        $definitionValues = [
            'poolId' => ''
        ];

        $fields['pool'] = NameSanitizer::sanitizeName($fields['pool']);
        if ('' === $fields['pool']) {
            return $definitionValues;
        }

        $poolId = $this->poolRepository->findFirstIdByName($fields['pool']);
        if (null === $poolId) {
            $pool = new Pool(
                $this->poolRepository->nextIdentity(),
                $this->getTranslatedValue([$locale => $fields['pool']])
            );
            $this->poolRepository->add($pool);
            $pool->publishEvents();

            $poolId = $pool->getId()->getId();
        }

        $definitionValues['poolId'] = $poolId;
        return $definitionValues;
    }

    /**
     * @param array $fields
     * @return array[]
     * @throws Exception
     */
    private function mapImageUploadDefinitionValues(array $fields): array
    {
        if ('' === $fields['canvas']) {
            throw new Exception('DataType image-upload: Canvas cant be an empty string.');
        }
        $canvasId = $this->canvasRepository->findIdByIdentifier(new Identifier($fields['canvas']));

        if (null === $canvasId) {
            throw new Exception('DataType image-upload: Canvas "' . $fields['canvas'] . '" not found.');
        }

        return [
            'canvas' => [
                'source' => 'Global',
                'canvasId' => $canvasId->getId()
            ]
        ];
    }

    /**
     * @param string $locale
     * @param array $fields
     * @return array
     * @throws InvalidUuidException
     */
    private function mapAreaElementDefinitionValues(string $locale, array $fields): array
    {
        $minValues = $this->getMultipleFieldValues($fields, 'min_');
        $maxValues = $this->getMultipleFieldValues($fields, 'max_');
        $stepValues = $this->getMultipleFieldValues($fields, 'step_');
        $prefixValues = $this->getMultipleFieldValues($fields, 'prefix_');
        $suffixValues = $this->getMultipleFieldValues($fields, 'suffix_');

        $fieldValues = [];

        // collect min values
        foreach ($minValues as $key => $minValue) {
            if ('' === trim($minValue)) {
                continue;
            }

            $key = explode('_', $key);
            $key = $key[1];
            $fieldValues[$key]['values'][0]['minimum'] = $this->convertToFloat($minValue);
        }

        // collect max values
        foreach ($maxValues as $key => $maxValue) {
            if ('' === trim($maxValue)) {
                continue;
            }

            $key = explode('_', $key);
            $key = $key[1];
            $fieldValues[$key]['values'][0]['maximum'] = $this->convertToFloat($maxValue);
        }

        // collect step values
        foreach ($stepValues as $key => $stepValue) {
            if ('' === trim($stepValue)) {
                continue;
            }

            $key = explode('_', $key);
            $key = $key[1];
            $fieldValues[$key]['values'][0]['step'] = $this->convertToFloat($stepValue);
        }

        // collect prefix values
        foreach ($prefixValues as $key => $prefixValue) {
            if ('' === trim($prefixValue)) {
                continue;
            }

            $key = explode('_', $key);
            $key = $key[1];
            $fieldValues[$key]['prefix'] = [$locale => $prefixValue];
        }

        // collect suffix values
        foreach ($suffixValues as $key => $suffixValue) {
            if ('' === trim($suffixValue)) {
                continue;
            }

            $key = explode('_', $key);
            $key = $key[1];
            $fieldValues[$key]['suffix'] = [$locale => $suffixValue];
        }

        // validate field values
        foreach ($fieldValues as $key => $fieldValue) {
            if (
                !array_key_exists('minimum', $fieldValue['values'][0]) ||
                !array_key_exists('maximum', $fieldValue['values'][0]) ||
                !array_key_exists('step', $fieldValue['values'][0])
            ) {
                unset($fieldValues[$key]);
            }

            if (!array_key_exists('prefix', $fieldValue)) {
                $fieldValues[$key]['prefix'] = [];
            }

            if (!array_key_exists('suffix', $fieldValue)) {
                $fieldValues[$key]['suffix'] = [];
            }
        }

        $definitionValues = [
            'priceMatrix' => [
                'id' => null,
                'row' => null,
                'column' => null
            ],
            'fields' => array_values($fieldValues)
        ];

        // process optional fields
        // price matrix
        if (array_key_exists('price-matrix', $fields)) {
            $fields['price-matrix'] = NameSanitizer::sanitizeName($fields['price-matrix']);
            if ('' !== $fields['price-matrix']) {
                $priceMatrixId = $this->priceMatrixRepository->findFirstIdByName($fields['price-matrix']);
                if (null === $priceMatrixId) {
                    $priceMatrix = new PriceMatrix(
                        $this->priceMatrixRepository->nextIdentity(),
                        $this->getTranslatedValue([$locale => $fields['price-matrix']])
                    );

                    $this->priceMatrixRepository->add($priceMatrix);
                    $priceMatrix->publishEvents();

                    $priceMatrixId = $priceMatrix->getId()->getId();
                }

                $definitionValues['priceMatrix']['id'] = $priceMatrixId;
            }
        }

        if (array_key_exists('row-formula', $fields)) {
            $definitionValues['priceMatrix']['row'] = $fields['row-formula'];
        }

        if (array_key_exists('column-formula', $fields)) {
            $definitionValues['priceMatrix']['column'] = $fields['column-formula'];
        }

        return $definitionValues;
    }

    /**
     * @param string $locale
     * @param array $fields
     * @return array
     * @throws InvalidUuidException
     */
    private function mapWidthHeightDefinitionValues(string $locale, array $fields): array
    {
        $definitionValues = [
            'priceMatrixId' => '',
            'width' => [
                [
                    'minimum' => $this->convertToFloat($fields['min-width']),
                    'maximum' => $this->convertToFloat($fields['max-width']),
                    'step' => $this->convertToFloat($fields['step-width'])
                ]
            ],
            'height' => [
                [
                    'minimum' => $this->convertToFloat($fields['min-height']),
                    'maximum' => $this->convertToFloat($fields['max-height']),
                    'step' => $this->convertToFloat($fields['step-height'])
                ]
            ]
        ];

        // process optional fields
        // price matrix
        if (array_key_exists('price-matrix', $fields)) {
            $fields['price-matrix'] = NameSanitizer::sanitizeName($fields['price-matrix']);
            if ('' !== $fields['price-matrix']) {
                $priceMatrixId = $this->priceMatrixRepository->findFirstIdByName($fields['price-matrix']);
                if (null === $priceMatrixId) {
                    $priceMatrix = new PriceMatrix(
                        $this->priceMatrixRepository->nextIdentity(),
                        $this->getTranslatedValue([$locale => $fields['price-matrix']])
                    );

                    $this->priceMatrixRepository->add($priceMatrix);
                    $priceMatrix->publishEvents();

                    $priceMatrixId = $priceMatrix->getId()->getId();
                }

                $definitionValues['priceMatrixId'] = $priceMatrixId;
            }
        }

        // prefix width
        if (array_key_exists('prefix-width', $fields)) {
            $definitionValues['prefixWidth'] = [$locale => $fields['prefix-width']];
        }

        // prefix height
        if (array_key_exists('prefix-height', $fields)) {
            $definitionValues['prefixHeight'] = [$locale => $fields['prefix-height']];
        }

        // suffix width
        if (array_key_exists('suffix-width', $fields)) {
            $definitionValues['suffixWidth'] = [$locale => $fields['suffix-width']];
        }

        // suffix height
        if (array_key_exists('suffix-height', $fields)) {
            $definitionValues['suffixHeight'] = [$locale => $fields['suffix-height']];
        }

        return $definitionValues;
    }

    /**
     * @param array $fields
     * @return array
     */
    private function mapCustomTextDefinitionValues(array $fields): array
    {
        // set rendering
        $rendering = 'input';
        if (array_key_exists('rendering', $fields)) {
            $rendering = $fields['rendering'];
        }

        return [
            'text' => [
                [
                    'minLength' => $this->convertToInt($fields['min-length']),
                    'maxLength' => $this->convertToInt($fields['max-length'])
                ]
            ],
            'rendering' => $rendering
        ];
    }

    /**
     * @param array $fields
     * @return array
     * @throws Exception
     */
    private function mapPricePerUnitDefinitionValues(array $fields): array
    {
        // set element value refs
        $elementValueRefs = [];
        $referenceValues = $this->getMultipleFieldValues($fields, 'reference-value_', '|');
        foreach ($referenceValues as $referenceValue) {
            // if not all required fields are set continue
            if (!is_array($referenceValue) || count($referenceValue) < 4) {
                continue;
            }

            // get section id and continue if no section id was found
            $sectionId = $this->getSectionId(new Identifier($referenceValue[0]));
            if (null === $sectionId) {
                continue;
            }

            // get section id and continue if no section id was found
            $elementId = $this->getElementId($sectionId, new Identifier($referenceValue[1]));
            if (null === $elementId) {
                continue;
            }

            // add element value ref
            $elementValueRefs[] = [
                'sectionId' => $sectionId->getId(),
                'elementId' => $elementId->getId(),
                'selectableValueType' => $referenceValue[2],
                'selectableValue' => $referenceValue[3]
            ];
        }

        $definitionValues = [
            'elementValueRefs' => $elementValueRefs,
            'conversionFactor' => $this->convertToFloat($fields['conversion-factor'])
        ];

        if (count($elementValueRefs) > 0) {
            $definitionValues = array_merge($definitionValues, [
                /** @deprecated not used anymore when elementValueRefs is set but still must be there */
                'sectionId' => $elementValueRefs[0]['sectionId'],
                'elementId' => $elementValueRefs[0]['elementId'],
                'selectableValueType' => $elementValueRefs[0]['selectableValueType'],
                'selectableValue' => $elementValueRefs[0]['selectableValue']
            ]);
        }

        return $definitionValues;
    }

    /**
     * @param string $locale
     * @param array $fields
     * @return array
     * @throws Exception
     */
    private function mapFloatInputDefinitionValues(string $locale, array $fields): array
    {
        // set conversion factor
        $conversionFactor = 1;
        if (array_key_exists('conversion-factor', $fields)) {
            $conversionFactor = $this->convertToFloat($fields['conversion-factor']);
        }

        // set prefix
        $prefix = [$locale => ''];
        if (array_key_exists('prefix-length', $fields)) {
            $prefix = [$locale => $fields['prefix-length']];
        }

        // set suffix
        $suffix = [$locale => ''];
        if (array_key_exists('suffix-length', $fields)) {
            $suffix = [$locale => $fields['suffix-length']];
        }

        // set element value refs
        $elementValueRefs = [];
        $referenceValues = $this->getMultipleFieldValues($fields, 'reference-value_');
        foreach ($referenceValues as $referenceValue) {
            $referenceValue = explode('|', $referenceValue);

            // if not all required fields are set continue
            if (!is_array($referenceValue) || count($referenceValue) < 5) {
                continue;
            }

            // get section id and continue if no section id was found
            $sectionId = $this->getSectionId(new Identifier($referenceValue[0]));
            if (null === $sectionId) {
                continue;
            }

            // get section id and continue if no section id was found
            $elementId = $this->getElementId($sectionId, new Identifier($referenceValue[1]));
            if (null === $elementId) {
                continue;
            }

            // set default for optional field formula
            if (count($referenceValue) < 6) {
                $referenceValue[5] = '';
            }

            // add element value ref
            $elementValueRefs[] = [
                'sectionId' => $sectionId->getId(),
                'elementId' => $elementId->getId(),
                'selectableValueType' => $referenceValue[2],
                'selectableValue' => $referenceValue[3],
                'compareValueType' => $referenceValue[4],
                'compareValueFormula' => $referenceValue[5]
            ];
        }

        return [
            'value' => [
                [
                    'minimum' => $fields['min-length'],
                    'maximum' => $fields['max-length'],
                    'step' => $fields['step-length']
                ]
            ],
            'conversionFactor' => $conversionFactor,
            'prefix' => $prefix,
            'suffix' => $suffix,
            'elementValueRefs' => $elementValueRefs
        ];
    }

    /**
     * @param array $fields
     * @throws InvalidUuidException
     * @throws AptoPriceDuplicateException
     */
    private function processPricePerUnitPrices(array $fields)
    {
        // find or create a PricePerUnitItem
        $pricePerUnitItem = $this->pricePerUnitItemRepository->findByElementId($this->elementId->getId());
        if (null === $pricePerUnitItem) {
            $pricePerUnitItem = new PricePerUnitItem(
                $this->pricePerUnitItemRepository->nextIdentity(),
                $this->product->getId(),
                $this->sectionId,
                $this->elementId
            );
            $this->pricePerUnitItemRepository->add($pricePerUnitItem);
        }

        // add prices
        $pricePerUnitItem->clearAptoPrices();
        $definitionPrices = $this->getMultipleFieldValues($fields, 'definition-price_', '|');
        foreach ($definitionPrices as $definitionPrice) {
            $price = $this->getAptoPriceFromPriceField($definitionPrice);

            // if not all required fields are set continue
            if (null === $price) {
                continue;
            }

            // add price
            $pricePerUnitItem->addAptoPrice($price[0], $price[1]);
        }

        $pricePerUnitItem->publishEvents();
    }

    /**
     * @param string $locale
     * @param array $fields
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    private function processSelectBoxItems(string $locale, array $fields)
    {
        $name = $this->getTranslatedValue([$locale => $fields['option-name']]);
        $selectBoxItems = $this->selectBoxItemRepository->findByElementId($this->elementId->getId());

        // return if item exists
        /** @var SelectBoxItem $selectBoxItem */
        $selectBoxItem = null;
        $isNewSelectBoxItem = true;
        foreach ($selectBoxItems as $selectBoxItemSearch) {
            if ($selectBoxItemSearch->getName()->equals($name)) {
                $selectBoxItem = $selectBoxItemSearch;
                $isNewSelectBoxItem = false;
            }
        }

        // add new select box item if no existing was found
        if (null === $selectBoxItem) {
            $selectBoxItem = new SelectBoxItem(
                $this->selectBoxItemRepository->nextIdentity(),
                $this->product->getId(),
                $this->sectionId,
                $this->elementId,
                $this->getTranslatedValue([$locale => $fields['option-name']])
            );
        }

        // set default
        // @todo not supported because to add a default element we need the item id before we can call this function, at the moment we need the default element as static values in element definition
        //if (array_key_exists('option-default', $fields)) {
            //$selectBoxItem->setIsDefault($this->convertToBool($fields['option-default']));
        //}

        // add select box item prices
        $prices = $this->getMultipleFieldValues($fields, 'option-price_', '|');
        if (count($prices) > 0) {
            $selectBoxItem->clearAptoPrices();

            foreach ($prices as $price) {
                $aptoPrice = $this->getAptoPriceFromPriceField($price);

                // if not all required fields are set continue
                if (null === $aptoPrice) {
                    continue;
                }

                // add price
                $selectBoxItem->addAptoPrice($aptoPrice[0], $aptoPrice[1]);
            }
        }

        // add or update select box item in repository
        if (true === $isNewSelectBoxItem) {
            $this->selectBoxItemRepository->add($selectBoxItem);
        } else {
            $this->selectBoxItemRepository->update($selectBoxItem);
        }

        $selectBoxItem->publishEvents();
    }

    /**
     * @param string $locale
     * @param string $domain
     * @param array $fields
     * @return Product
     * @throws Exception
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    private function getProduct(string $locale, string $domain, array $fields): Product
    {
        $identifier = new Identifier($fields['product-identifier']);
        $product = $this->productRepository->findByIdentifier($identifier);

        if (null === $product) {
            $product = $this->addProduct($locale, $domain, $fields);
        }

        return $product;
    }

    /**
     * @param string $locale
     * @param string $domain
     * @param array $fields
     * @return Product
     * @throws Exception
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     * @throws ProductShopCountException
     * @throws ProductTaxRateException
     * @throws ProductWeightException
     */
    private function addProduct(string $locale, string $domain, array $fields): Product
    {
        $shops = new ArrayCollection([$this->shopRepository->findOneByDomain($domain)]);
        $identifier = new Identifier($fields['product-identifier']);

        // set product name
        $name = $identifier->getValue();
        if (array_key_exists('product-name', $fields)) {
            $name = $fields['product-name'];
        }

        // set product url
        $url = $identifier->getValue();
        if (array_key_exists('product-url', $fields) && trim($fields['product-url'])) {
            $url = $fields['product-url'];
        }

        $product = new Product(
            $this->productRepository->nextIdentity(),
            $identifier,
            $this->getTranslatedValue([$locale => $name]),
            $shops
        );

        // set default values for required fields
        $product
            ->setActive(true)
            ->setHidden(false)
            ->setUseStepByStep(true)
            ->setPosition(0)
            ->setStock(0)
            ->setMinPurchase(0)
            ->setMaxPurchase(0)
            ->setDeliveryTime('')
            ->setWeight(0.0)
            ->setTaxRate(19)
            ->setPriceCalculatorId(SimplePriceCalculator::class)
            ->setSeoUrl($url)
        ;

        $this->productRepository->add($product);
        return $product;
    }

    /**
     * @param Identifier $identifier
     * @param string $locale
     * @return void
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     */
    private function addSection(Identifier $identifier, string $locale): void
    {
        $this->product->addSection(
            $identifier, AptoTranslatedValue::fromArray([$locale => $identifier->getValue()]), true
        );
    }

    /**
     * @param Identifier $identifier
     * @param string $locale
     * @return void
     * @throws IdentifierUniqueException
     */
    private function addElement(Identifier $identifier, string $locale): void
    {
        $this->product->addElement(
            $this->sectionId, $identifier, null, AptoTranslatedValue::fromArray([$locale => $identifier->getValue()]), true, false
        );
    }

    /**
     * @param Identifier $identifier
     * @return AptoUuid|null
     */
    protected function getSectionId(Identifier $identifier): ?AptoUuid
    {
        $sectionIds = $this->product->getSectionIds();

        foreach ($sectionIds as $sectionId) {
            $sectionIdentifier = $this->product->getSectionIdentifier($sectionId);

            if ($identifier->equals($sectionIdentifier)) {
                return $sectionId;
            }
        }

        return null;
    }

    /**
     * @param AptoUuid $sectionId
     * @param Identifier $identifier
     * @return AptoUuid|null
     */
    protected function getElementId(AptoUuid $sectionId, Identifier $identifier): ?AptoUuid
    {
        $elementIds = $this->product->getElementIds($sectionId);

        foreach ($elementIds as $elementId) {
            $elementIdentifier = $this->product->getElementIdentifier($sectionId, $elementId);

            if ($identifier->equals($elementIdentifier)) {
                return $elementId;
            }
        }

        return null;
    }

    /**
     * @param array $priceField
     * @return array|null
     */
    protected function getAptoPriceFromPriceField(array $priceField): ?array
    {
        // if not all required fields are set continue
        if (!is_array($priceField) || count($priceField) < 3) {
            return null;
        }

        // find customer group
        $customerGroup = $this->customerGroupRepository->findOneByName($priceField[1]);
        if (null === $customerGroup) {
            return null;
        }

        // return price
        return [
            new Money(
                $this->convertToInt($priceField[0]),
                new Currency(strtoupper($priceField[2]))
            ),
            $customerGroup->getId()
        ];
    }

    /**
     * @param array $priceField
     * @param string $locale
     * @return array|null
     */
    protected function getAptoDiscountFromPriceField(array $priceField, string $locale): ?array
    {
        // if not all required fields are set continue
        if (!is_array($priceField) || count($priceField) < 3) {
            return null;
        }

        // find customer group
        $customerGroup = $this->customerGroupRepository->findOneByName($priceField[2]);
        if (null === $customerGroup) {
            return null;
        }

        // return price
        return [
            $this->convertToFloat($priceField[0]),
            $customerGroup->getId(),
            $this->getTranslatedValue([$locale => $priceField[1]])
        ];
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function sanitizeFilename(string $filename): string
    {
        // sanitize file name
        $directory = dirname($filename);
        $fileName = $this->sanitizer->sanitizeFilename(basename($filename));
        $file = new File(new Directory($directory), $fileName);
        return $file->getPath();
    }

    /**
     * @param string $value
     * @return int
     */
    protected function convertToInt(string $value): int
    {
        return (int) $value;
    }

    /**
     * @param string $value
     * @return float
     */
    protected function convertToFloat(string $value): float
    {
        return (float) str_replace(',', '.', $value);
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function convertToBool(string $value): bool
    {
        switch (strtoupper($value)) {
            case 'WAHR':
            case 'TRUE':
            case 'JA':
            case 'YES':
            case 'X': {
                return true;
            }
        }
        return false;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield ImportDefaultDataType::class => [
            'method' => 'handleImportDefaultDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportMaterialPickerDataType::class => [
            'method' => 'handleImportMaterialPickerDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportImageUploadDataType::class => [
            'method' => 'handleImportImageUploadDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportAreaElementDataType::class => [
            'method' => 'handleImportAreaElementDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportWidthHeightDataType::class => [
            'method' => 'handleImportWidthHeightDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportCustomTextDataType::class => [
            'method' => 'handleImportCustomTextDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportPricePerUnitDataType::class => [
            'method' => 'handleImportPricePerUnitDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportFloatInputDataType::class => [
            'method' => 'handleImportFloatInputDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportSelectBoxDataType::class => [
            'method' => 'handleImportSelectBoxDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportProductComputedValueDataType::class => [
            'method' => 'handleImportProductComputedValueDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];
    }
}
