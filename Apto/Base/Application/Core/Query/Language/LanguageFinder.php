<?php

namespace Apto\Base\Application\Core\Query\Language;

use Apto\Base\Application\Core\Query\AptoFinder;

interface LanguageFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array
     */
    public function findById(string $id);

    /**
     * @param string $searchString
     * @return array
     */
    public function findLanguages(string $searchString = ''): array;

    /**
     * @return array
     */
    public function findTranslatedValues(): array;
}