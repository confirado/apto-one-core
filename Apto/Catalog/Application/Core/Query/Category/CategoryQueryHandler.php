<?php

namespace Apto\Catalog\Application\Core\Query\Category;

use Apto\Base\Application\Core\QueryHandlerInterface;

class CategoryQueryHandler implements QueryHandlerInterface
{
    /**
     * @var CategoryFinder
     */
    protected $categoryFinder;

    /**
     * CategoryQueryHandler constructor.
     * @param CategoryFinder $categoryFinder
     */
    public function __construct(CategoryFinder $categoryFinder)
    {
        $this->categoryFinder = $categoryFinder;
    }

    /**
     * @param FindCategories $query
     * @return array
     */
    public function handleFindCategories(FindCategories $query)
    {
        return $this->categoryFinder->findCategories($query->getSearchString());
    }

    /**
     * @param FindCategoryTree $query
     * @return array
     */
    public function handleFindCategoryTree(FindCategoryTree $query)
    {
        return $this->categoryFinder->findCategoryTree($query->getSearchString());
    }

    /**
     * @param FindCategory $query
     * @return array|null
     */
    public function handleFindCategory(FindCategory $query)
    {
        return $this->categoryFinder->findById($query->getId());
    }

    /**
     * @param FindCategoryCustomProperties $query
     * @return array|null
     */
    public function handleFindCategoryCustomProperties(FindCategoryCustomProperties $query)
    {
        return $this->categoryFinder->findCustomProperties($query->getId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindCategories::class => [
            'method' => 'handleFindCategories',
            'bus' => 'query_bus'
        ];

        yield FindCategoryTree::class => [
            'method' => 'handleFindCategoryTree',
            'bus' => 'query_bus'
        ];

        yield FindCategory::class => [
            'method' => 'handleFindCategory',
            'bus' => 'query_bus'
        ];

        yield FindCategoryCustomProperties::class => [
            'method' => 'handleFindCategoryCustomProperties',
            'bus' => 'query_bus'
        ];
    }
}