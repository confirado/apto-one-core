<?php
namespace Apto\Catalog\Domain\Core\Model\Category;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface CategoryRepository extends AptoRepository
{
    /**
     * @param Category $model
     */
    public function update(Category $model);

    /**
     * @param Category $model
     */
    public function add(Category $model);

    /**
     * @param Category $model
     */
    public function remove(Category $model);

    /**
     * @param $id
     * @return Category|null
     */
    public function findById($id);

    /**
     * @param string $name
     * @return Category|null
     */
    public function findByName(string $name);
}