<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part;

use Apto\Base\Domain\Core\Model\AptoRepository;
use Doctrine\Common\Collections\Collection;

interface PartRepository extends AptoRepository
{
    /**
     * @param Part $model
     */
    public function update(Part $model);

    /**
     * @param Part $model
     */
    public function add(Part $model);

    /**
     * @param Part $model
     */
    public function remove(Part $model);

    /**
     * @param Part $model
     */
    public function flush(Part $model);

    /**
     * @param string $id
     * @return Part|null
     */
    public function findById(string $id);

    /**
     * @param string $name
     * @return Part|null
     */
    public function findByPartNumber(string $name);

    /**
     * @param array $productUsageIds
     * @param array $sectionUsageIds
     * @param array $elementUsageIds
     * @return Collection
     */
    public function findByUsages(array $productUsageIds, array $sectionUsageIds, array $elementUsageIds): Collection;
}