<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\Query\AptoFinder;

interface CodeConfigurationFinder extends AptoFinder
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
    public function findByCode(string $id);
}