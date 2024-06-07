<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Part;

interface PartFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id): ?array;

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array;

    /**
     * @param string $id
     * @return array|null
     */
    public function findElementUsageById(string $id): ?array;

    /**
     * @param string $id
     * @return array|null
     */
    public function findRuleUsageById(string $id): ?array;

    /**
     * @param string $id
     * @return array
     */
    public function findProductUsages(string $id): array;

    /**
     * @param string $id
     * @return array
     */
    public function findSectionUsages(string $id): array;

    /**
     * @param string $id
     * @return array
     */
    public function findElementUsages(string $id): array;

    /**
     * @param string $id
     * @return array
     */
    public function findRuleUsages(string $id): array;

    /**
     * @param string $searchString
     * @return array
     */
    public function findProducts(string $searchString): array;

    /**
     * @return array
     */
    public function findProductsSectionsElements(): array;

    /**
     * @param string $searchString
     * @return array
     */
    public function findSections(string $searchString): array;

    /**
     * @param string $searchString
     * @return array
     */
    public function findElements(string $searchString): array;

    /**
     * @param string $id
     * @return array|null
     */
    public function findCustomProperties(string $id): ?array;

    /**
     * @param string $id
     * @return array|null
     */
    public function findPrices(string $id): array;
}
