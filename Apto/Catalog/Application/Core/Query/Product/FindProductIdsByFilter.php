<?php

namespace Apto\Catalog\Application\Core\Query\Product;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindProductIdsByFilter implements PublicQueryInterface
{
    /**
     * @var array
     */
    private $filter;

    /**
     * FindProductIdsByFilter constructor.
     * @param array $filter
     */
    public function __construct(array $filter = [])
    {
        $this->filter = $filter;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }
}
