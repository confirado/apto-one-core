<?php

namespace Apto\Base\Domain\Core\Model\CustomerGroup;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface CustomerGroupRepository extends AptoRepository
{
    /**
     * @param CustomerGroup $model
     */
    public function add(CustomerGroup $model);

    /**
     * @param CustomerGroup $model
     */
    public function update(CustomerGroup $model);

    /**
     * @param CustomerGroup $model
     */
    public function remove(CustomerGroup $model);

    /**
     * @param string $id
     * @return CustomerGroup|null
     */
    public function findById($id);

    /**
     * @param string $shopId
     * @return array
     */
    public function findFallback(string $shopId): array;

    /**
     * @param string $shopId
     * @param string $excludeId
     * @return array
     */
    public function findFallbackWithExcludeId(string $shopId, string $excludeId): array;

    /**
     * @param string $name
     * @return CustomerGroup|null
     */
    public function findOneByName(string $name);

    /**
     * @param string $shopId
     * @param string $externalId
     * @return CustomerGroup|null
     */
    public function findOneByShopAndExternalId(string $shopId, string $externalId);

    /**
     * @param string $shopId
     * @return array|null
     */
    public function findAllExternalAndUuidsByShopId(string $shopId);
}