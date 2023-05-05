<?php

namespace Apto\Base\Application\Core\Query\DomainEvent;

use Apto\Base\Application\Core\Query\PaginationQuery;
use Apto\Base\Application\Core\PublicQueryInterface;

class FindDomainEventLog extends PaginationQuery implements PublicQueryInterface
{
    /**
     * @var string|null
     */
    private $filter;

    /**
     * FindDomainEventLog constructor.
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @param array $filter
     */
    public function __construct(int $pageNumber = 1, int $recordsPerPage = 50, string $searchString = '', array $filter = [])
    {
        parent::__construct($pageNumber, $recordsPerPage, $searchString);
        $this->filter = $filter;
    }

    /**
     * @return null|string
     */
    public function getFilter()
    {
        return $this->filter;
    }
}