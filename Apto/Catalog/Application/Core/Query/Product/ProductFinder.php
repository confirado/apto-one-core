<?php

namespace Apto\Catalog\Application\Core\Query\Product;

use Apto\Base\Application\Core\Query\AptoFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

interface ProductFinder extends AptoFinder
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
    public function findSections(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findSectionsElements(string $id);

    /**
     * @param array $ids
     * @return array
     */
    public function findProductCustomProperties(array $ids);

    /**
     * @param array $ids
     * @return array
     */
    public function findSectionCustomProperties(array $ids);

    /**
     * @param array $ids
     * @return array
     */
    public function findElementCustomProperties(array $ids);

    /**
     * @param string $id
     * @return array|null
     */
    public function findRules(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findProductConditionSets(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findComputedValues(string $id);

    /**
     * @param string $id
     * @return array
     */
    public function findPrices(string $id): array;

    /**
     * @param string $id
     * @return array
     */
    public function findDiscounts(string $id): array;

    /**
     * @param string $id
     * @param bool $withRules
     * @param bool $withComputedValues
     * @param bool $withConditionSets
     * @return mixed
     */
    public function findConfigurableProductById(string $id, bool $withRules = true, bool $withComputedValues = true, bool $withConditionSets = true);

    /**
     * @param string $seoUrl
     * @param bool $withRules
     * @param bool $withComputedValues
     * @param bool $withConditionSets
     * @return mixed
     */
    public function findConfigurableProductBySeoUrl(string $seoUrl, bool $withRules = true, bool $withComputedValues = true, bool $withConditionSets = true);

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array;

    /**
     * @param string $categoryIdentifier
     * @return array
     */
    public function findByCategoryIdentifier(string $categoryIdentifier = null): array;

    /**
     * @param array $filter
     * @param bool $onlyActive
     * @return array
     */
    public function findByFilter(array $filter = [], bool $onlyActive = true): array;

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param array $filter
     * @param bool $onlyActive
     * @return array
     */
    public function findByFilterPagination(int $pageNumber = 1, int $recordsPerPage = 50, array $filter = [], bool $onlyActive = true): array;

    /**
     * @param array $filter
     * @param bool $onlyActive
     * @return array
     */
    public function findProductIdsByFilter(array $filter = [], bool $onlyActive = true): array;

    /**
     * @param string $identifier
     * @return array|null
     */
    public function findProductByIdentifier(string $identifier);

    /**
     * @param string $productIdentifier
     * @return array|null
     */
    public function findProductIdByIdentifier(string $productIdentifier);

    /**
     * @param string $productIdentifier
     * @param string $sectionIdentifier
     * @return array|null
     */
    public function findSectionIdByIdentifier(string $productIdentifier, string $sectionIdentifier);

    /**
     * @param string $productIdentifier
     * @param string $sectionIdentifier
     * @param string $elementIdentifier
     * @return array|null
     */
    public function findElementIdByIdentifier(string $productIdentifier, string $sectionIdentifier, string $elementIdentifier);

    /**
     * @param string $id
     * @param State $state
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @return array
     */
    public function findPricesByState(
        string $id,
        State $state,
        string $currencyCode,
        string $fallbackCurrencyCode,
        string $customerGroupId,
        string $fallbackCustomerGroupId = null,
        string $shopId = null
    );

    /**
     * @param string $id
     * @return float
     */
    public function findTaxRateById(string $id);

    /**
     * @param string $id
     * @return string
     */
    public function findPriceCalculatorIdById(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findCustomProperties(string $id);

    // Translation related Stuff
    /**
     * @return array
     */
    public function findTranslatableProductFields(): array;

    /**
     * @param string $id
     * @return mixed
     */
    public function findTranslatableSectionsElements(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findTranslatableRules(string $id);

    // Price Export/Import related Stuff

    /**
     * @param string $id
     * @return array|null
     */
    public function findProductSectionElementPrices(string $id);

    /**
     * @param array $filter
     * @param bool $activeOnly
     * @return array
     */
    public function findAllProductIdsByCategories(array $filter, bool $activeOnly): array;

    /**
     * @return int
     */
    public function findNextPosition(): int;
}
