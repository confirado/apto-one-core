<?php

namespace Apto\Base\Application\Core\Query\Customer;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCustomer implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindCustomer constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}