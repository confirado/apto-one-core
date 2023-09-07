<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Unit;

interface UnitFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array;
}