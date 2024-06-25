<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Catalog\Application\Core\Query\Product\Condition\ProductConditionSetFinder;
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
    protected $poolFinder;

    /**
     * @var ProductConditionSetFinder
     */
    private ProductConditionSetFinder $productConditionSetFinder;

    /**
     * @param PoolFinder                $poolFinder
     * @param ProductConditionSetFinder $productConditionSetFinder
     */
    public function __construct(PoolFinder $poolFinder, ProductConditionSetFinder $productConditionSetFinder)
    {
        $this->poolFinder = $poolFinder;
        $this->productConditionSetFinder = $productConditionSetFinder;
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
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
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

        $itemsMatchingCondition = [];

        foreach ($items['data'] as $item) {

            if ($item['material']['conditionSets'] && !empty($item['material']['conditionSets'])) {

                $productConditionsResult = $this->productConditionSetFinder->findByIds($item['material']['conditionSets']);

                $counter = 0;
                foreach ($productConditionsResult['data'] as $key => $productCondition) {

                    $criterion = new Condition(
                        new LinkOperator($productCondition['conditionsOperator']),
                        RuleFactory::criteriaFromArray($productCondition['conditions'])
                    );

                    if ($criterion->isFulfilled($state, new RulePayload([]))) {
                        $counter++;
                    }
                }

                if ($counter >= count($productConditionsResult['data'])) {
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
     * @param FindPoolPriceGroups $query
     * @return array
     */
    public function handleFindPoolPriceGroups(FindPoolPriceGroups $query)
    {
        return $this->poolFinder->findPoolPriceGroups($query->getPoolId());
    }

    /**
     * @param FindPoolPropertyGroups $query
     * @return array
     */
    public function handleFindPoolPropertyGroups(FindPoolPropertyGroups $query)
    {
        return $this->poolFinder->findPoolPropertyGroups($query->getPoolId());
    }

    /**
     * @param FindPoolColors $query
     * @return mixed
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
