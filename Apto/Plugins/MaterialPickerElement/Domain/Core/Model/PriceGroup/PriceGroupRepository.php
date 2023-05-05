<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface PriceGroupRepository extends AptoRepository
{
    /**
     * @param PriceGroup $model
     */
    public function update(PriceGroup $model);

    /**
     * @param PriceGroup $model
     */
    public function add(PriceGroup $model);

    /**
     * @param PriceGroup $model
     */
    public function remove(PriceGroup $model);

    /**
     * @param $id
     * @return PriceGroup|null
     */
    public function findById($id);

    /**
     * @param string $internalName
     * @return string|null
     */
    public function findFirstIdByInternalName(string $internalName);
}