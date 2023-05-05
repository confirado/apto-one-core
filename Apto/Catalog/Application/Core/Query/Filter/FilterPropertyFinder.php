<?php

namespace Apto\Catalog\Application\Core\Query\Filter;

use Apto\Base\Application\Core\Query\AptoFinder;

interface FilterPropertyFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $identifier
     * @return array
     */
    public function findByIdentifier(string $identifier);

    /**
     * @param string $searchString
     * @return array
     */
    public function findFilterProperties(string $searchString = ''): array;
}