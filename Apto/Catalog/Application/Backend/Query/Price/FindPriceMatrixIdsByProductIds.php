<?php

namespace Apto\Catalog\Application\Backend\Query\Price;

use Apto\Base\Application\Core\QueryInterface;

class FindPriceMatrixIdsByProductIds implements QueryInterface
{
    /**
     * @var array
     */
    private $productIds;

    /**
     * FindPriceMatrixIdsByProductIds constructor.
     * @param array $productIds
     */
    public function __construct(array $productIds)
    {
        $this->productIds = $productIds;
    }

    /**
     * @return array
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }
}
