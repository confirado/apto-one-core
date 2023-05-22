<?php

namespace Apto\Base\Application\Core\Query\Customer;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCustomerByEmail implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $email;

    /**
     * FindCustomerByEmail constructor.
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
