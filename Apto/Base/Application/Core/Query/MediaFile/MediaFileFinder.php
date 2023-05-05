<?php

namespace Apto\Base\Application\Core\Query\MediaFile;

use Apto\Base\Application\Core\Query\AptoFinder;

interface MediaFileFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $file
     * @return array|null
     */
    public function findByFile(string $file);
}