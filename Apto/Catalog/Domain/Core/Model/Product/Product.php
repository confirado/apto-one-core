<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\AptoDiscount\AptoDiscountDuplicateException;
use Apto\Base\Domain\Core\Model\AptoDiscount\AptoDiscounts;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPrices;
use Apto\Base\Domain\Core\Model\AptoPriceFormula\AptoPriceFormulaDuplicateException;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Catalog\Domain\Core\Model\Group\Group;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrix;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Condition\Condition;
use Apto\Catalog\Domain\Core\Model\Product\Condition\ConditionSet;
use Apto\Catalog\Domain\Core\Model\Product\Condition\Criterion;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidOperatorException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidPropertyException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidTypeException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidValueException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionOperator;
use Apto\Catalog\Domain\Core\Model\Product\Element\Element;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefaultUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionCopyAware;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionSelectableValuesUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementExtendedPriceCalculationActiveUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementExtendedPriceCalculationFormulaUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementIsActiveUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementIsMandatoryUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementIsNotAvailableUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementIsZoomableUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementOpenLinksInDialogUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementPercentageSurchargeUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementPositionUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementPriceMatrixUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementZoomFunctionUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Element\RenderImage;
use Apto\Catalog\Domain\Core\Model\Product\Element\RenderImageOptions;
use Apto\Catalog\Domain\Core\Model\Product\Element\ZoomFunction;
use Apto\Catalog\Domain\Core\Model\Product\Rule\Rule;
use Apto\Catalog\Domain\Core\Model\Product\Rule\RuleCriterion;
use Apto\Catalog\Domain\Core\Model\Product\Section\Section;
use Apto\Catalog\Domain\Core\Model\Product\Section\SectionAllowMultipleUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Section\SectionIsActiveUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Section\SectionIsHiddenUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Section\SectionIsMandatoryUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Section\SectionIsZoomableUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Section\SectionPositionUpdated;
use Apto\Catalog\Domain\Core\Model\Product\Section\SectionRepeatableUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Money\Currency;
use Money\Money;


class Product extends AptoAggregate
{
    use AptoCustomProperties;
    use AptoPrices;
    use AptoDiscounts;

    /**
     * @var Identifier
     */
    protected $identifier;

    /**
     * @var string|null
     */
    protected $seoUrl;

    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var AptoTranslatedValue
     */
    protected $description;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var bool
     */
    protected $hidden;

    /**
     * @var bool
     */
    protected $useStepByStep;

    /**
     * Reset configurator steps when going backwards in configurator or not
     *
     * @var bool
     */
    protected $keepSectionOrder;

    /**
     * @var string|null
     */
    protected $articleNumber;

    /**
     * @var AptoTranslatedValue
     */
    protected $metaTitle;

    /**
     * @var AptoTranslatedValue
     */
    protected $metaDescription;

    /**
     * @var int
     */
    protected $stock;

    /**
     * @var int
     */
    protected $minPurchase;

    /**
     * @var int
     */
    protected $maxPurchase;

    /**
     * @var string
     */
    protected $deliveryTime;

    /**
     * @var float
     */
    protected $weight;

    /**
     * @todo make tax decimal field
     * @var float
     */
    protected $taxRate;

    /**
     * @var Collection
     */
    protected $categories;

    /**
     * @var Collection
     */
    protected $shops;

    /**
     * @var Collection
     */
    protected $sections;

    /**
     * @var Collection
     */
    protected $rules;

    /**
     * @var string
     */
    protected $priceCalculatorId;

    /**
     * @var MediaFile|null
     */
    protected $previewImage;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var Collection
     */
    protected $filterProperties;

    /**
     * @var Collection
     */
    protected $computedProductValues;

    /**
     * @var Collection
     */
    protected $domainProperties;

    /**
     * @var Collection
     */
    protected $conditions;

    /**
     * @var Collection
     */
    protected $conditionSets;

    /**
     * Product constructor.
     * @param AptoUuid $id
     * @param Identifier $identifier
     * @param AptoTranslatedValue $name
     * @param Collection $shops
     * @throws ProductShopCountException
     */
    public function __construct(AptoUuid $id, Identifier $identifier, AptoTranslatedValue $name, Collection $shops)
    {
        parent::__construct($id);
        $this->publish(
            new ProductAdded(
                $this->getId()
            )
        );
        $this->categories = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->rules = new ArrayCollection();
        $this->customProperties = new ArrayCollection();
        $this->aptoPrices = new ArrayCollection();
        $this->aptoDiscounts = new ArrayCollection();
        $this->computedProductValues = new ArrayCollection();
        $this->priceCalculatorId = '';
        $this
            ->setIdentifier($identifier)
            ->setName($name)
            ->setShops($shops);

        $this->previewImage = null;
        $this->filterProperties = new ArrayCollection();
        $this->domainProperties = new ArrayCollection();
        $this->conditions = new ArrayCollection();
        $this->conditionSets = new ArrayCollection();
        $this->keepSectionOrder = true;
    }

    /**
     * @return Identifier
     */
    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @param Identifier $identifier
     * @return Product
     */
    public function setIdentifier(Identifier $identifier): Product
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeoUrl(): string
    {
        if (null === $this->seoUrl) {
            return '';
        }

        return $this->seoUrl;
    }

    /**
     * @param string $seoUrl
     * @return Product
     */
    public function setSeoUrl(string $seoUrl)
    {
        if ('' === trim($seoUrl)) {
            $this->seoUrl = null;
        } else {
            $this->seoUrl = $seoUrl;
        }

        return $this;
    }

