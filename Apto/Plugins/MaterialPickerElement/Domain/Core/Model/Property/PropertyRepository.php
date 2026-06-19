<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface PropertyRepository extends AptoRepository
{
    /**
     * @param Property $model
     */
    public function update(Property $model);

    /**
     * @param Property $model
     */
    public function add(Property $model);

    /**
     * @param Property $model
     */
    public function remove(Property $model);

    /**
     * @param $id
     * @return Property|null
     */
    public function findById($id);

    /**
     * @param $groupId
     * @return Property[]
     */
    public function getPropertiesByGroupId($groupId);

    /**
     * @return Property[]
     */
    public function getProperties();
    /**
     * @return void
     */
    public function invalidateCache();
}
