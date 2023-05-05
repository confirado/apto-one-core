<?php

namespace Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface SelectBoxItemRepository extends AptoRepository
{
    /**
     * @param SelectBoxItem $model
     */
    public function update(SelectBoxItem $model);

    /**
     * @param SelectBoxItem $model
     */
    public function add(SelectBoxItem $model);

    /**
     * @param SelectBoxItem $model
     */
    public function remove(SelectBoxItem $model);

    /**
     * @param $id
     * @return SelectBoxItem|null
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
     * @return array
     */
    public function findByElementId($id);
}