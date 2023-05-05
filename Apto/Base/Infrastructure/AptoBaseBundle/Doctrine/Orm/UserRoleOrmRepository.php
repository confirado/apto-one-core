<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Backend\Model\UserRole\UserRole;
use Apto\Base\Domain\Backend\Model\UserRole\UserRoleRepository;

class UserRoleOrmRepository extends AptoOrmRepository implements UserRoleRepository
{
    const ENTITY_CLASS = UserRole::class;
    
    /**
     * @param UserRole $model
     */
    public function update(UserRole $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param UserRole $model
     */
    public function add(UserRole $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param UserRole $model
     */
    public function remove(UserRole $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return UserRole|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('UserRole')
            ->where('UserRole.id.id = :id')
            ->setParameter('id', $id);
        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $identifier
     * @return UserRole|null
     */
    public function findOneByIdentifier($identifier)
    {
        $builder = $this->createQueryBuilder('UserRole')
            ->where('UserRole.identifier.identifier = :identifier')
            ->setParameter('identifier', $identifier);

        return $builder->getQuery()->getOneOrNullResult();
    }
}