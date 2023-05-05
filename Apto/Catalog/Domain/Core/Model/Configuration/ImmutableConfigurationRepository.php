<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface ImmutableConfigurationRepository extends AptoRepository
{
    /**
     * @param ImmutableConfiguration $model
     */
    public function add(ImmutableConfiguration $model);

    /**
     * @param ImmutableConfiguration $model
     */
    public function update(ImmutableConfiguration $model);

    /**
     * @param ImmutableConfiguration $model
     */
    public function remove(ImmutableConfiguration $model);

    /**
     * @param $id
     * @return ImmutableConfiguration|null
     */
    public function findById($id);

    /**
     * @param array $conditions
     * @return array
     */
    public function findByCustomPropertyValues(array $conditions);
}