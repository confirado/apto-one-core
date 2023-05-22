<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Backend\Model\UserLicence\UserLicence;
use Apto\Base\Domain\Backend\Model\UserLicence\UserLicenceRepository;

class UserLicenceOrmRepository extends AptoOrmRepository implements UserLicenceRepository
{
    const ENTITY_CLASS = UserLicence::class;

    /**
     * @param UserLicence $model
     */
    public function update(UserLicence $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param UserLicence $model
     */
    public function add(UserLicence $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param UserLicence $model
     */
    public function remove(UserLicence $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return UserLicence|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('UserLicence')
            ->where('UserLicence.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return UserLicence|null
     */
    public function findCurrent()
    {
        $builder = $this->createQueryBuilder('UserLicence')
            ->where('UserLicence.validSince <= :now')
            ->orderBy('UserLicence.validSince', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->setMaxResults(1);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
