<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Base\Application\Core\Query\AptoFinder;

interface PoolFinder extends AptoFinder
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
    public function findPools(string $searchString): array;

    /**
     * @param string $materialId
     * @param string $searchString
     * @return array
     */
    public function findPoolsWithoutMaterial(string $materialId, string $searchString): array;

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array;

    /**
     * @param string $poolId
     * @return array
     */
    public function findPoolItems(string $poolId): array;

    /**
     * @param string $materialId
     * @return array
     */
    public function findPoolItemsByMaterial(string $materialId): array;

    /**
     * @param string $poolId
     * @param string $searchString
     * @return array
     */
    public function findNotInPoolMaterials(string $poolId, string $searchString): array;

    /**
     * @param string $poolId
     * @param array $filter
     * @param string $sortBy
     * @param string $orderBy
     * @return array
     */
    public function findPoolItemsFiltered(string $poolId, array $filter, string $sortBy = 'clicks', string $orderBy = 'asc'): array;

    /**
     * @param string $poolId
     * @return array
     */
    public function findPoolPriceGroups(string $poolId): array;

    /**
     * @param string $poolId
     * @return array
     */
    public function findPoolPropertyGroups(string $poolId): array;

    /**
     * @param string $poolId
     * @param string $materialId
     * @return array|null
     */
    public function findPriceGroup(string $poolId, string $materialId);

    /**
     * @param string $poolId
     * @param array $filter
     * @return array
     */
    public function findPoolColors(string $poolId, array $filter): array;

    /**
     * @param string $poolId
     * @param array $materials
     * @param string $perspective
     * @return array
     */
    public function findRenderImagesByMaterials(string $poolId, array $materials, string $perspective): array;
}
