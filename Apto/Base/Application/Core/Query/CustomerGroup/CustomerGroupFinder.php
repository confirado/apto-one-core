<?php

namespace Apto\Base\Application\Core\Query\CustomerGroup;

use Apto\Base\Application\Core\Query\AptoFinder;

interface CustomerGroupFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $name
     * @return array|null
     */
    public function findByName(string $name);

    /**
     * @param string $shopId
     * @param string $externalId
     * @return array|null
     */
    public function findByShopAndExternalId(string $shopId, string $externalId);

    /**
     * @return array|null
     */
    public function findFallbackCustomerGroup();

    /**
     * @param string $searchString
     * @return array
     */
    public function findCustomerGroups(string $searchString = ''): array;

    /**
     * @param string $shopId
     * @return array|null
     */
    public function findAllExternalAndUuidsByShopId(string $shopId): ?array;
}