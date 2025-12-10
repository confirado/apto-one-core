<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface SharedConfigurationRepository extends AptoRepository
{
    /**
     * @param SharedConfiguration $model
     */
    public function add(SharedConfiguration $model);

    /**
     * @param SharedConfiguration $model
     */
    public function update(SharedConfiguration $model);

    /**
     * @param SharedConfiguration $model
     */
    public function remove(SharedConfiguration $model);

    /**
     * @param $id
     * @return SharedConfiguration|null
     */
    public function findById($id);
}
