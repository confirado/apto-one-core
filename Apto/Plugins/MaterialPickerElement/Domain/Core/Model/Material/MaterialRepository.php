<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface MaterialRepository extends AptoRepository
{
    /**
     * @param Material $model
     */
    public function update(Material $model);

    /**
     * @param Material $model
     */
    public function add(Material $model);

    /**
     * @param Material $model
     */
    public function remove(Material $model);

    /**
     * @param $id
     * @return Material|null
     */
    public function findById($id);

    /**
     * @param $identifier
     * @return Material|null
     */
    public function findFirstIdByIdentifier($identifier);

    /**
     * @return void
     */
    public function invalidateCache();
}