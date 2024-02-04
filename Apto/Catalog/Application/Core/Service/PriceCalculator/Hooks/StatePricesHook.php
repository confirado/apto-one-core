<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator\Hooks;

class StatePricesHook
{
    /**
     * @param array $statePrices
     * @param array $connectorUser
     * @return array
     */
    public function getStatePrices(array $statePrices, array $connectorUser): array
    {
        return $statePrices;
    }
}
