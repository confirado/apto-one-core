<?php

namespace Apto\Base\Domain\Core\Model\Customer;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface CustomerRepository extends AptoRepository
{
    /**
     * @param Customer $model
     */
    public function update(Customer $model);

    /**
     * @param Customer $model
     */
    public function add(Customer $model);

    /**
     * @param Customer $model
     */
    public function remove(Customer $model);

    /**
     * @param string $id
     * @return Customer|null
     */
    public function findById($id);

    /**
     * @param string $username
     * @return Customer|null
     */
    public function findOneByUsername(string $username);

    /**
     * @param string $email
     * @return Customer|null
     */
    public function findOneByEmail(string $email);

    /**
     * @param string $shopId
     * @param string $externalId
     * @return Customer|null
     */
    public function findOneByShopAndExternalId(string $shopId, string $externalId);
}