<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\FrontendUser\FrontendUser;
use Apto\Base\Domain\Core\Model\FrontendUser\FrontendUserRepository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\NonUniqueResultException;

class FrontendUserOrmRepository extends AptoOrmRepository implements FrontendUserRepository
{
    const ENTITY_CLASS = FrontendUser::class;

    /**
     * @param FrontendUser $model
     * @throws ORMException
     */
    public function update(FrontendUser $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param FrontendUser $model
     * @throws ORMException
     */
    public function add(FrontendUser $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param FrontendUser $model
     * @throws ORMException
     */
    public function remove(FrontendUser $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return FrontendUser|mixed|null
     * @throws NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('FrontendUser')
            ->where('FrontendUser.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $username
     * @return FrontendUser|mixed|null
     * @throws NonUniqueResultException
     */
    public function findOneByUsername($username)
    {
        $builder = $this->createQueryBuilder('FrontendUser')
            ->where('FrontendUser.username.username = :username')
            ->setParameter('username', $username);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $email
     * @return FrontendUser|mixed|null
     * @throws NonUniqueResultException
     */
    public function findOneByEmail($email)
    {
        $builder = $this->createQueryBuilder('FrontendUser')
            ->where('FrontendUser.email = :email')
            ->setParameter('email', $email);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
