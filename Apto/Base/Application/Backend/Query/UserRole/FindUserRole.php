<?php

namespace Apto\Base\Application\Backend\Query\UserRole;

use Apto\Base\Application\Core\QueryInterface;

class FindUserRole implements QueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindShop constructor.
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