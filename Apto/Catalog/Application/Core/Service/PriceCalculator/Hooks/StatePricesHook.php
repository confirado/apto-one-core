<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator\Hooks;

use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;

class StatePricesHook
{
    /**
     * @param PriceCalculator $priceCalculator
     * @param array $statePrices
     * @return array
     */
    public function getStatePrices(PriceCalculator $priceCalculator, array $statePrices): array
    {
        return $statePrices;
    }
}
