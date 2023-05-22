<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface GuestConfigurationRepository extends AptoRepository
{
    /**
     * @param GuestConfiguration $model
     */
    public function add(GuestConfiguration $model);

    /**
     * @param GuestConfiguration $model
     */
    public function update(GuestConfiguration $model);

    /**
     * @param GuestConfiguration $model
     */
    public function remove(GuestConfiguration $model);

    /**
     * @param $id
     * @return GuestConfiguration|null
     */
    public function findById($id);
}
