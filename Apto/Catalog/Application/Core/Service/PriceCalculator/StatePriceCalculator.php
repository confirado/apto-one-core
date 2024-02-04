<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Application\Core\Service\TaxCalculator\TaxCalculator;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Money\Currency;

class StatePriceCalculator
{
    /**
     * @var PriceCalculatorRegistry
     */
    private $priceCalculatorRegistry;

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * StatePriceCalculator constructor.
     * @param PriceCalculatorRegistry $priceCalculatorRegistry
     * @param ProductFinder $productFinder
     */
    public function __construct(PriceCalculatorRegistry $priceCalculatorRegistry, ProductFinder $productFinder)
    {
        $this->priceCalculatorRegistry = $priceCalculatorRegistry;
        $this->productFinder = $productFinder;
    }

    /**
     * @param AptoUuid $productId
     * @param array $customerGroup
     * @param Currency $currency
     * @param State $state
     * @param TaxCalculator $taxCalculator
     * @param Currency|null $fallbackCurrency
     * @param float $currencyFactor
     * @return array
     */
    public function getRawPrices(
        AptoUuid $productId,
        array $customerGroup,
        Currency $currency,
        State $state,
        TaxCalculator $taxCalculator,
        Currency $fallbackCurrency = null,
        float $currencyFactor = 1.0
    ): array {
        return $this
            ->getPriceCalculator($productId)
            ->getRawPrices($productId, $customerGroup, $currency, $state, $taxCalculator, $fallbackCurrency, $currencyFactor);
    }

    /**
     * @param AptoUuid $productId
     * @param array $customerGroup
     * @param Currency $currency
     * @param State $state
     * @param TaxCalculator $taxCalculator
     * @param Currency|null $fallbackCurrency
     * @param float $currencyFactor
     * @param array $connectorUser
     * @return array
     */
    public function getDisplayPrices(
        AptoUuid $productId,
        array $customerGroup,
        Currency $currency,
        State $state,
        TaxCalculator $taxCalculator,
        Currency $fallbackCurrency = null,
        float $currencyFactor = 1.0,
        array $connectorUser = []
    ): array {
        return $this
            ->getPriceCalculator($productId)
            ->getDisplayPrices($productId, $customerGroup, $currency, $state, $taxCalculator, $fallbackCurrency, $currencyFactor, $connectorUser);
    }

    /**
     * @param AptoUuid $productId
     * @return PriceCalculator
     */
    private function getPriceCalculator(AptoUuid $productId): PriceCalculator
    {
        $priceCalculatorId = $this->productFinder->findPriceCalculatorIdById($productId->getId());
        if (!$priceCalculatorId) {
            //@todo should we throw an exception or define a default price calculator here?
            $priceCalculatorId = 'Apto\Catalog\Application\Core\Service\PriceCalculator\SimplePriceCalculator';
        }
        return $this->priceCalculatorRegistry->getPriceCalculatorById($priceCalculatorId);
    }
}
