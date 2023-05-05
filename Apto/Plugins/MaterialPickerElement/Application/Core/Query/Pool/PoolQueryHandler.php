<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;

class PoolQueryHandler implements QueryHandlerInterface
{
    /**
     * @var PoolFinder
     */
    protected $poolFinder;

    /**
     * PoolQueryHandler constructor.
     * @param PoolFinder $poolFinder
     */
    public function __construct(PoolFinder $poolFinder)
    {
        $this->poolFinder = $poolFinder;
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
        AptoCacheService::setItem($poolCacheKey, $items);
        return $items;
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
