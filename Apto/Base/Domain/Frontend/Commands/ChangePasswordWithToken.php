<?php

namespace Apto\Base\Domain\Frontend\Commands;

use Apto\Base\Application\Core\PublicCommandInterface;

class ChangePasswordWithToken implements PublicCommandInterface
{
    private string $token;
    private string $password;
    private string $repeatPassword;

    public function __construct(array $data)
    {
        $this->token = $data['token'];
        $this->password = $data['password'];
        $this->repeatPassword = $data['repeatPassword'];
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getRepeatPassword(): string
    {
        return $this->repeatPassword;
    }
}
