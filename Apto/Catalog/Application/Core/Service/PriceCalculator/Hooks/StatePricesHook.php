<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator\Hooks;

class StatePricesHook
{
    /**
     * @param array $statePrices
     * @return array
     */
    public function getStatePrices(array $statePrices): array
    {
        return $statePrices;
    }
}
