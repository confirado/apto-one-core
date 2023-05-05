<?php

namespace Apto\Catalog\Application\Core\Query\Shop;

use Apto\Base\Application\Core\Query\AptoFinder;

interface ShopFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $domain
     * @return array|null
     */
    public function findByDomain(string $domain);

    /**
     * @param string $domain
     * @return array|null
     */
    public function findContextByDomain(string $domain);

    /**
     * @param string $searchString
     * @return array
     */
    public function findShops(string $searchString = ''): array;

    /**
     * @param string $domain
     * @return array|null
     */
    public function findConnectorConfigByDomain(string $domain);

    /**
     * @param string $id
     * @return array|null
     */
    public function findCustomProperties(string $id);
}
