<?php

namespace Apto\Catalog\Application\Backend\Query\Price;

use Apto\Base\Application\Core\QueryInterface;

class FindPriceMatrixConflicts implements QueryInterface
{
    /**
     * @var array
     */
    private $productIds;

    /**
     * FindPriceMatrixConflicts constructor.
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
