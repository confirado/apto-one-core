<?php

namespace Apto\Base\Application\Core\Query\AptoCustomProperty;

use Apto\Base\Application\Core\Query\AptoFinder;

interface AptoCustomPropertyFinder extends AptoFinder
{
    /**
     * @return array
     */
    public function findUsedKeys();

    /**
     * @return array
     */
    public function findCustomProperties(): array;
}
