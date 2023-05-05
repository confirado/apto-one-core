<?php
namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface OrderConfigurationRepository extends AptoRepository
{
    /**
     * @param OrderConfiguration $model
     */
    public function add(OrderConfiguration $model);

    /**
     * @param OrderConfiguration $model
     */
    public function remove(OrderConfiguration $model);

    /**
     * @param $id
     * @return OrderConfiguration|null
     */
    public function findById($id);
}