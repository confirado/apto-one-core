<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\Auth\PasswordReset;
use Apto\Base\Domain\Core\Model\Auth\PasswordResetRepository;
use Doctrine\ORM\NonUniqueResultException;

class PasswordResetOrmRepository extends AptoOrmRepository implements PasswordResetRepository
{
    const ENTITY_CLASS = PasswordReset::class;

    /**
     * @param string $token
     * @return PasswordReset|null
     * @throws NonUniqueResultException
     */
    public function findOneByToken(string $token): ?PasswordReset
    {
        $builder = $this->createQueryBuilder('PasswordReset')
            ->where('PasswordReset.token = :token')
            ->setParameters([
                'token' => $token,
            ]);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param PasswordReset $passwordReset
     * @return PasswordReset
     */
    public function add(PasswordReset $passwordReset): PasswordReset
    {
        $this->_em->persist($passwordReset);

        return $passwordReset;
    }

    /**
     * @param PasswordReset $passwordReset
     * @return void
     */
    public function remove(PasswordReset $passwordReset): void
    {
        $this->_em->remove($passwordReset);
        $this->_em->flush();
    }
}
