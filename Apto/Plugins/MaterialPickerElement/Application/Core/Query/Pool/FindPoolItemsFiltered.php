<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPoolItemsFiltered implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $productId;

    /**
     * @var string
     */
    private $poolId;

    /**
     * @var array
     */
    private $filter;

    /**
     * @var string
     */
    private $sortBy;

    /**
     * @var string
     */
    private $orderBy;

    /**
     * @var array
     */
    private $state;

    /**
     * @param string $productId
     * @param string $poolId
     * @param array $filter
     * @param array $state
     * @param string $sortBy
     * @param string $orderBy
     */
    public function __construct(string $productId, string $poolId, array $filter, array $state, string $sortBy = 'clicks', string $orderBy = 'asc')
    {
        $this->productId = $productId;
        $this->poolId = $poolId;
        $this->filter = $filter;
        $this->sortBy = $sortBy;
        $this->orderBy = $orderBy;
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getPoolId(): string
    {
        return $this->poolId;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @return string
     */
    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }
}
