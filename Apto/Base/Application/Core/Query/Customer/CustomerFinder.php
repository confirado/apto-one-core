<?php

namespace Apto\Base\Application\Core\Query\Customer;

use Apto\Base\Application\Core\Query\AptoFinder;

interface CustomerFinder extends AptoFinder
{
    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id);

    /**
     * @param string $username
     * @return array
     */
    public function findByUsername(string $username);

    /**
     * @param string $email
     * @return array
     */
    public function findByEmail(string $email);

    /**
     * @param string $shopId
     * @param string $externalId
     * @return array
     */
    public function findByShopAndExternalId(string $shopId, string $externalId);

    /**
     * @param string $searchString
     * @return array
     */
    public function findCustomers(string $searchString = ''): array;
}