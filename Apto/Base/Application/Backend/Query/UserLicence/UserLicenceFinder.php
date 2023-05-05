<?php

namespace Apto\Base\Application\Backend\Query\UserLicence;

use Apto\Base\Application\Core\Query\AptoFinder;

interface UserLicenceFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @return array|null
     */
    public function findCurrent();
}