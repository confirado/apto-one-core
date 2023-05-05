<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Group\Group;
use Apto\Catalog\Domain\Core\Model\Group\GroupRepository;
use Apto\Catalog\Domain\Core\Model\Product\IdentifierNullable;

use Doctrine\ORM\ORMException;

class GroupOrmRepository extends AptoOrmRepository implements GroupRepository
{
    const ENTITY_CLASS = Group::class;

    /**
     * @param Group $model
     * @throws ORMException
     */
    public function update(Group $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param Group $model
     * @throws ORMException
     */
    public function add(Group $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param Group $model
     * @throws ORMException
     */
    public function remove(Group $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param $id
     * @return Group|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('GroupModel')
            ->where('GroupModel.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param IdentifierNullable $identifier
     * @return Group|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByIdentifier(IdentifierNullable $identifier)
    {
        $builder = $this->createQueryBuilder('GroupModel')
            ->where('GroupModel.identifier.value = :identifier')
            ->setParameter('identifier', $identifier->getValue());

        return $builder->getQuery()->getOneOrNullResult();
    }
}