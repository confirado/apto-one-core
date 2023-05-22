<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface ProposedConfigurationRepository extends AptoRepository
{
    /**
     * @param ProposedConfiguration $model
     */
    public function add(ProposedConfiguration $model);

    /**
     * @param ProposedConfiguration $model
     */
    public function update(ProposedConfiguration $model);

    /**
     * @param ProposedConfiguration $model
     */
    public function remove(ProposedConfiguration $model);

    /**
     * @param $id
     * @return ProposedConfiguration|null
     */
    public function findById($id);
}
