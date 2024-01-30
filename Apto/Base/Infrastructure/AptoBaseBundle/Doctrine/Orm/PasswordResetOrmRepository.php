<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\Auth\PasswordReset;
use Apto\Base\Domain\Core\Model\Auth\PasswordResetRepository;

class PasswordResetOrmRepository extends AptoOrmRepository implements PasswordResetRepository
{
    const ENTITY_CLASS = PasswordReset::class;

    public function create(string $email): PasswordReset
    {
        $passwordReset = new PasswordReset($email, rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));

        $this->_em->persist($passwordReset);

        return $passwordReset;
    }

    public function findOneByToken(string $token): ?PasswordReset
    {
        $builder = $this->createQueryBuilder('PasswordReset')
            ->where('PasswordReset.token = :token')
            ->setParameters([
                'token' => $token,
            ]);

        return $builder->getQuery()->getOneOrNullResult();
    }

    public function delete(PasswordReset $passwordReset): void
    {
        $this->_em->remove($passwordReset);
        $this->_em->flush();
    }
}
