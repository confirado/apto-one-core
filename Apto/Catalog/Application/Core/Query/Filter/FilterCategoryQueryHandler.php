<?php

namespace Apto\Catalog\Application\Core\Query\Filter;

use Apto\Base\Application\Core\QueryHandlerInterface;

class FilterCategoryQueryHandler implements QueryHandlerInterface
{
    /**
     * @var FilterCategoryFinder
     */
    protected $filterCategoryFinder;

    /**
     * FilterCategoryQueryHandler constructor.
     * @param FilterCategoryFinder $filterCategoryFinder
     */
    public function __construct(FilterCategoryFinder $filterCategoryFinder)
    {
        $this->filterCategoryFinder = $filterCategoryFinder;
    }

    /**
     * @param FindFilterCategories $query
     * @return array
     */
    public function handleFindFilterCategories(FindFilterCategories $query)
    {
        return $this->filterCategoryFinder->findFilterCategories($query->getSearchString());
    }

    /**
     * @param FindFilterCategory $query
     * @return array|null
     */
    public function handleFindFilterCategory(FindFilterCategory $query)
    {
        return $this->filterCategoryFinder->findById($query->getId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindFilterCategory::class => [
            'method' => 'handleFindFilterCategory',
            'bus' => 'query_bus'
        ];

        yield FindFilterCategories::class => [
            'method' => 'handleFindFilterCategories',
            'bus' => 'query_bus'
        ];
    }
}