    /**
     * @return AptoTranslatedValue|null
     */
    public function getName(): ?AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @param AptoTranslatedValue $name
     * @return Product
     */
    public function setName(AptoTranslatedValue $name): Product
    {
        if (null !== $this->name && $this->name->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new ProductNameUpdated(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @return AptoTranslatedValue|null
     */
    public function getDescription(): ?AptoTranslatedValue
    {
        return $this->description;
    }

    /**
     * @param AptoTranslatedValue $description
     * @return Product
     */
    public function setDescription(AptoTranslatedValue $description): Product
    {
        if (null !== $this->description && $this->description->equals($description)) {
            return $this;
        }
        $this->description = $description;
        $this->publish(
            new ProductDescriptionUpdated(
                $this->getId(),
                $this->getDescription()
            )
        );
        return $this;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function setActive(bool $active)
    {
        if ($this->active === $active) {
            return $this;
        }
        $this->active = $active;
        $this->publish(
            new ProductActiveUpdated(
                $this->getId(),
                $this->getActive()
            )
        );
        return $this;
    }

    /**
     * @return bool
     */
    public function getHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     * @return Product
     */
    public function setHidden(bool $hidden): Product
    {
        if ($this->hidden === $hidden) {
            return $this;
        }
        $this->hidden = $hidden;
        $this->publish(
            new ProductHiddenUpdated(
                $this->getId(),
                $this->getHidden()
            )
        );
        return $this;
    }

    /**
     * @return bool
     */
    public function getUseStepByStep(): bool
    {
        return $this->useStepByStep;
    }

    /**
     * @param bool $useStepByStep
     * @return Product
     */
    public function setUseStepByStep(bool $useStepByStep): Product
    {
        if ($this->useStepByStep === $useStepByStep) {
            return $this;
        }
        $this->useStepByStep = $useStepByStep;
        $this->publish(
            new ProductUseStepByStepUpdated(
                $this->getId(),
                $this->getUseStepByStep()
            )
        );
        return $this;
    }

    /**
     * @return bool
     */
    public function getKeepSectionOrder(): bool
    {
        return $this->keepSectionOrder;
    }

    /**
     * @param bool $keepSectionOrder
     * @return Product
     */
    public function setKeepSectionOrder(bool $keepSectionOrder): Product
    {
        if ($this->keepSectionOrder === $keepSectionOrder) {
            return $this;
        }
        $this->keepSectionOrder = $keepSectionOrder;
        $this->publish(
            new ProductKeepSectionOrderUpdated(
                $this->getId(),
                $this->getKeepSectionOrder()
            )
        );
        return $this;
    }


    /**
     * @return string
     */
    public function getArticleNumber(): string
    {
        if ($this->articleNumber === null) {
            return '';
        }

        return $this->articleNumber;
    }

    /**
     * @param string $articleNumber
     * @return Product
     */
    public function setArticleNumber(string $articleNumber): Product
    {
        if ($articleNumber == '') {
            $articleNumber = null;
        }

        if ($this->articleNumber === $articleNumber) {
            return $this;
        }

        $this->articleNumber = $articleNumber;
        $this->publish(
            new ProductArticleNumberUpdated(
                $this->getId(),
                $this->getArticleNumber()
            )
        );
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param AptoTranslatedValue $metaTitle
     * @return Product
     */
    public function setMetaTitle(AptoTranslatedValue $metaTitle): Product
    {
        if (null !== $this->metaTitle && $this->metaTitle->equals($metaTitle)) {
            return $this;
        }
        $this->metaTitle = $metaTitle;
        $this->publish(
            new ProductMetaTitleUpdated(
                $this->getId(),
                $this->getMetaTitle()
            )
        );
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param AptoTranslatedValue $metaDescription
     * @return Product
     */
    public function setMetaDescription(AptoTranslatedValue $metaDescription): Product
    {
        if (null !== $this->metaDescription && $this->metaDescription->equals($metaDescription)) {
            return $this;
        }
        $this->metaDescription = $metaDescription;
        $this->publish(
            new ProductMetaDescriptionUpdated(
                $this->getId(),
                $this->getMetaDescription()
            )
        );
        return $this;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     * @return Product
     */
    public function setStock(int $stock): Product
    {
        if ($this->stock === $stock) {
            return $this;
        }
        $this->stock = $stock;
        $this->publish(
            new ProductStockUpdated(
                $this->getId(),
                $this->getStock()
            )
        );
        return $this;
    }

    /**
     * @return int
     */
    public function getMinPurchase(): int
    {
        if (null === $this->minPurchase) {
            return 0;
        }
        return $this->minPurchase;
    }

    /**
     * @param int $minPurchase
     * @return Product
     *
     * @return $this
     * @throws ProductMinPurchaseException
     */
    public function setMinPurchase(int $minPurchase): Product
    {
        if ($this->minPurchase === $minPurchase) {
            return $this;
        }

        $this->minPurchase = $minPurchase;

        if ($this->minPurchase > $this->maxPurchase) {
            throw new ProductMinPurchaseException('Product "Mindestabnahme('.$this->minPurchase.')" can not be bigger then "Maximalabnahme('.$this->maxPurchase.')"');
        }

        $this->publish(
            new ProductMinPurchaseUpdated(
                $this->getId(),
                $this->getMinPurchase()
            )
        );
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxPurchase(): int
    {
        if (null === $this->maxPurchase) {
            return 0;
        }
        return $this->maxPurchase;
    }

    /**
     * @param int $maxPurchase
     * @return Product
     *
     * @return $this
     * @throws ProductMinPurchaseException
     */
    public function setMaxPurchase(int $maxPurchase): Product
    {
        if ($this->maxPurchase === $maxPurchase) {
            return $this;
        }

        $this->maxPurchase = $maxPurchase;

        if ($this->maxPurchase < $this->minPurchase) {
            throw new ProductMinPurchaseException('Product "Mindestabnahme('.$this->minPurchase.')" can not be bigger then "Maximalabnahme('.$this->maxPurchase.')"');
        }

        $this->publish(
            new ProductMaxPurchaseUpdated(
                $this->getId(),
                $this->getMaxPurchase()
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    /**
     * @param string $deliveryTime
     * @return Product
     */
    public function setDeliveryTime(string $deliveryTime): Product
    {
        $this->deliveryTime = $deliveryTime;
        $this->publish(
            new ProductDeliveryTimeUpdated(
                $this->getId(),
                $this->getDeliveryTime()
            )
        );
        return $this;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     * @return Product
     * @throws ProductWeightException
     */
    public function setWeight(float $weight): Product
    {
        // we're not selling helium, antimatter etc., aren't we?!
        if ($weight < 0) {
            throw new ProductWeightException('A weight cannot be negative, zero or larger value expected.');
        }

        if ($this->weight === $weight) {
            return $this;
        }
        $this->weight = $weight;
        $this->publish(
            new ProductWeightUpdated(
                $this->getId(),
                $this->getWeight()
            )
        );
        return $this;
    }


    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @param float $taxRate
     * @return Product
     * @throws ProductTaxRateException
     */
    public function setTaxRate(float $taxRate): Product
    {
        if ($taxRate < 0) {
            throw new ProductTaxRateException('A tax rate cannot be negative, zero or larger value expected.');
        }

        if ($this->taxRate === $taxRate) {
            return $this;
        }
        $this->taxRate = $taxRate;
        $this->publish(
            new ProductTaxRateUpdated(
                $this->getId(),
                $this->getTaxRate()
            )
        );
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection $categories
     * @return Product
     */
    public function setCategories(Collection $categories): Product
    {
        if ($this->categories !== null && !$this->hasCollectionChanged($this->getCategories(), $categories)) {
            return $this;
        }
        $this->categories = $categories;
        $this->publish(
            new ProductCategoriesUpdated(
                $this->getId(),
                $this->getCollectionIds($this->getCategories())
            )
        );
        return $this;
    }

    /**
     * @return Collection
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    /**
     * @param Collection $shops
     * @return Product
     * @throws ProductShopCountException
     */
    public function setShops(Collection $shops): Product
    {
        if ($shops->count() < 1) {
            throw new ProductShopCountException('Product must have at least one shop.');
        }

        $this->shops = $shops;

        $this->publish(
            new ProductShopsUpdated(
                $this->getId(),
                $this->getCollectionIds($this->getShops())
            )
        );
        return $this;
    }

    /**
     * @return array
     */
    public function getSingleOnlySectionIds(): array
    {
        $ids = [];

        /** @var Section $section */
        foreach ($this->sections as $section) {
            if (!$section->getAllowMultiple()) {
                $ids[] = $section->getId()->getId();
            }
        }

        return $ids;
    }

    /**
     * @param Identifier $identifier
     * @return bool
     */
    public function sectionIdentifierExists(Identifier $identifier): bool
    {
        /** @var Section $section */
        foreach ($this->sections as $section) {
            if ($section->getIdentifier()->equals($identifier)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param AptoUuid $sectionId
     * @param Identifier $identifier
     * @return bool
     */
    public function elementIdentifierExists(AptoUuid $sectionId, Identifier $identifier): bool
    {
        $section = $this->getSection($sectionId);
        if (null === $section) {
            throw new \InvalidArgumentException('Section not found.');
        }

        return $section->elementIdentifierExists($identifier);
    }

    /**
     * @param Identifier $identifier
     * @param AptoTranslatedValue|null $sectionName
     * @param bool $active
     * @return Product
     * @throws IdentifierUniqueException
     * @throws InvalidUuidException
     */
    public function addSection(Identifier $identifier, AptoTranslatedValue $sectionName = null, bool $active = false, int $position = 0, bool $addDefaultElement = false): Product
    {
        if ($this->sectionIdentifierExists($identifier)) {
            throw new IdentifierUniqueException('Section Identifier must be unique within a collection!');
        }

        $sectionId = $this->nextSectionId();
        $this->sections->set(
            $sectionId->getId(),
            new Section($sectionId, $this, $identifier)
        );

        if (null !== $sectionName) {
            $this->setSectionName($sectionId, $sectionName);
        }

        $this->setSectionIsActive($sectionId, $active);
        $this->setSectionPosition($sectionId, $position);

        if ($addDefaultElement === true) {
            $this->addElement(
                $sectionId,
                $identifier,
                null,
                $sectionName,
                true,
                false,
                10
            );
        }
        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param Money $aptoPrice
     * @param AptoUuid $customerGroupId
     * @param AptoUuid|null $productConditionId
     * @return Product
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function addSectionPrice(AptoUuid $sectionId, Money $aptoPrice, AptoUuid $customerGroupId, ?AptoUuid $productConditionId = null): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // add new price to section
        $section->addAptoPrice($aptoPrice, $customerGroupId, $productConditionId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return Money|null
     */
    public function getSectionPrice(AptoUuid $sectionId, Currency $currency, AptoUuid $customerGroupId)
    {
        // if element does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return null;
        }

        return $section->getAptoPrice($currency, $customerGroupId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $priceId
     * @return Product
     */
    public function removeSectionPrice(AptoUuid $sectionId, AptoUuid $priceId): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // remove price from section
        $section->removeAptoPrice($priceId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @return $this
     */
    public function clearSectionPrices(AptoUuid $sectionId): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->clearAptoPrices();
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param float $discount
     * @param AptoUuid $customerGroupId
     * @param AptoTranslatedValue $name
     * @return Product
     * @throws AptoDiscountDuplicateException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function addSectionDiscount(AptoUuid $sectionId, float $discount, AptoUuid $customerGroupId, AptoTranslatedValue $name): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // add new price to section
        $section->addAptoDiscount($discount, $customerGroupId, $name);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $discountId
     * @param float $discount
     * @return Product
     */
    public function setSectionDiscountValue(AptoUuid $sectionId, AptoUuid $discountId, float $discount): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        $section->setAptoDiscountValue($discountId, $discount);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $discountId
     * @param AptoUuid $customerGroupId
     * @return Product
     * @throws AptoDiscountDuplicateException
     */
    public function setSectionDiscountCustomerGroup(AptoUuid $sectionId, AptoUuid $discountId, AptoUuid $customerGroupId): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        $section->setAptoDiscountCustomerGroupId($discountId, $customerGroupId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $discountId
     * @param AptoTranslatedValue $name
     * @return Product
     */
    public function setSectionDiscountName(AptoUuid $sectionId, AptoUuid $discountId, AptoTranslatedValue $name): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        $section->setAptoDiscountName($discountId, $name);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $discountId
     * @return AptoTranslatedValue|null
     */
    public function getSectionDiscountName(AptoUuid $sectionId, AptoUuid $discountId): ?AptoTranslatedValue
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return null;
        }

        return $section->getAptoDiscountName($discountId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $discountId
     * @param AptoTranslatedValue $description
     * @return Product
     */
    public function setSectionDiscountDescription(AptoUuid $sectionId, AptoUuid $discountId, AptoTranslatedValue $description): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        $section->setAptoDiscountDescription($discountId, $description);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $discountId
     * @return Product
     */
    public function removeSectionDiscount(AptoUuid $sectionId, AptoUuid $discountId): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // remove discount from section
        $section->removeAptoDiscount($discountId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @return $this
     */
    public function clearSectionDiscounts(AptoUuid $sectionId): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->clearAptoDiscounts();
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param Identifier $newIdentifier
     * @throws IdentifierUniqueException
     * @return Product
     */
    public function setSectionIdentifier(AptoUuid $sectionId, Identifier $newIdentifier): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {

            if (!$section->getIdentifier()->equals($newIdentifier) && $this->sectionIdentifierExists($newIdentifier)) {
                throw new IdentifierUniqueException('Section Identifier must be unique within a collection!');
            }

            $section->setIdentifier($newIdentifier);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param int $position
     * @return Product
     */
    public function setSectionPosition(AptoUuid $sectionId, int $position): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            if ($section->getPosition() === $position) {
                return $this;
            }
            $section->setPosition($position);
            $this->publish(
                new SectionPositionUpdated(
                    $this->getId(),
                    $section->getId(),
                    $section->getPosition()
                )
            );
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoTranslatedValue $name
     * @return Product
     */
    public function setSectionName(AptoUuid $sectionId, AptoTranslatedValue $name): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->setName($name);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoTranslatedValue $description
     * @return Product
     */
    public function setSectionDescription(AptoUuid $sectionId, AptoTranslatedValue $description): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->setDescription($description);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param bool $isActive
     * @return Product
     */
    public function setSectionIsActive(AptoUuid $sectionId, bool $isActive): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // if the section we want to set isActive is already in the same state as $isActive we have nothing to do
        if ($section->getIsActive() === $isActive) {
            return $this;
        }

        // update section isActive flag
        $section->setIsActive($isActive);
        $this->publish(
            new SectionIsActiveUpdated($section->getId(), $isActive)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @return bool
     */
    public function getSectionIsHidden(AptoUuid $sectionId): bool
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return true;
        }

        return $section->getIsHidden();
    }

    /**
     * @param AptoUuid $sectionId
     * @param bool $isHidden
     * @return Product
     */
    public function setSectionIsHidden(AptoUuid $sectionId, bool $isHidden): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // if the section we want to set isHidden is already in the same state as $isHidden we have nothing to do
        if ($section->getIsHidden() === $isHidden) {
            return $this;
        }

        // update section IsHidden flag
        $section->setIsHidden($isHidden);
        $this->publish(
            new SectionIsHiddenUpdated($section->getId(), $isHidden)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param bool $isMandatory
     * @return Product
     */
    public function setSectionIsMandatory(AptoUuid $sectionId, bool $isMandatory): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // if the section we want to set isMandatory is already in the same state as $isMandatory we have nothing to do
        if ($section->getIsMandatory() === $isMandatory) {
            return $this;
        }

        // update section isMandatory flag
        $section->setIsMandatory($isMandatory);
        $this->publish(
            new SectionIsMandatoryUpdated($section->getId(), $isMandatory)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param bool $isZoomable
     * @return Product
     */
    public function setSectionIsZoomable(AptoUuid $sectionId, bool $isZoomable): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // if the section we want to set isZoomable is already in the same state as $isZoomable we have nothing to do
        if ($section->getIsZoomable() === $isZoomable) {
            return $this;
        }

        // update section isZoomable flag
        $section->setIsZoomable($isZoomable);
        $this->publish(
            new SectionIsZoomableUpdated($section->getId(), $isZoomable)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param bool $allowMultiple
     * @return Product
     */
    public function setSectionAllowMultiple(AptoUuid $sectionId, bool $allowMultiple): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // if the section we want to set allowMultiple is already in the same state as $allowMultiple we have nothing to do
        if ($section->getAllowMultiple() === $allowMultiple) {
            return $this;
        }

        // update section allowMultiple flag
        $section->setAllowMultiple($allowMultiple);
        $this->publish(
            new SectionAllowMultipleUpdated($section->getId(), $allowMultiple)
        );

        return $this;
    }

    /**
     * @param AptoUuid   $sectionId
     * @param Repeatable $repeatable
     *
     * @return $this
     */
    public function setSectionRepeatable(AptoUuid $sectionId, Repeatable $repeatable): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // if value is the same skip
        if ($section->getRepeatable()->equals($repeatable)) {
            return $this;
        }

        // update section type flag
        $section->setRepeatable($repeatable);
        $this->publish(
            new SectionRepeatableUpdated($section->getId(), $repeatable->jsonSerialize())
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param Group $group
     * @return Product
     */
    public function setSectionGroup(AptoUuid $sectionId, Group $group = null): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->setGroup($group);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param MediaFile $previewImage
     * @return Product
     */
    public function setSectionPreviewImage(AptoUuid $sectionId,  MediaFile $previewImage): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->setPreviewImage($previewImage);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @return Product
     */
    public function removeSectionPreviewImage(AptoUuid $sectionId): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->removeSectionPreviewImage();
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @return Product
     */
    public function removeSection(AptoUuid $sectionId): Product
    {
        if ($this->hasSection($sectionId)) {
            $this->sections->remove($sectionId->getId());
            $this->publish(
                new ProductSectionRemoved($sectionId)
            );
        }
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasSection(AptoUuid $id): bool
    {
        return $this->sections->containsKey($id->getId());
    }

    /**
     * @param AptoUuid $id
     * @return Section|null
     */
    private function getSection(AptoUuid $id)
    {
        if ($this->hasSection($id)) {
            return $this->sections->get($id->getId());
        }

        return null;
    }

    /**
     * @param AptoUuid $sectionId
     * @return Identifier|null
     */
    public function getSectionIdentifier(AptoUuid $sectionId)
    {
        $section = $this->getSection($sectionId);

        if (null === $section) {
            return null;
        }

        return $section->getIdentifier();
    }

    /**
     * @param AptoUuid $sectionId
     * @return AptoTranslatedValue|null
     */
    public function getSectionName(AptoUuid $sectionId)
    {
        $section = $this->getSection($sectionId);

        if (null === $section) {
            return null;
        }

        return $section->getName();
    }

    /**
     * @param AptoUuid $sectionId
     * @return AptoTranslatedValue|null
     */
    public function getSectionDescription(AptoUuid $sectionId)
    {
        $section = $this->getSection($sectionId);

        if (null === $section) {
            return null;
        }

        return $section->getDescription();
    }

    /**
     * @param AptoUuid $sectionId
     * @return int|null
     */
    public function getSectionPosition(AptoUuid $sectionId)
    {
        $section = $this->getSection($sectionId);

        if (null === $section) {
            return null;
        }

        return $section->getPosition();
    }

    /**
     * @param AptoUuid $sectionId
     * @return Repeatable|null
     */
    public function getSectionRepeatable(AptoUuid $sectionId): ?Repeatable
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return null;
        }

        return $section->getRepeatable();
    }

    /**
     * @param Identifier $identifier
     * @return AptoUuid|null
     */
    public function getSectionIdByIdentifier(Identifier $identifier)
    {
        /** @var Section $section */
        foreach ($this->sections as $section) {
            if ($section->getIdentifier()->equals($identifier)) {
                return $section->getId();
            }
        }
        return null;
    }

    /**
     * @return AptoUuid
     * @throws InvalidUuidException
     */
    private function nextSectionId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @param AptoUuid $sectionId
     * @param Identifier $elementIdentifier
     * @param ElementDefinition|null $elementDefinition
     * @param AptoTranslatedValue|null $elementName
     * @param bool $isActive
     * @param bool $isMandatory
     * @param int $position
     * @return $this
     * @throws IdentifierUniqueException
     */
    public function addElement(AptoUuid $sectionId, Identifier $elementIdentifier, ElementDefinition $elementDefinition = null, AptoTranslatedValue $elementName = null, bool $isActive = false, bool $isMandatory = false, int $position = 0): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->addElement($elementIdentifier, $elementDefinition, $elementName, $isActive, $isMandatory, $position);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param int $layer
     * @param string $perspective
     * @param MediaFile $mediaFile
     * @param float $offsetX
     * @param int $offsetUnitX
     * @param float $offsetY
     * @param int $offsetUnitY
     * @param RenderImageOptions $renderImageOptions
     * @return $this
     */
    public function addElementRenderImage(AptoUuid $sectionId, AptoUuid $elementId, int $layer, string $perspective, MediaFile $mediaFile, float $offsetX, int $offsetUnitX, float $offsetY, int $offsetUnitY, RenderImageOptions $renderImageOptions): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->addElementRenderImage($elementId, $layer, $perspective, $mediaFile, $offsetX, $offsetUnitX, $offsetY, $offsetUnitY, $renderImageOptions);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $name
     * @param MediaFile $mediaFile
     * @return $this
     */
    public function addElementAttachment(AptoUuid $sectionId, AptoUuid $elementId, AptoTranslatedValue $name, MediaFile $mediaFile): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
             $section->addElementAttachment($elementId, $name, $mediaFile);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $name
     * @param MediaFile $mediaFile
     * @return $this
     */
    public function addElementGallery(AptoUuid $sectionId, AptoUuid $elementId, AptoTranslatedValue $name, MediaFile $mediaFile): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
             $section->addElementGallery($elementId, $name, $mediaFile);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return bool
     */
    public function hasElement(AptoUuid $sectionId, AptoUuid $elementId): bool
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            return $section->hasElement($elementId);
        }

        return false;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param Identifier $newElementIdentifier
     * @return Product
     * @throws IdentifierUniqueException
     */
    public function setElementIdentifier(AptoUuid $sectionId, AptoUuid $elementId, Identifier $newElementIdentifier): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->setElementIdentifier($elementId, $newElementIdentifier);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param int $position
     * @return Product
     */
    public function setElementPosition(AptoUuid $sectionId, AptoUuid $elementId, int $position): Product
    {
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            if ($element->getPosition() === $position) {
                return $this;
            }

            $element->setPosition($position);
            $this->publish(
                new ElementPositionUpdated(
                    $this->getId(),
                    $sectionId,
                    $element->getId(),
                    $element->getPosition()
                )
            );
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param float $percentageSurcharge
     * @return Product
     */
    public function setElementPercentageSurcharge(AptoUuid $sectionId, AptoUuid $elementId, float $percentageSurcharge): Product
    {
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            if ($element->getPercentageSurcharge() === $percentageSurcharge) {
                return $this;
            }

            $element->setPercentageSurcharge($percentageSurcharge);
            $this->publish(
                new ElementPercentageSurchargeUpdated(
                    $this->getId(),
                    $sectionId,
                    $element->getId(),
                    $element->getPercentageSurcharge()
                )
            );
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $elementName
     * @return Product
     */
    public function setElementName(AptoUuid $sectionId, AptoUuid $elementId, AptoTranslatedValue $elementName): Product
    {
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            $element->setName($elementName);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $elementDescription
     * @return Product
     */
    public function setElementDescription(AptoUuid $sectionId, AptoUuid $elementId, AptoTranslatedValue $elementDescription): Product
    {
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            $element->setDescription($elementDescription);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoTranslatedValue $elementErrorMessage
     * @return Product
     */
    public function setElementErrorMessage(AptoUuid $sectionId, AptoUuid $elementId, AptoTranslatedValue $elementErrorMessage): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->setElementErrorMessage($elementId, $elementErrorMessage);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @return Product
     */
    public function setElementDefinition(AptoUuid $sectionId, AptoUuid $elementId, ElementDefinition $elementDefinition): Product
    {
        $section = $this->getSection($sectionId);
        $oldValues = json_encode($section->getElementSelectableValues($elementId), JSON_UNESCAPED_UNICODE);
        if (null !== $section) {
            $section->setElementDefinition($elementId, $elementDefinition);
        }
        $newValues = json_encode($section->getElementSelectableValues($elementId), JSON_UNESCAPED_UNICODE);
        if ($oldValues !== $newValues) {
            $this->publish(
                new ElementDefinitionSelectableValuesUpdated($elementId, $oldValues, $newValues)
            );
        }
        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return ElementDefinition|null
     */
    public function getElementDefinition(AptoUuid $sectionId, AptoUuid $elementId)
    {
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            return $element->getDefinition();
        }

        return null;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param MediaFile $previewImage
     * @return Product
     */
    public function setElementPreviewImage(AptoUuid $sectionId, AptoUuid $elementId, MediaFile $previewImage): Product
    {
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            $element->setPreviewImage($previewImage);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return Product
     */
    public function removeElementPreviewImage(AptoUuid $sectionId, AptoUuid $elementId): Product
    {
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            $element->removeElementPreviewImage();
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return Product
     */
    public function removeElement(AptoUuid $sectionId, AptoUuid $elementId): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->removeElement($elementId);
            $this->publish(
                new ProductElementRemoved($elementId)
            );
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $renderImageId
     * @return Product
     */
    public function removeElementRenderImage(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $renderImageId): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->removeElementRenderImage($elementId, $renderImageId);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $attachmentId
     * @return $this
     */
    public function removeElementAttachment(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $attachmentId): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->removeElementAttachment($elementId, $attachmentId);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $galleryId
     * @return $this
     */
    public function removeElementGallery(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $galleryId): Product
    {
        $section = $this->getSection($sectionId);

        if (null !== $section) {
            $section->removeElementGallery($elementId, $galleryId);
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param bool $isDefault
     * @return Product
     */
    public function setElementIsDefault(AptoUuid $sectionId, AptoUuid $elementId, bool $isDefault): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // if element does not exists anymore we have nothing to do
        $element = $section->getElement($elementId);
        if (null === $element) {
            return $this;
        }

        // if we want to remove default state its not important if another element is already set to default so we can just set to false or leave it how it is
        if (false === $isDefault) {
            if ($element->getIsDefault() === true) {
                $element->setIsDefault(false);
                $this->publish(
                    new ElementDefaultUpdated($element->getId(), false)
                );
            }
            return $this;
        }

        // if the default element we want to set is already the default element we have nothing to do
        if (true === $element->getIsDefault()) {
            return $this;
        }

        // we need the current default element to set a new element as default
        $defaultElement = $section->getDefaultElement();


        if(!$section->getAllowMultiple()) {
            // reset current default element
            if (null !== $defaultElement) {
                $defaultElement->setIsDefault(false);
                $this->publish(
                    new ElementDefaultUpdated($defaultElement->getId(), false)
                );
            }
        }

        // set new default element
        $element->setIsDefault(true);
        $this->publish(
            new ElementDefaultUpdated($element->getId(), true)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param string $key
     * @param string $value
     * @param bool $translatable
     * @return Product
     * @throws AptoCustomPropertyException
     */
    public function addSectionCustomProperty(AptoUuid $sectionId, string $key, string $value, bool $translatable = false): Product
    {
        // @todo we should use a value object for $key|$value pair
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // add new custom property to section
        $section->setCustomProperty($key, $value, $translatable);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param string $key
     * @return string|null
     */
    public function getSectionCustomProperty(AptoUuid $sectionId, string $key): ?string
    {
        // @todo we should use a value object for $key|$value pair
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return null;
        }

        // return custom property from section
        return $section->getCustomProperty($key);
    }

    /**
     * @param AptoUuid $sectionId
     * @param string $key
     * @return Product
     */
    public function removeSectionCustomProperty(AptoUuid $sectionId, string $key): Product
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return $this;
        }

        // remove custom property from section
        $section->removeCustomProperty($key);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $key
     * @param string $value
     * @param bool $translatable
     * @return Product
     * @throws AptoCustomPropertyException
     */
    public function addElementCustomProperty(AptoUuid $sectionId, AptoUuid $elementId, string $key, string $value, bool $translatable = false): Product
    {
        // @todo we should use a value object for $key|$value pair
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // add new custom property to element
        $element->setCustomProperty($key, $value, $translatable);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $key
     * @return string|null
     */
    public function getElementCustomProperty(AptoUuid $sectionId, AptoUuid $elementId, string $key): ?string
    {
        // @todo we should use a value object for $key|$value pair
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        // return custom property from element
        return $element->getCustomProperty($key);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $key
     * @return Product
     */
    public function removeElementCustomProperty(AptoUuid $sectionId, AptoUuid $elementId, string $key): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // remove custom property from element
        $element->removeCustomProperty($key);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param Money $aptoPrice
     * @param AptoUuid $customerGroupId
     * @return Product
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function addElementPrice(AptoUuid $sectionId, AptoUuid $elementId, Money $aptoPrice, AptoUuid $customerGroupId, ?AptoUuid $productConditionId = null): Product
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // add new price to element
        $element->addAptoPrice($aptoPrice, $customerGroupId, $productConditionId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $formula
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return Product
     * @throws AptoPriceFormulaDuplicateException
     * @throws InvalidUuidException
     */
    public function addElementPriceFormula(AptoUuid $sectionId, AptoUuid $elementId, string $formula, Currency $currency, AptoUuid $customerGroupId, ?AptoUuid $productConditionId): Product
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // add new price to element
        $element->addAptoPriceFormula($formula, $currency, $customerGroupId, $productConditionId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return Element|null
     */
    public function getElement(AptoUuid $sectionId, AptoUuid $elementId)
    {
        // if section does not exists anymore we have nothing to do
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return null;
        }

        // if element does not exists anymore we have nothing to do
        $element = $section->getElement($elementId);
        if (null === $element) {
            return null;
        }

        return $element;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return Identifier|null
     */
    public function getElementIdentifier(AptoUuid $sectionId, AptoUuid $elementId)
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getIdentifier();
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return AptoTranslatedValue|null
     */
    public function getElementName(AptoUuid $sectionId, AptoUuid $elementId)
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getName();
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return AptoTranslatedValue|null
     */
    public function getElementDescription(AptoUuid $sectionId, AptoUuid $elementId)
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getDescription();
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return AptoTranslatedValue|null
     */
    public function getElementErrorMessage(AptoUuid $sectionId, AptoUuid $elementId)
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getErrorMessage();
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return int|null
     */
    public function getElementPosition(AptoUuid $sectionId, AptoUuid $elementId)
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getPosition();
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return Money|null
     */
    public function getElementPrice(AptoUuid $sectionId, AptoUuid $elementId, Currency $currency, AptoUuid $customerGroupId)
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getAptoPrice($currency, $customerGroupId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return AptoUuid|null
     */
    public function getElementPriceId(AptoUuid $sectionId, AptoUuid $elementId, Currency $currency, AptoUuid $customerGroupId)
    {
	    // if element does not exists anymore we have nothing to do
	    $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getAptoPriceId($currency, $customerGroupId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return string|null
     */
    public function getElementPriceFormula(AptoUuid $sectionId, AptoUuid $elementId, Currency $currency, AptoUuid $customerGroupId)
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getAptoPriceFormula($currency, $customerGroupId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return AptoUuid|null
     */
    public function getElementPriceFormulaId(AptoUuid $sectionId, AptoUuid $elementId, Currency $currency, AptoUuid $customerGroupId)
    {
	    // if element does not exist we have nothing to do
	    $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getAptoPriceFormulaId($currency, $customerGroupId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return array
     */
    public function getElementSelectableValues(AptoUuid $sectionId, AptoUuid $elementId): array
    {
        $section = $this->getSection($sectionId);
        if (!$section) {
            throw new \InvalidArgumentException('Product \'' . $this->getId() . '\' does not contain a section with Uuid \'' . $sectionId->getId() . '\'.');
        }

        return $section->getElementSelectableValues($elementId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return array
     */
    public function getElementRenderImages(AptoUuid $sectionId, AptoUuid $elementId): array
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return [];
        }

        return $this->getPublicElementRenderImages($element->getRenderImages());
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $perspective
     * @return array
     */
    public function getElementRenderImagesByPerspective(AptoUuid $sectionId, AptoUuid $elementId, string $perspective): array
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return [];
        }

        $publicRenderImages = $this->getPublicElementRenderImages($element->getRenderImagesByPerspective($perspective));

        if (array_key_exists($perspective, $publicRenderImages)) {
            return $publicRenderImages[$perspective];
        }

        return [];
    }

    /**
     * @param AptoUuid $sectionId
     * @param Identifier $identifier
     * @return AptoUuid|null
     */
    public function getElementIdByIdentifier(AptoUuid $sectionId, Identifier $identifier)
    {
        // if element does not exists anymore we have nothing to do
        $section= $this->getSection($sectionId);
        if (null === $section) {
            return null;
        }

        return $section->getElementIdByIdentifier($identifier);
    }

    /**
     * @param array $renderImages
     * @return array
     */
    protected function getPublicElementRenderImages($renderImages): array
    {
        $publicRenderImages = [];
        /** @var RenderImage $renderImage */
        foreach ($renderImages as $renderImage) {
            $publicRenderImages[$renderImage->getPerspective()][] = [
                'id' => $renderImage->getId(),
                'layer' => $renderImage->getLayer(),
                'mediaFile' => $renderImage->getMediaFile()->getFile(),
                'offsetX' => $renderImage->getOffsetX(),
                'offsetY' => $renderImage->getOffsetY()
            ];
        }
        return $publicRenderImages;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $priceId
     * @return Product
     */
    public function removeElementPrice(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $priceId): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // remove price from element
        $element->removeAptoPrice($priceId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return $this
     */
    public function clearElementPrices(AptoUuid $sectionId, AptoUuid $elementId): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            $element->clearAptoPrices();
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $priceFormulaId
     * @return Product
     */
    public function removeElementPriceFormula(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $priceFormulaId): Product
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // remove price formula from element
        $element->removeAptoPriceFormula($priceFormulaId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return $this
     */
    public function clearElementPriceFormulas(AptoUuid $sectionId, AptoUuid $elementId): Product
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            $element->clearAptoPriceFormulas();
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param float $discount
     * @param AptoUuid $customerGroupId
     * @param AptoTranslatedValue $name
     * @return Product
     * @throws AptoDiscountDuplicateException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function addElementDiscount(AptoUuid $sectionId, AptoUuid $elementId, float $discount, AptoUuid $customerGroupId, AptoTranslatedValue $name): Product
    {
        // if section does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // add new price to section
        $element->addAptoDiscount($discount, $customerGroupId, $name);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $discountId
     * @param float $discount
     * @return Product
     */
    public function setElementDiscountValue(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $discountId, float $discount): Product
    {
        // if section does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        $element->setAptoDiscountValue($discountId, $discount);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $discountId
     * @param AptoUuid $customerGroupId
     * @return Product
     * @throws AptoDiscountDuplicateException
     */
    public function setElementDiscountCustomerGroup(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $discountId, AptoUuid $customerGroupId): Product
    {
        // if section does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        $element->setAptoDiscountCustomerGroupId($discountId, $customerGroupId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $discountId
     * @param AptoTranslatedValue $name
     * @return Product
     */
    public function setElementDiscountName(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $discountId, AptoTranslatedValue $name): Product
    {
        // if section does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        $element->setAptoDiscountName($discountId, $name);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $discountId
     * @return AptoTranslatedValue|null
     */
    public function getElementDiscountName(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $discountId): ?AptoTranslatedValue
    {
        // if section does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return null;
        }

        return $element->getAptoDiscountName($discountId);
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $discountId
     * @param AptoTranslatedValue $description
     * @return Product
     */
    public function setElementDiscountDescription(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $discountId, AptoTranslatedValue $description): Product
    {
        // if section does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        $element->setAptoDiscountDescription($discountId, $description);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $discountId
     * @return Product
     */
    public function removeElementDiscount(AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $discountId): Product
    {
        // if section does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // remove discount from section
        $element->removeAptoDiscount($discountId);

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return $this
     */
    public function clearElementDiscounts(AptoUuid $sectionId, AptoUuid $elementId): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            $element->clearAptoDiscounts();
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return $this
     */
    public function clearElementRenderImages(AptoUuid $sectionId, AptoUuid $elementId): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);

        if (null !== $element) {
            $element->clearRenderImages();
        }

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param bool $isActive
     * @return Product
     */
    public function setElementIsActive(AptoUuid $sectionId, AptoUuid $elementId, bool $isActive): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // if the element we want to set isActive is already in the same state as $isActive we have nothing to do
        if ($element->getIsActive() === $isActive) {
            return $this;
        }

        // update element isActive flag
        $element->setIsActive($isActive);
        $this->publish(
            new ElementIsActiveUpdated($element->getId(), $isActive)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param bool $isMandatory
     * @return Product
     */
    public function setElementIsMandatory(AptoUuid $sectionId, AptoUuid $elementId, bool $isMandatory): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // if the element we want to set isMandatory is already in the same state as $isMandatory we have nothing to do
        if ($element->getIsMandatory() === $isMandatory) {
            return $this;
        }

        // update element isActive flag
        $element->setIsMandatory($isMandatory);
        $this->publish(
            new ElementIsMandatoryUpdated($element->getId(), $isMandatory)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param bool $isNotAvailable
     * @return Product
     */
    public function setElementIsNotAvailable(AptoUuid $sectionId, AptoUuid $elementId, bool $isNotAvailable): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // if the element we want to set isNotAvailable is already in the same state as $isNotAvailable we have nothing to do
        if ($element->getIsNotAvailable() === $isNotAvailable) {
            return $this;
        }

        // update element isActive flag
        $element->setIsNotAvailable($isNotAvailable);
        $this->publish(
            new ElementIsNotAvailableUpdated($element->getId(), $isNotAvailable)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param bool $isZoomable
     * @return $this
     */
    public function setElementIsZoomable(AptoUuid $sectionId, AptoUuid $elementId, bool $isZoomable): Product
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // if the element we want to set isZoomable is already in the same state as $isZoomable we have nothing to do
        if ($element->getIsZoomable() === $isZoomable) {
            return $this;
        }

        // update element isActive flag
        $element->setIsZoomable($isZoomable);
        $this->publish(
            new ElementIsZoomableUpdated($element->getId(), $isZoomable)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ZoomFunction $zoomFunction
     * @return $this
     */
    public function setElementZoomFunction(AptoUuid $sectionId, AptoUuid $elementId, ZoomFunction $zoomFunction): Product
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // if the element we want to set is already in the same state as $zoomFunction we have nothing to do
        if ($zoomFunction->equals($element->getZoomFunction())) {
            return $this;
        }

        // update element isActive flag
        $element->setZoomFunction($zoomFunction);
        $this->publish(
            new ElementZoomFunctionUpdated($element->getId(), $zoomFunction->getValue())
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param bool $openLinksInDialog
     * @return $this
     */
    public function setOpenLinksInDialog(AptoUuid $sectionId, AptoUuid $elementId, bool $openLinksInDialog): Product
    {
        // if element does not exist anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // if the element we want to set $openLinksInDialog is already in the same state as $openLinksInDialog we have nothing to do
        if ($element->getOpenLinksInDialog() === $openLinksInDialog) {
            return $this;
        }

        // update element isActive flag
        $element->setOpenLinksInDialog($openLinksInDialog);
        $this->publish(
            new ElementOpenLinksInDialogUpdated($element->getId(), $openLinksInDialog)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param bool $active
     * @param PriceMatrix|null $priceMatrix
     * @param string|null $row
     * @param string|null $column
     * @return Product
     */
    public function setElementPriceMatrix(AptoUuid $sectionId, AptoUuid $elementId, bool $active, ?PriceMatrix $priceMatrix, ?string $row, ?string $column): Product
    {
        // assert existing element
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // skip if nothing has changed
        $oldPriceMatrixId = $element->getPriceMatrix() ? $element->getPriceMatrix()->getId()->getId() : null;
        $newPriceMatrixId = $priceMatrix ? $priceMatrix->getId()->getId() : null;
        if (
            $element->getPriceMatrixActive() === $active &&
            $oldPriceMatrixId === $newPriceMatrixId &&
            $element->getPriceMatrixRow() === $row &&
            $element->getPriceMatrixColumn() === $column
        ) {
            return $this;
        }

        // update element priceMatrix
        $element->setElementPriceMatrix($active, $priceMatrix, $row, $column);
        $this->publish(
            new ElementPriceMatrixUpdated(
                $element->getId(),
                $active,
                $newPriceMatrixId,
                $row,
                $column
            )
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param bool $extendedPriceCalculationActive
     * @return $this
     */
    public function setElementExtendedPriceCalculationActive(AptoUuid $sectionId, AptoUuid $elementId, bool $extendedPriceCalculationActive): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // if the element we want to set extendedPriceCalculationActive is already in the same state as $extendedPriceCalculationActive we have nothing to do
        if ($element->getExtendedPriceCalculationActive() === $extendedPriceCalculationActive) {
            return $this;
        }

        // update element isActive flag
        $element->setExtendedPriceCalculationActive($extendedPriceCalculationActive);
        $this->publish(
            new ElementExtendedPriceCalculationActiveUpdated($element->getId(), $extendedPriceCalculationActive)
        );

        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $extendedPriceCalculationFormula
     * @return $this
     */
    public function setElementExtendedPriceCalculationFormula(AptoUuid $sectionId, AptoUuid $elementId, string $extendedPriceCalculationFormula): Product
    {
        // if element does not exists anymore we have nothing to do
        $element = $this->getElement($sectionId, $elementId);
        if (null === $element) {
            return $this;
        }

        // if the element we want to set extendedPriceCalculationFormula is already in the same state as $extendedPriceCalculationFormula we have nothing to do
        if ($element->getExtendedPriceCalculationFormula() === $extendedPriceCalculationFormula) {
            return $this;
        }

        // update element isActive flag
        $element->setExtendedPriceCalculationFormula($extendedPriceCalculationFormula);
        $this->publish(
            new ElementExtendedPriceCalculationFormulaUpdated($element->getId(), $extendedPriceCalculationFormula)
        );

        return $this;
    }

    /**
     * @return Collection
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    /**
     * @param string $name
     * @return Product
     * @throws InvalidUuidException
     */
    public function addRule(string $name): Product
    {
        $ruleId = $this->nextRuleId();
        $this->rules->set(
            $ruleId->getId(),
            new Rule($ruleId, $this, $name)
        );
        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param int $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param CriterionOperator $operator
     * @param $value
     * @return $this
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidValueException
     */
    public function addRuleCondition(
        AptoUuid              $ruleId,
        CriterionOperator     $operator,
        int                   $type = 0,
        ?AptoUuid             $sectionId = null,
        ?AptoUuid             $elementId = null,
        ?string               $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string               $value = null
    ): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->addCondition($operator, $type, $sectionId, $elementId, $property, $computedProductValue, $value);
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param int $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param CriterionOperator $operator
     * @param $value
     * @return $this
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidValueException
     */
    public function addRuleImplication(
        AptoUuid              $ruleId,
        CriterionOperator     $operator,
        int                   $type = 0,
        ?AptoUuid             $sectionId = null,
        ?AptoUuid             $elementId = null,
        ?string               $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string               $value = null
    ): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->addImplication($operator, $type, $sectionId, $elementId, $property, $computedProductValue, $value);
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param bool $active
     * @return Product
     */
    public function setRuleActive(AptoUuid $ruleId, bool $active): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->setActive($active);
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param string $name
     * @return Product
     */
    public function setRuleName(AptoUuid $ruleId, string $name): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->setName($name);
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param AptoTranslatedValue $errorMessage
     * @return Product
     */
    public function setRuleErrorMessage(AptoUuid $ruleId, AptoTranslatedValue $errorMessage): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->setErrorMessage($errorMessage);
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @return AptoTranslatedValue|null
     */
    public function getRuleErrorMessage(AptoUuid $ruleId): ?AptoTranslatedValue
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            return $rule->getErrorMessage();
        }

        return null;
    }

    /**
     * @param AptoUuid $ruleId
     * @param int $operator
     * @return Product
     */
    public function setRuleConditionsOperator(AptoUuid $ruleId, int $operator): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->setConditionsOperator($operator);
        }

        return $this;
    }

    /**
     * @param AptoUuid              $ruleId
     * @param AptoUuid              $conditionId
     * @param int                   $type
     * @param CriterionOperator $operator
     * @param string                $value
     * @param AptoUuid|null         $computedValueId
     * @param AptoUuid|null         $sectionId
     * @param AptoUuid|null         $elementId
     * @param string|null           $property
     *
     * @return $this
     */
    public function setRuleCondition(
        AptoUuid          $ruleId,
        AptoUuid          $conditionId,
        int               $type,
        CriterionOperator $operator,
        string            $value,
        AptoUuid          $computedValueId = null,
        AptoUuid          $sectionId = null,
        AptoUuid          $elementId = null,
        string            $property = null,
    ): Product {
        $rule = $this->getRule($ruleId);

        if ($rule === null) {
            return $this;
        }

        // type is required in both cases, so we don't check if it is there or not
        $rule->setConditionType($conditionId, $type);

        if ($type === RuleCriterion::STANDARD_TYPE) {
            $rule->setConditionSectionId($conditionId, $sectionId);
            $rule->setConditionElementId($conditionId,  $elementId);
            $rule->setConditionProperty($conditionId, $property);

            $rule->setConditionComputedValue($conditionId, null);
        }
        else if ($type === RuleCriterion::COMPUTED_VALUE_TYPE) {
            $rule->setConditionComputedValue($conditionId, $this->getComputedProductValue($computedValueId));

            $rule->setConditionSectionId($conditionId, null);
            $rule->setConditionElementId($conditionId,  null);
            $rule->setConditionProperty($conditionId, null);
        }

        // operator is required in both cases
        $rule->setConditionOperator($conditionId, $operator);

        // value can be null, we might want to unset the value
        $rule->setConditionValue($conditionId, $value);

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param int $operator
     * @return Product
     */
    public function setRuleImplicationsOperator(AptoUuid $ruleId, int $operator): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->setImplicationsOperator($operator);
        }

        return $this;
    }

    /**
     * @param AptoUuid              $ruleId
     * @param AptoUuid              $implicationId
     * @param int                   $type
     * @param CriterionOperator $operator
     * @param string                $value
     * @param AptoUuid|null         $computedValueId
     * @param AptoUuid|null         $sectionId
     * @param AptoUuid|null         $elementId
     * @param string|null           $property
     *
     * @return $this
     */
    public function setRuleImplication(
        AptoUuid          $ruleId,
        AptoUuid          $implicationId,
        int               $type,
        CriterionOperator $operator,
        string            $value,
        AptoUuid          $computedValueId = null,
        AptoUuid          $sectionId = null,
        AptoUuid          $elementId = null,
        string            $property = null,
    ): Product {
        $rule = $this->getRule($ruleId);

        if ($rule === null) {
            return $this;
        }

        // type is required in both cases, so we don't check if it is there or not
        $rule->setImplicationType($implicationId, $type);

        if ($type === RuleCriterion::STANDARD_TYPE) {
            $rule->setImplicationSectionId($implicationId, $sectionId);
            $rule->setImplicationElementId($implicationId,  $elementId);
            $rule->setImplicationProperty($implicationId, $property);

            $rule->setImplicationComputedValue($implicationId, null);
        }
        else if ($type === RuleCriterion::COMPUTED_VALUE_TYPE) {
            $rule->setImplicationComputedValue($implicationId, $this->getComputedProductValue($computedValueId));

            $rule->setImplicationSectionId($implicationId, null);
            $rule->setImplicationElementId($implicationId,  null);
            $rule->setImplicationProperty($implicationId, null);
        }

        // operator is required in both cases
        $rule->setImplicationOperator($implicationId, $operator);

        // value can be null, we might want to unset the value
        $rule->setImplicationValue($implicationId, $value);

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param bool $softRule
     * @return Product
     */
    public function setSoftRule(AptoUuid $ruleId, bool $softRule): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->setSoftRule($softRule);
        }

        return $this;
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasRule(AptoUuid $id): bool
    {
        return $this->rules->containsKey($id->getId());
    }

    /**
     * @param AptoUuid $ruleId
     * @return Rule|null
     */
    private function getRule(AptoUuid $ruleId): ?Rule
    {
        if ($this->hasRule($ruleId)) {
            return $this->rules->get($ruleId->getId());
        }

        return null;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getRuleIdsByName(string $name): array
    {
        $ruleIds = [];
        /** @var Rule $rule */
        foreach ($this->rules as $rule) {
            if ($rule->getName() === $name) {
                $ruleIds[] = $rule->getId();
            }
        }
        return $ruleIds;
    }

    /**
     * @param AptoUuid $ruleId
     * @return Product
     */
    public function removeRule(AptoUuid $ruleId): Product
    {
        if ($this->hasRule($ruleId)) {
            $this->rules->remove($ruleId->getId());
        }
        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param AptoUuid $conditionId
     * @return Product
     */
    public function removeRuleCondition(AptoUuid $ruleId, AptoUuid $conditionId): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->removeCondition($conditionId);
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @return Product
     */
    public function removeAllRuleConditions(AptoUuid $ruleId): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->removeAllConditions();
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param AptoUuid $implicationId
     * @return Product
     */
    public function removeRuleImplication(AptoUuid $ruleId, AptoUuid $implicationId): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->removeImplication($implicationId);
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @return Product
     */
    public function removeAllRuleImplications(AptoUuid $ruleId): Product
    {
        $rule = $this->getRule($ruleId);

        if (null !== $rule) {
            $rule->removeAllImplications();
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param string   $description
     *
     * @return $this
     */
    public function setRuleDescription(AptoUuid $ruleId, string $description): Product
    {
        $rule = $this->getRule($ruleId);

        if ($rule !== null) {
            $rule->setDescription($description);
        }

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param int      $position
     *
     * @return $this
     */
    public function setRulePosition(AptoUuid $ruleId, int $position): Product
    {
        $rule = $this->getRule($ruleId);

        if ($rule !== null) {
            $rule->setPosition($position);
        }

        return $this;
    }

    /**
     * @return AptoUuid
     * @throws InvalidUuidException
     */
    public function nextRuleId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @return string
     */
    public function getPriceCalculatorId(): string
    {
        return $this->priceCalculatorId;
    }

    /**
     * @param string $priceCalculatorId
     * @return Product
     */
    public function setPriceCalculatorId(string $priceCalculatorId): Product
    {
        if ($this->priceCalculatorId === $priceCalculatorId) {
            return $this;
        }
        $this->priceCalculatorId = $priceCalculatorId;
        $this->publish(
            new ProductPriceCalculatorIdUpdated(
                $this->getId(),
                $this->getPriceCalculatorId()
            )
        );
        return $this;
    }

    /**
     * @param MediaFile|null $previewImage
     * @return Product
     */
    public function setPreviewImage(MediaFile $previewImage = null): Product
    {
        $this->previewImage = $previewImage;
        return $this;
    }

    /**
     * @return MediaFile|null
     */
    public function getPreviewImage()
    {
        return $this->previewImage;
    }

    /**
     * @return Product
     */
    public function removePreviewImage(): Product
    {
        $this->previewImage = null;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return Product
     */
    public function setPosition(int $position = 0): Product
    {
        if ($this->position === $position) {
            return $this;
        }
        $this->position = $position;
        $this->publish(
            new ProductPositionUpdated(
                $this->getId(),
                $this->getPosition()
            )
        );
        return $this;
    }

    /**
     * @return array
     */
    public function getSectionIds(): array
    {
        $sectionIds = [];
        /** @var Section $section */
        foreach ($this->sections as $section) {
            $sectionIds[] = $section->getId();
        }
        return $sectionIds;
    }

    /**
     * @param AptoUuid $sectionId
     * @return array
     */
    public function getElementIds(AptoUuid $sectionId): array
    {
        $section = $this->getSection($sectionId);
        if (null === $section) {
            return [];
        }
        return $section->getElementIds();
    }

    /**
     * @return Collection
     */
    public function getFilterProperties(): Collection
    {
        return $this->filterProperties;
    }

    /**
     * @param Collection $filterProperties
     * @return Product
     */
    public function setFilterProperties(Collection $filterProperties): Product
    {
        if ($this->filterProperties !== null && !$this->hasCollectionChanged($this->getFilterProperties(), $filterProperties)) {
            return $this;
        }
        $this->filterProperties = $filterProperties;
        $this->publish(
            new ProductFilterPropertiesUpdated(
                $this->getId(),
                $this->getCollectionIds($this->getFilterProperties())
            )
        );
        return $this;
    }

    /**
     * @return Collection
     */
    public function getComputedProductValues(): Collection
    {
        return $this->computedProductValues;
    }

    /**
     * @param AptoUuid $computedValueId
     *
     * @return ComputedProductValue|null
     */
    private function getComputedProductValue(AptoUuid $computedValueId): ?ComputedProductValue
    {
        /** @var ComputedProductValue $value */
        foreach ($this->getComputedProductValues() as $value) {
            if ($value->getId()->getId() === $computedValueId->getId()) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param Collection $computedProductValues
     * @return Product
     */
    public function setComputedProductValues(Collection $computedProductValues): Product
    {
        if ($this->computedProductValues !== null && !$this->hasCollectionChanged($this->getComputedProductValues(), $computedProductValues)) {
            return $this;
        }
        $this->computedProductValues = $computedProductValues;
        $this->publish(
            new ProductComputedProductValuesUpdated(
                $this->getId(),
                $this->getCollectionIds($this->getComputedProductValues())
            )
        );
        return $this;
    }

    /**
     * @param ComputedProductValue $computedProductValue
     * @return $this
     * @throws InvalidComputedValueNameException
     */
    public function addComputedProductValue(ComputedProductValue $computedProductValue): Product
    {
        $this->checkForDuplicateComputedValueNames($computedProductValue);
        $this->computedProductValues->add($computedProductValue);
        return $this;
    }

    /**
     * @param string $id
     * @return Product
     */
    public function removeComputedProductValue(string $id): Product
    {
        foreach ($this->computedProductValues as $computedProductValue) {
            if ($computedProductValue->getId()->getId() === $id) {
                $this->computedProductValues->removeElement($computedProductValue);
            }
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getDomainProperties(): Collection
    {
        return $this->domainProperties;
    }

    /**
     * @param Collection $domainProperties
     * @return $this
     */
    public function setDomainProperties(Collection $domainProperties): Product
    {
        $this->domainProperties = $domainProperties;
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @return Product
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     * @throws ProductShopCountException
     * @throws \Exception
     */
    public function copy(AptoUuid $id): Product
    {
        // set entity mapping
        $entityMapping = new ArrayCollection();

        // set id as identifier and seoUrl to be sure dont break unique rules
        $identifier = $id->getId();
        $seoUrl = $id->getId();
        $articleNumber = $id->getId();

        // create product
        $product = new Product(
            $id,
            new Identifier($identifier),
            $this->getName(),
            $this->getShops()
        );

        $entityMapping->set(
            $this->getId()->getId(), $product
        );

        // set categories
        $product->setCategories(
            $this->getCategories()
        );

        // set customProperties
        $product->customProperties = $this->copyAptoCustomProperties();

        // set prices
        $product->aptoPrices = $this->copyAptoPrices();

        // set discounts
        $product->aptoDiscounts = $this->copyAptoDiscounts();

        // set sections
        $product->sections = $this->copySections($entityMapping);

        // set ComputedProductValues
        $product->computedProductValues = $this->copyComputedProductValues($entityMapping);
        // @todo why has setter been used instead of $product->computedProductValues = ... ?
        //$product->setComputedProductValues($this->copyComputedProductValues($entityMapping));

        // set copy aware element definitions
        $this->copyElementDefinitions($entityMapping);

        // copy render image options
        $this->copyRenderImageOptions($entityMapping);

        // set rules
        $product->rules = $this->copyRules($entityMapping);

        // set properties
        $product
            ->setDescription($this->getDescription())
            ->setActive($this->getActive())
            ->setHidden($this->getHidden())
            ->setUseStepByStep($this->getUseStepByStep())
            ->setKeepSectionOrder($this->getKeepSectionOrder())
            ->setArticleNumber($articleNumber)
            ->setMetaTitle($this->getMetaTitle())
            ->setMetaDescription($this->getMetaDescription())
            ->setStock($this->getStock())
            ->setMinPurchase($this->getMinPurchase())
            ->setMaxPurchase($this->getMaxPurchase())
            ->setDeliveryTime($this->getDeliveryTime())
            ->setWeight($this->getWeight())
            ->setTaxRate($this->getTaxRate())
            ->setSeoUrl($seoUrl)
            ->setPriceCalculatorId($this->getPriceCalculatorId())
            ->setPosition($this->getPosition());

        if ($this->getPreviewImage() instanceof MediaFile) {
            $product->setPreviewImage($this->getPreviewImage());
        }

        // build mapping array for ProductCopied Event
        $entityMappingArray = [];
        foreach ($entityMapping as $key => $value) {
            $entityMappingArray[$key] = $value->getId();
        }

        // publish product copied event
        $product->publish(
            new ProductCopied(
                $product->getId(),
                $this->getId(),
                $entityMappingArray
            )
        );

        // return new product
        return $product;
    }

    /**
     * @param AptoUuid $sectionId
     * @return Product
     * @throws AptoCustomPropertyException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function copySection(AptoUuid $sectionId): Product
    {
        // get section
        $section = $this->getSection($sectionId);
        if ($section === null) {
            return $this;
        }

        // set entity mapping
        $entityMapping = new ArrayCollection();
        $entityMapping->set(
            $this->getId()->getId(), $this
        );

        // copy section
        $newSectionId = $this->nextSectionId();
        $this->sections->set(
            $newSectionId->getId(),
            $section->copy(
                $newSectionId,
                $entityMapping,
                new Identifier($newSectionId->getId())
            )
        );

        // copy render image options
        $this->copyRenderImageOptions($entityMapping);

        // build mapping array for ProductCopied Event
        $entityMappingArray = [];
        foreach ($entityMapping as $key => $value) {
            $entityMappingArray[$key] = $value->getId();
        }

        // publish product section copied event
        $this->publish(
            new ProductSectionCopied(
                $this->getId(),
                $section->getId(),
                $newSectionId,
                $entityMappingArray
            )
        );

        // return product
        return $this;
    }

    /**
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return Product
     * @throws AptoCustomPropertyException
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function copyElement(AptoUuid $sectionId, AptoUuid $elementId): Product
    {
        // get section
        $section = $this->getSection($sectionId);
        if ($section === null) {
            return $this;
        }

        // set entity mapping
        $entityMapping = new ArrayCollection();
        $entityMapping->set(
            $this->getId()->getId(), $this
        );

        // copy element
        $newElementId = $section->copyElement($elementId, $entityMapping);

        // copy render image options
        $this->copyRenderImageOptions($entityMapping);

        // build mapping array for ProductCopied Event
        $entityMappingArray = [];
        foreach ($entityMapping as $key => $value) {
            $entityMappingArray[$key] = $value->getId();
        }

        // publish product section copied event
        $this->publish(
            new ProductElementCopied(
                $this->getId(),
                $sectionId,
                $elementId,
                $newElementId,
                $entityMappingArray
            )
        );

        // return product
        return $this;
    }

    /**
     * @param Collection $entityMapping
     * @return Collection
     * @throws InvalidUuidException
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidValueException
     * @throws CriterionInvalidTypeException
     */
    private function copyRules(Collection &$entityMapping): Collection
    {
        $collection = new ArrayCollection();

        /** @var Rule $rule */
        foreach ($this->rules as $rule) {
            $ruleId = $this->nextRuleId();

            $collection->set(
                $ruleId->getId(),
                $rule->copy($ruleId, $entityMapping)
            );
        }

        return $collection;
    }

    /**
     * @param AptoUuid $ruleId
     *
     * @return $this
     * @throws InvalidUuidException
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    public function copyRule(AptoUuid $ruleId): Product
    {
        $rule = $this->getRule($ruleId);
        if ($rule === null) {
            return $this;
        }

        $newRuleId = $this->nextRuleId();

        $entityMapping = new ArrayCollection();
        $entityMapping->set($this->getId()->getId(), $this);

        foreach ($rule->getConditions() as $condition) {
            $sectionId = $condition->getSectionId();
            $elementId = $condition->getElementId();
            $computedProductValue = $condition->getComputedProductValue();

            if ($sectionId !== null) {
                $entityMapping->set($sectionId->getId(), $this->getSection($condition->getSectionId()));
            }

            if ($elementId !== null) {
                $entityMapping->set($elementId->getId(), $this->getElement($condition->getSectionId(), $condition->getElementId()));
            }

            if ($computedProductValue !== null) {
                $entityMapping->set($computedProductValue->getId()->getId(), $computedProductValue);
            }
        }

        foreach ($rule->getImplications() as $implication) {
            $sectionId = $implication->getSectionId();
            $elementId = $implication->getElementId();
            $computedProductValue = $implication->getComputedProductValue();

            if ($sectionId !== null) {
                $entityMapping->set($sectionId->getId(), $this->getSection($implication->getSectionId()));
            }

            if ($elementId !== null) {
                $entityMapping->set($elementId->getId(), $this->getElement($implication->getSectionId(), $implication->getElementId()));
            }

            if ($computedProductValue !== null) {
                $entityMapping->set($computedProductValue->getId()->getId(), $computedProductValue);
            }
        }

        $copiedRule = $rule->copy($newRuleId, $entityMapping);

        $this->rules->set(
            $newRuleId->getId(),
            $copiedRule
        );

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param AptoUuid $conditionId
     *
     * @return $this
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    public function copyRuleCondition(AptoUuid $ruleId, AptoUuid $conditionId): Product
    {
        $rule = $this->getRule($ruleId);
        if ($rule === null) {
            return $this;
        }

        $condition = $rule->getCondition($conditionId);

        $sectionId = $condition->getSectionId();
        $elementId = $condition->getElementId();
        $computedProductValue = $condition->getComputedProductValue();

        $entityMapping = new ArrayCollection();

        $entityMapping->set($this->getId()->getId(), $this);
        $entityMapping->set($rule->getId()->getId(), $rule);

        if ($sectionId !== null) {
            $entityMapping->set($sectionId->getId(), $this->getSection($condition->getSectionId()));
        }

        if ($elementId !== null) {
            $entityMapping->set($elementId->getId(), $this->getElement($condition->getSectionId(), $condition->getElementId()));
        }

        if ($computedProductValue !== null) {
            $entityMapping->set($computedProductValue->getId()->getId(), $computedProductValue);
        }

        $rule->copyCondition($conditionId, $entityMapping);

        return $this;
    }

    /**
     * @param AptoUuid $ruleId
     * @param AptoUuid $implicationId
     *
     * @return $this
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    public function copyRuleImplication(AptoUuid $ruleId, AptoUuid $implicationId): Product
    {
        $rule = $this->getRule($ruleId);
        if ($rule === null) {
            return $this;
        }

        $implication = $rule->getImplication($implicationId);

        $sectionId = $implication->getSectionId();
        $elementId = $implication->getElementId();
        $computedProductValue = $implication->getComputedProductValue();

        $entityMapping = new ArrayCollection();

        $entityMapping->set($this->getId()->getId(), $this);
        $entityMapping->set($rule->getId()->getId(), $rule);

        if ($sectionId !== null) {
            $entityMapping->set($sectionId->getId(), $this->getSection($implication->getSectionId()));
        }

        if ($elementId !== null) {
            $entityMapping->set($elementId->getId(), $this->getElement($implication->getSectionId(), $implication->getElementId()));
        }

        if ($computedProductValue !== null) {
            $entityMapping->set($computedProductValue->getId()->getId(), $computedProductValue);
        }

        $rule->copyImplication($implicationId, $entityMapping);

        return $this;
    }

    /**
     * @param Collection $entityMapping
     * @return Collection
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     * @throws AptoCustomPropertyException
     */
    private function copySections(Collection &$entityMapping): Collection
    {
        $collection = new ArrayCollection();

        /** @var Section $section */
        foreach ($this->sections as $section) {
            $sectionId = $this->nextSectionId();

            $collection->set(
                $sectionId->getId(),
                $section->copy($sectionId, $entityMapping)
            );
        }

        return $collection;
    }

    /**
     * @param Collection $entityMapping
     * @return Collection
     */
    private function copyComputedProductValues(Collection &$entityMapping): Collection
    {
        $collection = new ArrayCollection();

        /** @var ComputedProductValue $computedProductValue */
        foreach ($this->computedProductValues as $computedProductValue) {
            $computedProductValueId = new AptoUuid();

            $collection->set(
                $computedProductValueId->getId(),
                $computedProductValue->copy($computedProductValueId, $entityMapping)
            );
        }

        return $collection;
    }

    /**
     * @param Collection $entityMapping
     */
    private function copyElementDefinitions(Collection $entityMapping)
    {
        foreach ($entityMapping as $element) {
            if (!($element instanceof Element)) {
                continue;
            }

            $definition = $element->getDefinition();
            if (!($definition instanceof ElementDefinitionCopyAware)) {
                continue;
            }

            $element->setDefinition($definition->copy($entityMapping));
        }
    }

    /**
     * @param Collection $entityMapping
     * @return void
     */
    private function copyRenderImageOptions(Collection $entityMapping)
    {
        foreach ($entityMapping as $element) {
            if (!($element instanceof Element)) {
                continue;
            }

            $element->copyRenderImageOptions($entityMapping);
        }
    }

    /**
     * @param ComputedProductValue $computedProductValue
     * @throws InvalidComputedValueNameException
     */
    private function checkForDuplicateComputedValueNames(ComputedProductValue $computedProductValue)
    {
        foreach ($this->computedProductValues as $value)
        {
            if ($value->getName() === $computedProductValue->getName()) {
                throw new InvalidComputedValueNameException('ComputedProductValue with name ' . $computedProductValue->getName() . ' already exists in current Product');
            }
        }
    }


    /**
     * @param AptoUuid $conditionId
     * @return $this
     */
    public function copyCondition(AptoUuid $conditionId): Product
    {
        $condition = $this->getProductCondition($conditionId);

        if ($condition === null) {
            return $this;
        }

        $entityMapping = new ArrayCollection();
        $entityMapping->set($this->getId()->getId(), $this);

        $sectionId = $condition->getSectionId();
        $elementId = $condition->getElementId();
        $computedProductValue = $condition->getComputedProductValue();

        if ($sectionId !== null) {
            $entityMapping->set($sectionId->getId(), $this->getSection($condition->getSectionId()));
        }

        if ($elementId !== null) {
            $entityMapping->set($elementId->getId(), $this->getElement($condition->getSectionId(), $condition->getElementId()));
        }

        if ($computedProductValue !== null) {
            $entityMapping->set($computedProductValue->getId()->getId(), $computedProductValue);
        }

        $newConditionId = $this->nextConditionId();

        $copiedCondition = $condition->copy($newConditionId, $entityMapping);
        $this->conditions->set($newConditionId->getId(), $copiedCondition);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getProductConditions(): Collection
    {
        return $this->conditions;
    }

    /**
     * @param AptoUuid $conditionId
     * @return Condition|null
     */
    private function getProductCondition(AptoUuid $conditionId): ?Condition
    {
        if ($this->hasProductCondition($conditionId)) {
            return $this->conditions->get($conditionId->getId());
        }

        return null;
    }

    /**
     * @return AptoUuid
     */
    public function nextConditionId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasProductCondition(AptoUuid $id): bool
    {
        return $this->conditions->containsKey($id->getId());
    }

    /**
     * @param Identifier $identifier
     * @param CriterionOperator $operator
     * @param int $type
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @param ComputedProductValue|null $computedProductValue
     * @param string|null $value
     * @return $this
     * @throws CriterionInvalidOperatorException
     * @throws CriterionInvalidPropertyException
     * @throws CriterionInvalidTypeException
     * @throws CriterionInvalidValueException
     */
    public function addProductCondition(
        Identifier            $identifier,
        CriterionOperator     $operator,
        int                   $type = 0,
        ?AptoUuid             $sectionId = null,
        ?AptoUuid             $elementId = null,
        ?string               $property = null,
        ?ComputedProductValue $computedProductValue = null,
        ?string               $value = null
    ): Product {
        $conditionId = $this->nextConditionId();
        $this->conditions->set(
            $conditionId->getId(),
            new Condition(
                $this,
                $conditionId,
                $identifier,
                $operator,
                $type,
                $sectionId,
                $elementId,
                $property,
                $computedProductValue,
                $value,
            )
        );

        return $this;
    }


    /**
     * @param AptoUuid $conditionId
     * @param Identifier $identifier
     * @param int $type
     * @param CriterionOperator $operator
     * @param string $value
     * @param AptoUuid|null $computedValueId
     * @param AptoUuid|null $sectionId
     * @param AptoUuid|null $elementId
     * @param string|null $property
     * @return $this
     */
    public function setProductCondition(
        AptoUuid          $conditionId,
        Identifier        $identifier,
        int               $type,
        AptoUuid          $computedValueId = null,
        AptoUuid          $sectionId = null,
        AptoUuid          $elementId = null,
        string            $property = null,
        CriterionOperator $operator,
        string            $value,
    ): Product {
        $condition = $this->getProductCondition($conditionId);

        if ($condition === null) {
            return $this;
        }

        // identifier is required in both cases, so we don't check if it is there or not
        $condition->setIdentifier($identifier);

        // type is required in both cases, so we don't check if it is there or not
        $condition->setType($type);

        if ($type === Criterion::STANDARD_TYPE) {
            $condition->setSectionId($sectionId);
            $condition->setElementId($elementId);
            $condition->setProperty($property);

            $condition->setComputedProductValue(null);
        }
        else if ($type === Criterion::COMPUTED_VALUE_TYPE) {
            $condition->setComputedProductValue($this->getComputedProductValue($computedValueId));

            $condition->setSectionId(null);
            $condition->setElementId(null);
            $condition->setProperty(null);
        }

        // operator is required in both cases
        $condition->setOperator($operator);

        // value can be null, we might want to unset the value
        $condition->setValue($value);

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @return $this
     */
    public function removeCondition(AptoUuid $conditionId): Product
    {
        if ($this->conditions->containsKey($conditionId->getId())) {
            $this->conditions->remove($conditionId->getId());
        }
        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @param Identifier $identifier
     * @return $this
     */
    public function setProductConditionIdentifier(AptoUuid $conditionId, Identifier $identifier): Product
    {
        $condition = $this->getProductCondition($conditionId);

        if (null !== $condition) {
            $condition->setIdentifier($identifier);
        }

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @param int $type
     * @return $this
     */
    public function setProductConditionType(AptoUuid $conditionId, int $type): Product
    {
        $condition = $this->getProductCondition($conditionId);

        if (null === $condition) {
            return $this;
        }

        $condition->setType($type);

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @param ComputedProductValue|null $computedValue
     * @return $this
     */
    public function setProductConditionComputedValue(AptoUuid $conditionId, ?ComputedProductValue $computedValue): Product
    {
        $condition = $this->getProductCondition($conditionId);

        if (null === $condition) {
            return $this;
        }

        $condition->setComputedProductValue($computedValue);

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @param AptoUuid|null $sectionId
     * @return $this
     */
    public function setProductConditionSectionId(AptoUuid $conditionId, ?AptoUuid $sectionId): Product
    {
        $condition = $this->getProductCondition($conditionId);

        if ($condition === null) {
            return $this;
        }

        $condition->setSectionId($sectionId);

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @param AptoUuid|null $elementId
     * @return $this
     */
    public function setProductConditionElementId(AptoUuid $conditionId, ?AptoUuid $elementId): Product
    {
        $condition = $this->getProductCondition($conditionId);

        if (null === $condition) {
            return $this;
        }

        $condition->setElementId($elementId);

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @param string|null $property
     * @return $this
     */
    public function setProductConditionProperty(AptoUuid $conditionId, ?string $property): Product
    {
        $condition = $this->getProductCondition($conditionId);

        if (null === $condition) {
            return $this;
        }

        $condition->setProperty($property);

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @param CriterionOperator $CriterionOperator
     * @return $this
     */
    public function setProductConditionOperator(AptoUuid $conditionId, CriterionOperator $CriterionOperator): Product
    {
        $condition = $this->getProductCondition($conditionId);

        if (null === $condition) {
            return $this;
        }

        $condition->setOperator($CriterionOperator);

        return $this;
    }

    /**
     * @param AptoUuid $id
     * @param string $value
     * @return $this
     */
    public function setProductConditionValue(AptoUuid $id, string $value): Product
    {
        $condition = $this->getProductCondition($id);

        if (null === $condition) {
            return $this;
        }

        $condition->setValue($value);

        return $this;
    }

    /**
     * @param AptoUuid $conditionId
     * @return $this
     */
    public function removeProductCondition(AptoUuid $conditionId): Product
    {
        if ($this->conditions->containsKey($conditionId->getId())) {
            $this->conditions->remove($conditionId->getId());
        }
        return $this;
    }

    /**
     * @param Identifier $identifier
     * @return array
     */
    public function getProductConditionIdsByIdentifier(Identifier $identifier): array
    {
        $conditionIds = [];

        foreach ($this->conditions as $condition) {
            if ($condition->getIdentifier() === $identifier) {
                $conditionIds[] = $condition->getId();
            }
        }
        return $conditionIds;
    }

    /**
     * @return AptoUuid
     */
    public function nextConditionSetId(): AptoUuid
    {
        return new AptoUuid();
    }

    /**
     * @param Identifier $identifier
     * @return bool
     */
    private function conditionSetIdentifierExists(Identifier $identifier): bool
    {
        /** @var ConditionSet $conditionSet */
        foreach ($this->conditionSets as $conditionSet) {
            if ($conditionSet->getIdentifier()->equals($identifier)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Identifier $identifier
     * @return $this
     * @throws IdentifierUniqueException
     */
    public function addConditionSet(Identifier $identifier): Product
    {
        if ($this->conditionSetIdentifierExists($identifier)) {
            throw new IdentifierUniqueException('ConditionSet Identifier must be unique within a collection!');
        }

        $conditionSetId = $this->nextConditionSetId();

        $this->conditionSets->set(
            $conditionSetId->getId(),
            new ConditionSet($conditionSetId, $this, $identifier)
        );

        return $this;
    }

    /**
     * @param AptoUuid $conditionSetId
     * @param Identifier $newIdentifier
     * @return $this
     * @throws IdentifierUniqueException
     */
    public function setConditionSetIdentifier(AptoUuid $conditionSetId, Identifier $newIdentifier): Product
    {
        $conditionSet = $this->getConditionSet($conditionSetId);

        if (null !== $conditionSet) {
            if (!$conditionSet->getIdentifier()->equals($newIdentifier) && $this->conditionSetIdentifierExists($newIdentifier)) {
                throw new IdentifierUniqueException('ConditionSet Identifier must be unique within a collection!');
            }

            $conditionSet->setIdentifier($newIdentifier);
        }

        return $this;
    }

    /**
     * @param AptoUuid $conditionSetId
     * @param int $operator
     * @return $this
     * @throws CriterionInvalidOperatorException
     */
    public function setConditionSetConditionsOperator(AptoUuid $conditionSetId, int $operator): Product
    {
        $conditionSet = $this->getConditionSet($conditionSetId);

        if (null !== $conditionSet) {
            $conditionSet->setConditionsOperator($operator);
        }

        return $this;
    }

    /**
     * @param AptoUuid $conditionSetId
     * @return $this
     */
    public function removeConditionSet(AptoUuid $conditionSetId): Product
    {
        if ($this->hasConditionSet($conditionSetId)) {
            $this->conditionSets->remove($conditionSetId->getId());
        }
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @return bool
     */
    public function hasConditionSet(AptoUuid $id): bool
    {
        return $this->conditionSets->containsKey($id->getId());
    }

    /**
     * @param AptoUuid $id
     * @return ConditionSet|null
     */
    private function getConditionSet(AptoUuid $id): ?ConditionSet
    {
        if ($this->hasConditionSet($id)) {
            return $this->conditionSets->get($id->getId());
        }

        return null;
    }
}
