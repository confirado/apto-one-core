<?php

namespace Apto\Base\Application\Core\Query\CustomerGroup;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCustomerGroup implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindCustomerGroup constructor.
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