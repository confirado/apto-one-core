<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider;

use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;

interface ProductSurchargeProvider
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param PriceCalculator $priceCalculator
     * @return array
     */
    public function getSurcharges(
        PriceCalculator $priceCalculator
    ): array;
}