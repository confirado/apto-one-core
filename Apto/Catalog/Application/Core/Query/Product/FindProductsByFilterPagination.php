<?php

namespace Apto\Catalog\Application\Core\Query\Product;

use Apto\Base\Application\Core\Query\PaginationQuery;
use Apto\Base\Application\Core\PublicQueryInterface;

class FindProductsByFilterPagination extends PaginationQuery implements PublicQueryInterface
{
    /**
     * @var array
     */
    private $filter;

    /**
     * FindProductsByFilterPagination constructor.
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param array $filter
     */
    public function __construct(int $pageNumber = 1, int $recordsPerPage = 50, array $filter = [])
    {
        parent::__construct($pageNumber, $recordsPerPage, $filter['searchString']);
        $this->filter = $filter;
    }
    /**
     * @return array|null
     */
    public function getFilter(): array
    {
        return $this->filter;
    }
}