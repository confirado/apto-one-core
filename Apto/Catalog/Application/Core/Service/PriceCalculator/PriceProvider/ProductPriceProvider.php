<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider;

use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Money\Money;

interface ProductPriceProvider
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param PriceCalculator $priceCalculator
     * @param Money $productPrice
     * @return Money
     */
    public function getPrice(
        PriceCalculator $priceCalculator,
        Money $productPrice
    ): Money;
}