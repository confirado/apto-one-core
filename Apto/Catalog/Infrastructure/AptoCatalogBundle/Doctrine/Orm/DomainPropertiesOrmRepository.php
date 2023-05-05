<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Product\DomainProperties;
use Apto\Catalog\Domain\Core\Model\Product\DomainPropertiesRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class DomainPropertiesOrmRepository extends AptoOrmRepository implements DomainPropertiesRepository
{
    const ENTITY_CLASS = DomainProperties::class;

    /**
     * @param DomainProperties $model
     * @return void
     */
    public function update(DomainProperties $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param DomainProperties $model
     * @throws ORMException
     */
    public function add(DomainProperties $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param DomainProperties $model
     * @throws ORMException
     */
    public function remove(DomainProperties $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param $id
     * @return DomainProperties|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('DomainProperties')
            ->where('DomainProperties.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $entity
     * @return void
     */
    public function flush($entity = null)
    {
        $this->_em->flush($entity);
    }
}