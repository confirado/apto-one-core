<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\Query\AptoFinder;

interface SharedConfigurationFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);
}
