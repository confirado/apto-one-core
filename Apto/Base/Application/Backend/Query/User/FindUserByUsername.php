<?php

namespace Apto\Base\Application\Backend\Query\User;

use Apto\Base\Application\Core\QueryInterface;

class FindUserByUsername implements QueryInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var bool
     */
    private $secure;

    /**
     * FindUserByUsername constructor.
     * @param string $username
     * @param bool $secure
     */
    public function __construct(string $username, bool $secure = true)
    {
        $this->username = $username;
        $this->secure = $secure;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return bool
     */
    public function getSecure(): bool
    {
        return $this->secure;
    }
}