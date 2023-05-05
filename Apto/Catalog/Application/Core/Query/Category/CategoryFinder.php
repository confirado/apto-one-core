<?php

namespace Apto\Catalog\Application\Core\Query\Category;

use Apto\Base\Application\Core\Query\AptoFinder;

interface CategoryFinder extends AptoFinder
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
    public function findCategories(string $searchString = ''): array;

    /**
     * @param string $searchString
     * @return array
     */
    public function findCategoryTree(string $searchString = ''): array;

    /**
     * @param string $id
     * @return array|null
     */
    public function findCustomProperties(string $id);
}