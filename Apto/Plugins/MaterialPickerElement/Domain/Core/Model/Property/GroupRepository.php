<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface GroupRepository extends AptoRepository
{
    /**
     * @param Group $model
     */
    public function update(Group $model);

    /**
     * @param Group $model
     */
    public function add(Group $model);

    /**
     * @param Group $model
     */
    public function remove(Group $model);

    /**
     * @param $id
     * @return Group|null
     */
    public function findById($id);

    /**
     * @return void
     */
    public function invalidateCache();
}