<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface BasketConfigurationRepository extends AptoRepository
{
    /**
     * @param BasketConfiguration $model
     */
    public function add(BasketConfiguration $model);

    /**
     * @param BasketConfiguration $model
     */
    public function update(BasketConfiguration $model);

    /**
     * @param BasketConfiguration $model
     */
    public function remove(BasketConfiguration $model);

    /**
     * @param $id
     * @return BasketConfiguration|null
     */
    public function findById($id);
}
