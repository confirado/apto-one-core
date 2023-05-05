<?php
namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface PriceMatrixRepository extends AptoRepository
{
    /**
     * @ todo it looks like we don't need an update method anymore, doctrine seems to handle updates automatically because every command is coupled in a transaction, need to to some accurate research here if we can remove this method in every repository
     * @param PriceMatrix $model
     */
    public function update(PriceMatrix $model);

    /**
     * @param PriceMatrix $model
     */
    public function add(PriceMatrix $model);

    /**
     * @param PriceMatrix $model
     */
    public function remove(PriceMatrix $model);

    /**
     * @param $id
     * @return PriceMatrix|null
     */
    public function findById($id);

    /**
     * @param string $name
     * @return string|null
     */
    public function findFirstIdByName(string $name);
}