<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Property;

use Apto\Base\Application\Core\Query\AptoFinder;

interface GroupFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findPropertyById(string $id);

    /**
     * @param string $searchString
     * @return array
     */
    public function findGroups(string $searchString): array;

    /**
     * @param string $id
     * @param string $searchString
     * @return array
     */
    public function findGroupProperties(string $id, string $searchString): array;

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array;

    /**
     * @param string $id
     * @return array
     */
    public function findPropertyCustomProperties(string $id): array;
}