<?php

namespace Apto\Base\Domain\Frontend\Commands;

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
