<?php

namespace Apto\Catalog\Application\Core\Query\PriceMatrix;

use Apto\Base\Application\Core\Query\AptoFinder;

interface PriceMatrixFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $id
     * @param float $columnValue
     * @param float $rowValue
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @return array
     */
    public function findNextHigherPriceByColumnRowValue(
        string $id,
        float $columnValue,
        float $rowValue,
        string $customerGroupId,
        string $fallbackCustomerGroupId = null,
        string $currencyCode,
        string $fallbackCurrencyCode
    ): array;

    /**
     * @param string $id
     * @param float $columnValue
     * @param float $rowValue
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @return array
     */
    public function findAdditionalInformationByColumnRowValue(
        string $id,
        float $columnValue,
        float $rowValue,
        string $customerGroupId,
        string $fallbackCustomerGroupId = null,
        string $currencyCode,
        string $fallbackCurrencyCode
    ): array;

    /**
     * @param string $searchString
     * @return array
     */
    public function findPriceMatrices(string $searchString): array;

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
    public function findElements(string $id): array;

    /**
     * @param string $id
     * @param string $elementId
     * @return array
     */
    public function findElementPrices(string $id, string $elementId): array;

    /**
     * @param string $id
     * @param string $elementId
     * @return array
     */
    public function findElementCustomProperties(string $id, string $elementId): array;

    /**
     * @param string $id
     * @param string $currencyCode
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @return array
     */
    public function findRules(string $id, string $currencyCode, string $customerGroupId, string $fallbackCustomerGroupId = null): array;

    /**
     * a lookup table to test values against matrix entries
     *
     * @param string $id
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @return array
     */
    public function findMatrixLookupTable(string $id, string $currencyCode, string $fallbackCurrencyCode, string $customerGroupId, string $fallbackCustomerGroupId = null): array;

    public function findPriceMatricesByIds(array $ids): array;
}
