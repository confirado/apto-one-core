<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\PriceGroup;

use Apto\Base\Application\Core\Query\AptoFinder;

interface PriceGroupFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $searchString
     * @return array
     */
    public function findPriceGroups(string $searchString): array;

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array;
}