<?php

namespace Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface FloatInputItemRepository extends AptoRepository
{
    /**
     * @param FloatInputItem $model
     */
    public function update(FloatInputItem $model);

    /**
     * @param FloatInputItem $model
     */
    public function add(FloatInputItem $model);

    /**
     * @param FloatInputItem $model
     */
    public function remove(FloatInputItem $model);

    /**
     * @param $id
     * @return FloatInputItem|null
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
     * @return FloatInputItem|null
     */
    public function findByElementId($id);
}