<?php

namespace Apto\Base\Application\Frontend\Events\FrontendUser;

use Apto\Base\Application\Core\EventInterface;

class ResetPasswordTokenCreated implements EventInterface
{
    private string $email;
    private string $token;

    public function __construct(string $email, string $token)
    {
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
