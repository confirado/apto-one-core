<?php

namespace Apto\Base\Application\Core\Query\Customer;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCustomerByUsername implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * FindCustomerByUsername constructor.
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