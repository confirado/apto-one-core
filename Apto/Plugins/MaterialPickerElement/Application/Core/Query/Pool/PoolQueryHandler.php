<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Exception\LinkOperatorInvalidValueException;
use Apto\Catalog\Domain\Core\Model\Product\Condition\ConditionSet;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Exception\CacheException;
use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Core\Query\Product\Condition\ProductConditionSetFinder;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Condition;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\LinkOperator;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayload;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\RuleFactory;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class PoolQueryHandler implements QueryHandlerInterface
{
    /**
     * @var PoolFinder
     */
    protected PoolFinder $poolFinder;

    /**
     * @var ProductConditionSetFinder
     */
    private ProductConditionSetFinder $productConditionSetFinder;

    /**
     * @var ComputedProductValueCalculator
     */
    protected ComputedProductValueCalculator $computedProductValueCalculator;

    /**
     * @var RequestStore
     */
    protected RequestStore $requestStore;

    /**
     * @param PoolFinder $poolFinder
     * @param ProductConditionSetFinder $productConditionSetFinder
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     * @param RequestStore $requestStore
     */
    public function __construct(
        PoolFinder $poolFinder,
        ProductConditionSetFinder $productConditionSetFinder,
        ComputedProductValueCalculator $computedProductValueCalculator,
        RequestStore $requestStore,
    ) {
        $this->poolFinder = $poolFinder;
        $this->productConditionSetFinder = $productConditionSetFinder;
        $this->computedProductValueCalculator = $computedProductValueCalculator;
        $this->requestStore = $requestStore;
    }

    /**
     * @param FindPools $query
     * @return array
     */
    public function handleFindPools(FindPools $query)
    {
        return $this->poolFinder->findPools($query->getSearchString());
    }

    /**
     * @param FindPoolsWithoutMaterial $query
     * @return array
     */
    public function handleFindPoolsWithoutMaterial(FindPoolsWithoutMaterial $query)
    {
        return $this->poolFinder->findPoolsWithoutMaterial($query->getMaterialId(), $query->getSearchString());
    }

    /**
     * @param FindPoolsByPage $query
     * @return array
     */
    public function handleFindPoolsByPage(FindPoolsByPage $query)
    {
        return $this->poolFinder->findByListingPageNumber($query->getPageNumber(), $query->getRecordsPerPage(), $query->getSearchString());
    }

    /**
     * @param FindPool $query
     * @return array|null
     */
    public function handleFindPool(FindPool $query)
    {
        return $this->poolFinder->findById($query->getId());
    }

    /**
     * @param FindPoolItems $query
     * @return array
     */
    public function handleFindPoolItems(FindPoolItems $query)
    {
        return $this->poolFinder->findPoolItems($query->getId());
    }

    /**
     * @param FindPoolItemsByMaterial $query
     * @return array
     */
    public function handleFindPoolItemsByMaterial(FindPoolItemsByMaterial $query)
    {
        return $this->poolFinder->findPoolItemsByMaterial($query->getMaterialId());
    }

    /**
     * @param FindNotInPoolMaterials $query
     * @return array
     */
    public function handleFindNotInPoolMaterials(FindNotInPoolMaterials $query)
    {
        return $this->poolFinder->findNotInPoolMaterials($query->getId(), $query->getSearchString());
    }

    /**
     * @param FindPoolItemsFiltered $query
     * @return array[]|mixed
     * @throws InvalidUuidException
     * @throws CircularReferenceException
     * @throws InvalidArgumentException
     * @throws CacheException
     */
    public function handleFindPoolItemsFiltered(FindPoolItemsFiltered $query)
    {
        $poolCacheKey = "PoolItemsFiltered-" . $query->getPoolId() . '-' . md5(serialize($query->getFilter()));
        $items = AptoCacheService::getItem($poolCacheKey);
        if ($items) {
            return $items;
        }
        $items = $this->poolFinder->findPoolItemsFiltered($query->getPoolId(), $query->getFilter(), $query->getSortBy(), $query->getOrderBy());

        $state = new State($query->getState());
        $computedValuesIndexedById = $this->computedProductValueCalculator->calculateComputedValues($query->getProductId(), $state, true);
        $itemsMatchingCondition = [];

        foreach ($items['data'] as $item) {

            if (array_key_exists('conditionSets', $item['material']) && count($item['material']['conditionSets']) > 0) {

                $productConditionsResult = $this->productConditionSetFinder->findByIdsForProduct($query->getProductId(), $item['material']['conditionSets']);

                if ($this->conditionSetsFulfilled($productConditionsResult['data'],
                    $item['material']['conditionsOperator'], $state, $computedValuesIndexedById)) {
                    $itemsMatchingCondition[] = $item;
                }

            } else {
                $itemsMatchingCondition[] = $item;
            }
        }

        AptoCacheService::setItem($poolCacheKey, $itemsMatchingCondition);

        return ['data' => $itemsMatchingCondition];
    }

    /**
     * @param array $conditionSets
     * @param int $operator
     * @param State $state
     * @param array $computedValuesIndexedById
     * @return bool
     * @throws InvalidUuidException
     */
    private function conditionSetsFulfilled(array $conditionSets, int $operator, State $state, array $computedValuesIndexedById): bool
    {
        switch ($operator) {

            // all conditions must be fulfilled
            case ConditionSet::OPERATOR_AND:
            {
                foreach ($conditionSets as $key => $productCondition) {
                    $criteria = new Condition(
                        new LinkOperator($productCondition['conditionsOperator']),
                        RuleFactory::criteriaFromArray($productCondition['conditions'])
                    );

                    if (!$criteria->isFulfilled($state, new RulePayload($computedValuesIndexedById))) {
                        return false;
                    }
                }
                return true;
            }

            // any condition must be fulfilled
            case ConditionSet::OPERATOR_OR:
            {
                foreach ($conditionSets as $key => $productCondition) {
                    $criteria = new Condition(
                        new LinkOperator($productCondition['conditionsOperator']),
                        RuleFactory::criteriaFromArray($productCondition['conditions'])
                    );

                    if ($criteria->isFulfilled($state, new RulePayload($computedValuesIndexedById))) {
                        return true;
                    }
                }
                return false;
            }
            // something went wrong, operator should be valid at this point
            default:
            {
                throw new LinkOperatorInvalidValueException(sprintf(
                    'The given value \'%s\' is not a valid LinkOperator.',
                    $operator
                ));
            }
        }
    }

    /**
     * @param FindPoolPriceGroups $query
     * @return array
     */
    public function handleFindPoolPriceGroups(FindPoolPriceGroups $query)
    {
        $result = $this->poolFinder->findPoolPriceGroups($query->getPoolId());

        // sort pricegroups by name
        $this->sortListByName($result);

        return $result;
    }

    /**
     * @param FindPoolPropertyGroups $query
     * @return array
     */
    public function handleFindPoolPropertyGroups(FindPoolPropertyGroups $query)
    {
        $result = $this->poolFinder->findPoolPropertyGroups($query->getPoolId());

        // sort groups by name
        $this->sortListByName($result);

        // sort properties by name
        foreach ($result as &$propertyGroup) {
            $this->sortListByName($propertyGroup['properties']);
        }

        return $result;
    }

    /**
     * @param FindPoolColors $query
     * @return array|mixed
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function handleFindPoolColors(FindPoolColors $query)
    {
        $cacheId =  "PoolColorItemsFiltered-" . $query->getPoolId() . '-' . md5(serialize($query->getFilter()));
        $colors = AptoCacheService::getItem($cacheId);
        if ($colors) {
            return $colors;
        }
        $colors = $this->poolFinder->findPoolColors($query->getPoolId(), $query->getFilter());
        AptoCacheService::setItem($cacheId, $colors);
        return $colors;
    }

    /**
     * @param array $list
     * @return void
     */
    private function sortListByName(array &$list): void
    {
        $locale = new AptoLocale($this->requestStore->getLocale());
        usort($list, function($val1, $val2) use ($locale) {
            $val1Name = AptoTranslatedValue::fromArray($val1['name'])->getTranslation($locale, new AptoLocale('de_DE'), true)->getValue();
            $val2Name = AptoTranslatedValue::fromArray($val2['name'])->getTranslation($locale, new AptoLocale('de_DE'), true)->getValue();

            $values = [$val1Name, $val2Name];
            sort($values, SORT_NATURAL);

            if ($val1Name === $values[0]) {
                return -1;
            }

            if ($val2Name === $values[0]) {
                return 1;
            }

            return 0;
        });
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindPools::class => [
            'method' => 'handleFindPools',
            'aptoMessageName' => 'FindMaterialPickerPools',
            'bus' => 'query_bus'
        ];

        yield FindPoolsWithoutMaterial::class => [
            'method' => 'handleFindPoolsWithoutMaterial',
            'aptoMessageName' => 'FindMaterialPickerPoolsWithoutMaterial',
            'bus' => 'query_bus'
        ];

        yield FindPoolsByPage::class => [
            'method' => 'handleFindPoolsByPage',
            'aptoMessageName' => 'FindMaterialPickerPoolsByPage',
            'bus' => 'query_bus'
        ];

        yield FindPool::class => [
            'method' => 'handleFindPool',
            'aptoMessageName' => 'FindMaterialPickerPool',
            'bus' => 'query_bus'
        ];

        yield FindPoolItems::class => [
            'method' => 'handleFindPoolItems',
            'aptoMessageName' => 'FindMaterialPickerPoolItems',
            'bus' => 'query_bus'
        ];

        yield FindPoolItemsByMaterial::class => [
            'method' => 'handleFindPoolItemsByMaterial',
            'aptoMessageName' => 'FindMaterialPickerPoolItemsByMaterial',
            'bus' => 'query_bus'
        ];

        yield FindNotInPoolMaterials::class => [
            'method' => 'handleFindNotInPoolMaterials',
            'aptoMessageName' => 'FindMaterialPickerNotInPoolMaterials',
            'bus' => 'query_bus'
        ];

        yield FindPoolItemsFiltered::class => [
            'method' => 'handleFindPoolItemsFiltered',
            'aptoMessageName' => 'FindMaterialPickerPoolItemsFiltered',
            'bus' => 'query_bus'
        ];

        yield FindPoolPriceGroups::class => [
            'method' => 'handleFindPoolPriceGroups',
            'aptoMessageName' => 'FindMaterialPickerPoolPriceGroups',
            'bus' => 'query_bus'
        ];

        yield FindPoolPropertyGroups::class => [
            'method' => 'handleFindPoolPropertyGroups',
            'aptoMessageName' => 'FindMaterialPickerPoolPropertyGroups',
            'bus' => 'query_bus'
        ];

        yield FindPoolColors::class => [
            'method' => 'handleFindPoolColors',
            'aptoMessageName' => 'FindMaterialPickerPoolColors',
            'bus' => 'query_bus'
        ];
    }
}
