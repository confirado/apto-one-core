<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddProduct implements CommandInterface
{
    /**
     * @var string|null
     */
    private $identifier;

    /**
     * @var array
     */
    private $name;

    /**
     * @var array
     */
    private $description;

    /**
     * @var array
     */
    private $shops;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var bool
     */
    private $hidden;

    /**
     * @var bool
     */
    private $useStepByStep;

    /**
     * @var string
     */
    private $articleNumber;

    /**
     * @var array
     */
    private $metaTitle;

    /**
     * @var array
     */
    private $metaDescription;

    /**
     * @var int
     */
    private $stock;

    /**
     * @var int
     */
    private $minPurchase;

    /**
     * @var int
     */
    private $maxPurchase;

    /**
     * @var string
     */
    private $deliveryTime;

    /**
     * @var float
     */
    private $weight;

    /**
     * @var float
     */
    private $taxRate;

    /**
     * @var string
     */
    private $seoUrl;

    /**
     * @var string
     */
    private $priceCalculatorId;

    /**
     * @var null|string
     */
    private $previewImage;

    /**
     * @var int
     */
    private $position;

    /**
     * @var bool
     */
    private bool $keepSectionOrder;

    /**
     * AddProduct constructor.
     *
     * @param string|null $identifier
     * @param array $name
     * @param array $description
     * @param array $shops
     * @param array $categories
     * @param bool $active
     * @param bool $hidden
     * @param bool $useStepByStep
     * @param string $articleNumber
     * @param array $metaTitle
     * @param array $metaDescription
     * @param int $stock
     * @param string $deliveryTime
     * @param float $weight
     * @param float $taxRate
     * @param string $seoUrl
     * @param string $priceCalculatorId
     * @param int $minPurchase
     * @param int $maxPurchase
     * @param null $previewImage
     * @param int $position
     * @param bool $keepSectionOrder
     */
    public function __construct(
        ?string $identifier,
        array $name,
        array $description,
        array $shops,
        array $categories = [],
        bool $active = false,
        bool $hidden = false,
        bool $useStepByStep = false,
        string $articleNumber = '',
        array $metaTitle = [],
        array $metaDescription = [],
        int $stock = 0,
        string $deliveryTime = '',
        float $weight = 0.0,
        float $taxRate = 0.0,
        string $seoUrl = '',
        string $priceCalculatorId = '',
        int $minPurchase = 0,
        int $maxPurchase = 0,
        $previewImage = null,
        int $position = 0,
        bool $keepSectionOrder = true,
    ) {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->description = $description;
        $this->shops = $shops;
        $this->categories = $categories;
        $this->active = $active;
        $this->hidden = $hidden;
        $this->useStepByStep = $useStepByStep;
        $this->articleNumber = $articleNumber;
        $this->metaTitle = $metaTitle;
        $this->metaDescription = $metaDescription;
        $this->stock = $stock;
        $this->weight = $weight;
        $this->deliveryTime = $deliveryTime;
        $this->taxRate = $taxRate;
        $this->seoUrl = $seoUrl;
        $this->priceCalculatorId = $priceCalculatorId;
        $this->minPurchase = $minPurchase;
        $this->maxPurchase = $maxPurchase;
        $this->previewImage = $previewImage;
        $this->position = $position;
        $this->keepSectionOrder = $keepSectionOrder;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getDescription(): array
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getShops(): array
    {
        return $this->shops;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function getHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @return bool
     */
    public function getUseStepByStep(): bool
    {
        return $this->useStepByStep;
    }

    /**
     * @return string
     */
    public function getArticleNumber(): string
    {
        return $this->articleNumber;
    }

    /**
     * @return array
     */
    public function getMetaTitle(): array
    {
        return $this->metaTitle;
    }

    /**
     * @return array
     */
    public function getMetaDescription(): array
    {
        return $this->metaDescription;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return int
     */
    public function getMinPurchase(): int
    {
        return $this->minPurchase;
    }

    /**
     * @return int
     */
    public function getMaxPurchase(): int
    {
        return $this->maxPurchase;
    }

    /**
     * @return string
     */
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @return string
     */
    public function getSeoUrl(): string
    {
        return $this->seoUrl;
    }

    /**
     * @return string
     */
    public function getPriceCalculatorId(): string
    {
        return $this->priceCalculatorId;
    }

    /**
     * @return null|string
     */
    public function getPreviewImage()
    {
        return $this->previewImage;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function getKeepSectionOrder(): bool
    {
        return $this->keepSectionOrder;
    }
}
