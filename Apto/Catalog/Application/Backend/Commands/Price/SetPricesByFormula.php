<?php

namespace Apto\Catalog\Application\Backend\Commands\Price;

use Apto\Base\Application\Core\CommandInterface;

class SetPricesByFormula implements CommandInterface
{

    /**
     * @var array
     */
    private $priceItems;

    /**
     * @var string
     */
    private $formula;

    /**
     * SetPricesByFormula constructor.
     * @param array $priceItems
     * @param string $formula
     */
    public function __construct(array $priceItems, string $formula)
    {
        $this->priceItems = $priceItems;
        $this->formula = $formula;
    }

    /**
     * @return array
     */
    public function getPriceItems(): array
    {
        return $this->priceItems;
    }

    /**
     * @return string
     */
    public function getFormula(): string
    {
        return $this->formula;
    }
}
