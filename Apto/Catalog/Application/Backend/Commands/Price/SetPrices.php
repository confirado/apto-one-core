<?php

namespace Apto\Catalog\Application\Backend\Commands\Price;

use Apto\Base\Application\Core\CommandInterface;

class SetPrices implements CommandInterface
{
    /**
     * @var array
     */
    private $priceItems;

    /**
     * @var float
     */
    private $multiplier;

    /**
     * SetPrices constructor.
     * @param array $priceItems
     * @param float $multiplier
     */
    public function __construct(array $priceItems, float $multiplier)
    {
        $this->priceItems = $priceItems;
        $this->multiplier = $multiplier;
    }

    /**
     * @return array
     */
    public function getPriceItems(): array
    {
        return $this->priceItems;
    }

    /**
     * @return float
     */
    public function getMultiplier(): float
    {
        return $this->multiplier;
    }
}