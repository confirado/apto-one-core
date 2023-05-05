<?php

namespace Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface PricePerUnitItemRepository extends AptoRepository
{
    /**
     * @param PricePerUnitItem $model
     */
    public function update(PricePerUnitItem $model);

    /**
     * @param PricePerUnitItem $model
     */
    public function add(PricePerUnitItem $model);

    /**
     * @param PricePerUnitItem $model
     */
    public function remove(PricePerUnitItem $model);

    /**
     * @param $id
     * @return PricePerUnitItem|null
     */
    public function findById($id);

    /**
     * @param $id
     * @return array
     */
    public function findByProductId($id);

    /**
     * @param $id
     * @return array
     */
    public function findBySectionId($id);

    /**
     * @param $id
     * @return PricePerUnitItem|null
     */
    public function findByElementId($id);
}