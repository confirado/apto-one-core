<?php

namespace Apto\Base\Domain\Core\Model\Auth;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface PasswordResetRepository extends AptoRepository
{
    public function add(PasswordReset $passwordReset): PasswordReset;
    public function findOneByToken(string $token): ?PasswordReset;
    public function remove(PasswordReset $passwordReset): void;
}
