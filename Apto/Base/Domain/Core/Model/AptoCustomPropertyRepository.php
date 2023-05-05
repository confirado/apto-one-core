<?php
namespace Apto\Base\Domain\Core\Model;

interface AptoCustomPropertyRepository extends AptoRepository
{
    /**
     * @param AptoCustomProperty $model
     */
    public function update(AptoCustomProperty $model);

    /**
     * @param AptoCustomProperty $model
     */
    public function add(AptoCustomProperty $model);

    /**
     * @param AptoCustomProperty $model
     */
    public function remove(AptoCustomProperty $model);

    /**
     * @param $id
     * @return AptoCustomProperty|null
     */
    public function findById($id);
}