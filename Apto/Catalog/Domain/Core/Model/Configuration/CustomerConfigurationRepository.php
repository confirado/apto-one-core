<?php
namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface CustomerConfigurationRepository extends AptoRepository
{
    /**
     * @param CustomerConfiguration $model
     */
    public function update(CustomerConfiguration $model);

    /**
     * @param CustomerConfiguration $model
     */
    public function add(CustomerConfiguration $model);

    /**
     * @param CustomerConfiguration $model
     */
    public function remove(CustomerConfiguration $model);

    /**
     * @param $id
     * @return CustomerConfiguration|null
     */
    public function findById($id);
}