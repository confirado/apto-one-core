<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\OrderConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\OrderConfigurationRepository;

class OrderConfigurationOrmRepository extends AptoOrmRepository implements OrderConfigurationRepository
{
    const ENTITY_CLASS = OrderConfiguration::class;

    /**
     * @param OrderConfiguration $model
     */
    public function add(OrderConfiguration $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param OrderConfiguration $model
     */
    public function remove(OrderConfiguration $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return OrderConfiguration|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('OrderConfiguration')
            ->where('OrderConfiguration.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}