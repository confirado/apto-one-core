<?php

namespace Apto\Catalog\Application\Core\Query\Product\Rule;

use Apto\Base\Application\Core\Query\AptoFinder;

interface ProductRuleFinder extends AptoFinder
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
    public function findConditions(string $id);

    /**
     * @param string $id
     * @return array|null
     */
    public function findImplications(string $id);
}