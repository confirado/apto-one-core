<?php

namespace Apto\Base\Application\Frontend\Commands\FrontendUser;

use Apto\Base\Application\Core\PublicCommandInterface;

class ResetPassword implements PublicCommandInterface
{
    private string $email;

    public function __construct(array $data)
    {
        $this->email = $data['email'];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
