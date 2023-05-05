<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPoolColors implements PublicQueryInterface
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
     * FindPoolItemsFiltered constructor.
     * @param string $poolId
     * @param array $filter
     */
    public function __construct(string $poolId, array $filter)
    {
        $this->poolId = $poolId;
        $this->filter = $filter;
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
}