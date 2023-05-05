<?php
namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface DomainPropertiesRepository extends AptoRepository
{
    /**
     * @ todo it looks like we don't need an update method anymore, doctrine seems to handle updates automatically because every command is coupled in a transaction, need to to some accurate research here if we can remove this method in every repository
     * @param DomainProperties $model
     */
    public function update(DomainProperties $model);

    /**
     * @param DomainProperties $model
     */
    public function add(DomainProperties $model);

    /**
     * @param DomainProperties $model
     */
    public function remove(DomainProperties $model);

    /**
     * @param $id
     * @return DomainProperties|null
     */
    public function findById($id);

    /**
     * @param null|object|array $entity
     */
    public function flush($entity = null);
}