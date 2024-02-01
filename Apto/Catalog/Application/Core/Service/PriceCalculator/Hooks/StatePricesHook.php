<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator\Hooks;

class StatePricesHook
{
    private array $statePrices;

    public function __construct(array $statePrices)
    {
        $this->statePrices = $statePrices;
    }

    /**
     * @return array
     */
    public function getStatePrices(): array
    {
        return $this->statePrices;
    }
}
