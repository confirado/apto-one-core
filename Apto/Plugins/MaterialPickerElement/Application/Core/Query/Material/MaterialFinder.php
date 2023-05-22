<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Material;

use Apto\Base\Application\Core\Query\AptoFinder;

interface MaterialFinder extends AptoFinder
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
    public function findMaterials(string $searchString): array;

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
    public function findPrices(string $id): array;

    /**
     * @param string $id
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @return array
     */
    public function findPrice(string $id, string $customerGroupId, string $fallbackCustomerGroupId = null, string $currencyCode, string $fallbackCurrencyCode): array;

    /**
     * @param string $id
     * @return array
     */
    public function findGalleryImages(string $id): array;

    /**
     * @param string $id
     * @return array
     */
    public function findMaterialProperties(string $id): array;

    /**
     * @param string $materialId
     * @param string $searchString
     * @return array
     */
    public function findNotAssignedMaterialProperties(string $materialId, string $searchString): array;

    /**
     * @param string $id
     * @return array
     */
    public function findColorRatings(string $id): array;

    /**
     * @param string $id
     * @return array|null
     */
    public function findRenderImages(string $id);
}
