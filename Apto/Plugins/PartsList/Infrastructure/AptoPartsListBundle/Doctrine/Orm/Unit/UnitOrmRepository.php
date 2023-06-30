<?php

namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\Doctrine\Orm\Unit;

use Apto\Plugins\PartsList\Domain\Core\Model\Unit\Unit;
use Apto\Plugins\PartsList\Domain\Core\Model\Unit\UnitRepository;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class UnitOrmRepository extends AptoOrmRepository implements UnitRepository
{
    const ENTITY_CLASS = Unit::class;

    /**
     * @param Unit $model
     * @throws ORMException
     */
    public function add(Unit $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param Unit $model
     * @throws ORMException
     */
    public function update(Unit $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param Unit $model
     * @throws ORMException
     */
    public function remove(Unit $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param Unit $model
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush(Unit $model)
    {
        $this->_em->flush($model);
    }

    /**
     * @param string $id
     * @return Unit|mixed|null
     * @throws NonUniqueResultException
     */
    public function findById(string $id)
    {
        $builder = $this->createQueryBuilder('Unit')
            ->where('Unit.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $name
     * @return Unit|mixed|null
     * @throws NonUniqueResultException
     */
    public function findByName(string $name)
    {
        $builder = $this->createQueryBuilder('Unit')
            ->where('Unit.unit = :name')
            ->setParameter('name', $name);

        return $builder->getQuery()->getOneOrNullResult();
    }
}