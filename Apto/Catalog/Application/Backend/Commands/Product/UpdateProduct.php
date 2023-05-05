<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

class UpdateProduct extends AbstractAddProduct
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $filterPropertyIds;

    /**
     * @var array
     */
    private $domainProperties;

    /**
     * UpdateProduct constructor.
     * @param string $id
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
     * @param array $filterPropertyIds
     * @param array $domainProperties
     */
    public function __construct(
        string $id,
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
        array $filterPropertyIds = [],
        array $domainProperties = []
    )
    {
        parent::__construct(
            $identifier,
            $name,
            $description,
            $shops,
            $categories,
            $active,
            $hidden,
            $useStepByStep,
            $articleNumber,
            $metaTitle,
            $metaDescription,
            $stock,
            $deliveryTime,
            $weight,
            $taxRate,
            $seoUrl,
            $priceCalculatorId,
            $minPurchase,
            $maxPurchase,
            $previewImage,
            $position
        );
        $this->id = $id;
        $this->filterPropertyIds = $filterPropertyIds;
        $this->domainProperties = $domainProperties;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getFilterPropertyIds(): array
    {
        return $this->filterPropertyIds;
    }

    /**
     * @return array
     */
    public function getDomainProperties(): array
    {
        return $this->domainProperties;
    }

}