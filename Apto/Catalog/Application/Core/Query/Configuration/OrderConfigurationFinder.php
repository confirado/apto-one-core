<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\Query\AptoFinder;

interface OrderConfigurationFinder extends AptoFinder
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
    public function findConfigurations(string $searchString = ''): array;
}