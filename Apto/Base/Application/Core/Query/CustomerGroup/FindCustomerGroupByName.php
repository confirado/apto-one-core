<?php

namespace Apto\Base\Application\Core\Query\CustomerGroup;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCustomerGroupByName implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * FindCustomerGroupByName constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}