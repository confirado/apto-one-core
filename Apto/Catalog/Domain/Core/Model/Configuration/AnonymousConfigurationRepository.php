<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface AnonymousConfigurationRepository extends AptoRepository
{
    /**
     * @param AnonymousConfiguration $model
     */
    public function add(AnonymousConfiguration $model);

    /**
     * @param AnonymousConfiguration $model
     */
    public function update(AnonymousConfiguration $model);

    /**
     * @param AnonymousConfiguration $model
     */
    public function remove(AnonymousConfiguration $model);

    /**
     * @param $id
     * @return AnonymousConfiguration|null
     */
    public function findById($id);
}
