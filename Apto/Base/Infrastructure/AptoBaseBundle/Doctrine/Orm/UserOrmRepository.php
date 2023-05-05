<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Backend\Model\User\User;
use Apto\Base\Domain\Backend\Model\User\UserRepository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\NonUniqueResultException;

class UserOrmRepository extends AptoOrmRepository implements UserRepository
{

    const ENTITY_CLASS = User::class;
    
    /**
     * @param User $model
     * @throws ORMException
     */
    public function update(User $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param User $model
     * @throws ORMException
     */
    public function add(User $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param User $model
     * @throws ORMException
     */
    public function remove(User $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return User|mixed|null
     * @throws NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('User')
            ->where('User.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $username
     * @return User|mixed|null
     * @throws NonUniqueResultException
     */
    public function findOneByUsername($username)
    {
        $builder = $this->createQueryBuilder('User')
            ->where('User.username.username = :username')
            ->setParameter('username', $username);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $email
     * @return User|mixed|null
     * @throws NonUniqueResultException
     */
    public function findOneByEmail($email)
    {
        $builder = $this->createQueryBuilder('User')
            ->where('User.email = :email')
            ->setParameter('email', $email);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $apiKey
     * @return User|mixed|null
     * @throws NonUniqueResultException
     */
    public function findOneByApiKey($apiKey)
    {
        $builder = $this->createQueryBuilder('User')
            ->where('User.apiKey = :apiKey')
            ->setParameter('apiKey', $apiKey);

        return $builder->getQuery()->getOneOrNullResult();
    }
}