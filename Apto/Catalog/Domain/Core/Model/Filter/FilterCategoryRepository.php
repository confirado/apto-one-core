<?php
namespace Apto\Catalog\Domain\Core\Model\Filter;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface FilterCategoryRepository extends AptoRepository
{
    /**
     * @param FilterCategory $model
     */
    public function update(FilterCategory $model);

    /**
     * @param FilterCategory $model
     */
    public function add(FilterCategory $model);

    /**
     * @param FilterCategory $model
     */
    public function remove(FilterCategory $model);

    /**
     * @param $id
     * @return FilterCategory|null
     */
    public function findById($id);

    /**
     * @param string $identifier
     * @return FilterCategory|null
     */
    public function findByIdentifier(string $identifier);
}