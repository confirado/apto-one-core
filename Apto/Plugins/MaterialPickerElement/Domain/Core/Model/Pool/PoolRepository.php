<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface PoolRepository extends AptoRepository
{
    /**
     * @param Pool $model
     */
    public function update(Pool $model);

    /**
     * @param Pool $model
     */
    public function add(Pool $model);

    /**
     * @param Pool $model
     */
    public function remove(Pool $model);

    /**
     * @param $id
     * @return Pool|null
     */
    public function findById($id);

    /**
     * @param string $name
     * @return string|null
     */
    public function findFirstIdByName(string $name);

    /**
     * @return void
     */
    public function invalidateCache();
}