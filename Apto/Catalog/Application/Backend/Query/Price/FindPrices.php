<?php

namespace Apto\Catalog\Application\Backend\Query\Price;

use Apto\Base\Application\Core\QueryInterface;

class FindPrices implements QueryInterface
{
    /**
     * @var array
     */
    private $productIds;

    /**
     * @var array
     */
    private $filter;

    /**
     * FindPrices constructor.
     * @param array $productIds
     * @param array $filter
     */
    public function __construct(array $productIds, array $filter)
    {
        $this->productIds = $productIds;
        $this->filter = $filter;
    }

    /**
     * @return array
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }
}
