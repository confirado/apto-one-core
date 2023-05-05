<?php
namespace Apto\Catalog\Domain\Core\Model\Filter;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface FilterPropertyRepository extends AptoRepository
{
    /**
     * @param FilterProperty $model
     */
    public function update(FilterProperty $model);

    /**
     * @param FilterProperty $model
     */
    public function add(FilterProperty $model);

    /**
     * @param FilterProperty $model
     */
    public function remove(FilterProperty $model);

    /**
     * @param $id
     * @return FilterProperty|null
     */
    public function findById($id);

    /**
     * @param string $identifier
     * @return FilterProperty|null
     */
    public function findByIdentifier(string $identifier);
}