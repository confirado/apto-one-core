<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\Query\AptoFinder;

interface ImmutableConfigurationFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @return array
     */
    public function findAll();

    /**
     * @param array $conditions
     * @return mixed
     */
    public function findByCustomPropertyValues(array $conditions);
}
