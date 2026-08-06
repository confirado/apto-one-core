<?php
namespace Apto\Base\Application\Core\Query\Settings;
use Apto\Base\Application\Core\Query\AptoFinder;

interface SettingsFinder extends AptoFinder
{
    /**
     * @return array|null
     */
    public function findSettings(): ?array;
}
