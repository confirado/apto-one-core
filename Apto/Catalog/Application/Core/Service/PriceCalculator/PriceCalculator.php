<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Core\Service\TaxCalculator\TaxCalculator;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Money\Currency;
use Money\Money;

/**
 * @todo maybe we have to extend PriceCalculator Interface to easily chain price calculators together
 * Interface PriceCalculator
 * @package Apto\Catalog\Application\Core\Service\PriceCalculator
 */
interface PriceCalculator
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return AptoUuid
     */
    public function getProductId(): AptoUuid;

    /**
     * @return State
     */
    public function getState(): State;

    /**
     * @return Currency
     */
    public function getCurrency(): Currency;

    /**
     * @return Currency
     */
    public function getFallbackCurrency(): Currency;

    /**
     * @return array
     */
    public function getCustomerGroup(): array;

    /**
     * @return array|null
     */
    public function getFallbackCustomerGroupOrNull();

    /**
     * @return TaxCalculator|null
     */
    public function getTaxCalculator(): ?TaxCalculator;

    /**
     * @return array
     */
    public function getElementIdIdentifierMapping(): array;

    /**
     * @param array $prices
     * @return Money
     */
    public function getTaxAdaptedPriceByPreferredCustomerGroup(array $prices): Money;

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
    ): array;

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
    ): array;
}
