<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPoolItemsFiltered implements PublicQueryInterface
{
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
     * @param string $poolId
     * @param array $filter
     * @param string $sortBy
     * @param string $orderBy
     */
    public function __construct(string $poolId, array $filter, string $sortBy = 'clicks', string $orderBy = 'asc')
    {
        $this->poolId = $poolId;
        $this->filter = $filter;
        $this->sortBy = $sortBy;
        $this->orderBy = $orderBy;
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
}
