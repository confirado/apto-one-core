<?php
namespace Apto\Catalog\Application\Core\Service\ShopConnector;

class BasketItem
{
    /**
     * @var string
     */
    private $configurationId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $taxRate;

    /**
     * @var array
     */
    private $prices;

    /**
     * @var array
     */
    private $billOfMaterials;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var array
     */
    private $images;

    /**
     * @var array
     */
    private $additionalData;

    /**
     * BasketItem constructor.
     * @param string $configurationId
     * @param string $name
     * @param float $taxRate
     * @param array $prices
     * @param array $billOfMaterials
     * @param array $properties
     * @param array $images
     * @param array $additionalData
     */
    public function __construct(
        string $configurationId,
        string $name,
        float $taxRate,
        array $prices,
        array $billOfMaterials,
        array $properties,
        array $images,
        array $additionalData
    ) {
        $this->configurationId = $configurationId;
        $this->name = $name;
        $this->taxRate = $taxRate;
        $this->prices = $prices;
        $this->billOfMaterials = $billOfMaterials;
        $this->properties = $properties;
        $this->images = $images;
        $this->additionalData = $additionalData;
    }

    /**
     * @return string
     */
    public function getConfigurationId(): string
    {
        return $this->configurationId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @return array
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @return array
     */
    public function getBillOfMaterials(): array
    {
        return $this->billOfMaterials;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return array
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
}