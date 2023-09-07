<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Unit;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface UnitRepository extends AptoRepository
{
    /**
     * @param Unit $model
     */
    public function update(Unit $model);

    /**
     * @param Unit $model
     */
    public function add(Unit $model);

    /**
     * @param Unit $model
     */
    public function remove(Unit $model);

    /**
     * @param Unit $model
     */
    public function flush(Unit $model);

    /**
     * @param string $id
     * @return Unit|null
     */
    public function findById(string $id);

    /**
     * @param string $name
     * @return Unit|null
     */
    public function findByName(string $name);
}