<?php

namespace Apto\Catalog\Application\Core\Query\Product\Condition;

use Apto\Base\Application\Core\Query\AptoFinder;

interface ProductConditionSetFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param array $ids
     * @return mixed
     */
    public function findByIds(array $ids);

    /**
     * @param string $id
     * @return array|null
     */
    public function findConditions(string $id);
}
