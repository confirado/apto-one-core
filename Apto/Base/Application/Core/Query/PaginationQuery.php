<?php

namespace Apto\Base\Application\Core\Query;

abstract class PaginationQuery
{
    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var int
     */
    private $recordsPerPage;

    /**
     * @var string
     */
    private $searchString;

    /**
     * PaginationQuery constructor.
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     */
    public function __construct(int $pageNumber = 1, int $recordsPerPage = 50, string $searchString = '')
    {
        $this->pageNumber = $pageNumber;
        $this->recordsPerPage = $recordsPerPage;
        $this->searchString = $searchString;
    }

    /**
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * @return int
     */
    public function getRecordsPerPage(): int
    {
        return $this->recordsPerPage;
    }

    /**
     * @return string
     */
    public function getSearchString(): string
    {
        return $this->searchString;
    }
}