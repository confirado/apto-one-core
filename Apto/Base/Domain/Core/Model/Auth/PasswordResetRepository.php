<?php

namespace Apto\Base\Domain\Core\Model\Auth;

interface PasswordResetRepository
{
    public function create(string $email): PasswordReset;
    public function findOneByToken(string $token): ?PasswordReset;
    public function delete(PasswordReset $passwordReset): void;
}
