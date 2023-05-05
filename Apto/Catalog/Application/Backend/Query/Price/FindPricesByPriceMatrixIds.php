<?php

namespace Apto\Catalog\Application\Backend\Query\Price;

use Apto\Base\Application\Core\QueryInterface;

class FindPricesByPriceMatrixIds implements QueryInterface
{
    /**
     * @var array
     */
    private $priceMatrixIds;

    /**
     * @var array
     */
    private $filter;

    /**
     * FindPrices constructor.
     * @param array $priceMatrixIds
     * @param array $filter
     */
    public function __construct(array $priceMatrixIds, array $filter)
    {
        $this->priceMatrixIds = $priceMatrixIds;
        $this->filter = $filter;
    }

    /**
     * @return array
     */
    public function getPriceMatrixIds(): array
    {
        return $this->priceMatrixIds;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }
}
