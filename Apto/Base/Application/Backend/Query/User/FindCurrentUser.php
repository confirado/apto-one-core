<?php

namespace Apto\Base\Application\Backend\Query\User;

use Apto\Base\Application\Core\QueryInterface;

class FindCurrentUser implements QueryInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * FindCurrentUser constructor.
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}