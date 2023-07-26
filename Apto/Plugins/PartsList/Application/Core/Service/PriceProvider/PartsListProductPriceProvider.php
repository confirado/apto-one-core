<?php
namespace Apto\Plugins\PartsList\Application\Core\Service\PriceProvider;

use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ProductPriceProvider;
use Apto\Plugins\PartsList\Domain\Core\Service\ConfigurationPartsList;
use Money\Money;


class PartsListProductPriceProvider implements ProductPriceProvider
{
    /**
     * @var ConfigurationPartsList
     */
    private $configurationPartsList;

    /**
     * @var ComputedProductValueCalculator
     */
    private $computedProductValueCalculator;

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * PartsListProductPriceProvider constructor.
     * @param ConfigurationPartsList $configurationPartsList
     * @param ProductFinder $productFinder
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     */
    public function __construct(ConfigurationPartsList $configurationPartsList, ProductFinder $productFinder, ComputedProductValueCalculator $computedProductValueCalculator)
    {
        $this->configurationPartsList = $configurationPartsList;
        $this->productFinder = $productFinder;
        $this->computedProductValueCalculator = $computedProductValueCalculator;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return self::class;
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param Money $productPrice
     * @return Money
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function getPrice(PriceCalculator $priceCalculator, Money $productPrice): Money
    {
        $customerGroupId = $priceCalculator->getCustomerGroup()['id'];
        $fallbackCustomerGroup = $priceCalculator->getFallbackCustomerGroupOrNull();
        $fallbackCustomerGroupId = null !== $fallbackCustomerGroup ? $fallbackCustomerGroup['id'] : null;
        $computedValues = $this->computedProductValueCalculator->calculateComputedValues($priceCalculator->getProductId(), $priceCalculator->getState(), true);
        $partsListTotalPrice = $this->configurationPartsList->getTotalPrice($priceCalculator->getProductId(), $priceCalculator->getState(), $priceCalculator->getCurrency(), $customerGroupId, $fallbackCustomerGroupId, $computedValues);
        return $productPrice->add($partsListTotalPrice);
    }
}