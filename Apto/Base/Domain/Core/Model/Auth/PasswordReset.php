<?php

namespace Apto\Base\Domain\Core\Model\Auth;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class PasswordReset extends AptoAggregate
{
    private string $email;
    private string $token;

    public function __construct(AptoUuid $id, string $email, string $token)
    {
        parent::__construct($id);
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
